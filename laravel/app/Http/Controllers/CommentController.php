<?php

namespace App\Http\Controllers;

use App\Comment;
use App\CommentVote;
use App\NotSeenComment;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function MongoDB\BSON\toJSON;

/** Source: https://www.cloudways.com/blog/comment-system-laravel-vuejs/ */
class CommentController extends Controller
{
    /* Возможно можно вообще слать сюда Date.now()*/
    /* А если не now(), а new Date, то нужно добавлять в конце date string UTC "yyyy-MM-dd hh:mm:ss UTC"*/

    /* Send from client js as Date.now() */
    // Maybe depricated (used NotSeenCommentsTable)
    public function getNewComments(Request $request) {
        /* js send timestamp with ms*/
        $timeStamp = round($request->timestamp / 1000);
        $dateTime = Carbon::createFromTimestamp($timeStamp)->toDateTimeString();

        $comments = collect(Comment::where('created_at', '>', $dateTime)->get());
        return $comments;
    }

    public function getNotSeenComments(Request $request) {
        $userId = $request->userId;
        $notSeenComments = collect(NotSeenComment::where('user_id', '=', $userId)->get())->toArray();

        return $notSeenComments;
    }

    public function deleteSeenComment(Request $request) {
        $userId = $request->userId;
        $commentId = $request->commentId;

        try {
            NotSeenComment::where('user_id', '=', $userId)->where('comment_id', '=', $commentId)->delete();

            return ['result' => 'true'];
        } catch (Exception $e) {
            return ['result' => 'false', 'error' => 'Error when delete comment'];
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
            'reply_id' => 'filled',
            'page_id' => 'filled',
            'users_id' => 'required',
        ]);

        $newComment = null;

        DB::transaction(function() use ($request, &$newComment) {
            $replyId = $request['reply_id'];
            $parentComment = collect(Comment::where('id', '=', $replyId)->get())->first(); //Take first to determine level. Other row has some level.

            $newLevel = 0;

            if ($parentComment) {
                $newLevel = $parentComment->level;
            } else {
                $newLevel = -1; // Crack
            }

            $newComment = new Comment($request->all());

            if ($newLevel < 5) {
                $newComment->level = $newLevel + 1;
            } else {
                $newComment->level = $parentComment->level;
                $newComment->reply_id = $parentComment->getReplyId();
            }

            $newComment->save();

            $otherUsers = collect(User::select('id')->where('id', '!=', $newComment->usersId())->get())->toArray();

            foreach ($otherUsers as $otherUser) {
                $notSeenComment = new NotSeenComment;
                $notSeenComment->setCommentId($newComment->id);
                $notSeenComment->setUserId($otherUser['id']);

                NotSeenComment::create($notSeenComment->attributesToArray());
            }
        });

        if($newComment) {
            return ["status" => "true", "commentId" => $newComment->id];
        }

        return ["status" => "false", "error" => "database error. Transaction failed"];
    }

    public function update(Request $request, $commentId, $type)
    {
        if($type == "vote"){
            $this->validate($request, [
                'vote' => 'required',
                'users_id' => 'required'
            ]);

            $comments = Comment::find($commentId);

            /** @noinspection PhpUndefinedFieldInspection */
            $data = [
                "comment_id" => $commentId,
                'vote' => $request->vote,
                'user_id' => $request->users_id,
            ];

            /** @noinspection PhpUndefinedFieldInspection */
            if($request->vote == "up"){
                $comment = $comments->first();

                $vote = $comment->votes;
                $vote++;

                $comments->votes = $vote;
                $comments->save();
            }

            /** @noinspection PhpUndefinedFieldInspection */
            if($request->vote == "down"){
                $comment = $comments->first();

                $vote = $comment->votes;
                $vote--;

                $comments->votes = $vote;
                $comments->save();
            }

            if(CommentVote::create($data)) {
                return "true";
            }
        }
    }

    public function index($pageId)
    {
        $comments = collect(Comment::where('page_id',$pageId)->get());
        $commentsData = [];

       foreach ($comments as $key) {
           $user = User::find($key->users_id);
           $name = $user->name;

           $reply = 0;  // Rework as nested level (1-5)

           $vote = 0;
           $voteStatus = 0;

           if(Auth::user()){
               $voteByUser = CommentVote::where('comment_id',$key->id)->where('user_id',Auth::user()->id)->first();

               if($voteByUser){
                   $vote = 1;
                   $voteStatus = $voteByUser->vote;

               }
           }

           array_push($commentsData,[
               "name" => $name,
               "id" => $key->id,
               "comment" => $key->comment,
               "votes" => $key->votes,
               "reply" => $reply,
               "votedByUser" =>$vote,
               "vote" =>$voteStatus,
               "reply_id"=>$key->getReplyId(),
               "date" => $key->created_at->toDateTimeString()
           ]);
       }

        return $this->MapRefsToTree($commentsData);
   }

    /**
     * Maps parent-child relations array to tree
     * @param $input array of items with parent->child relations (each child has ref to it parent)
     * @return array of root items with chields
     */

    //TODO Add param for select sort order
    // Use Entity
    public function MapRefsToTree($input)
    {
        //TODO Constraint 5 nesting level MAX*
        // $arr = collect(Comment::get())->prepend(array('id' => '0', 'reply_id' => 'root', 'name' => 'a'))->toArray();
        $arr = collect($input)->prepend(array('id' => '0', 'reply_id' => 'root', 'name' => 'a'))->toArray();

        $topLevel = collect($arr)->reject(function ($val, $key) {
            return $val['reply_id'] == 0;
        });

        $new = array();
        foreach ($arr as $a) {
            $new[$a['reply_id']][] = $a;
        }
        $tree = $this->createTree($new, array($arr[0]));
        return collect($tree)->sortByDesc('id')->first()['children'];
    }

    /**
     * Recursive builds hierarchy of objects
     *
     * @param $list list of nodes
     * @param $parent parent node
     * @return child nodes of parent item
     */
    //TODO Add param for select sort order
    private function createTree(&$list, $parent){
        $tree = array();
        foreach ($parent as $k=>$l){
            if(isset($list[$l['id']])){
                $l['children'] = $this->createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }

        return collect($tree)->sortByDesc('id')->toArray();
    }
}
