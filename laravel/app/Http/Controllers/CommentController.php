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
use phpDocumentor\Reflection\Types\Array_;

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

        $ids = array();

        foreach ($notSeenComments as $comment) {
            array_push($ids, ["comment_id" => $comment['comment_id']]);
        }

        return $ids;
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
        // TODO Check vote logic. And remove constraint for user votes count
        if($type == "vote"){
            $this->validate($request, [
                'vote' => 'required',
                'users_id' => 'required'
            ]);

            $comment = Comment::find($commentId);

            /** @noinspection PhpUndefinedFieldInspection */
            $data = [
                "comment_id" => $commentId,
                'vote' => $request->vote,
                'user_id' => $request->users_id,
            ];

            /** @noinspection PhpUndefinedFieldInspection */
            if($request->vote == "up"){

                $vote = $comment->votes;
                $vote++;

                $comment->votes = $vote;
                $comment->save();
            }

            /** @noinspection PhpUndefinedFieldInspection */
            if($request->vote == "down"){

                $vote = $comment->votes;
                $vote--;

                $comment->votes = $vote;
                $comment->save();
            }

            if(CommentVote::create($data)) {
                return "true";
            }
        }
    }

    public function index(Request $request)
    {
        $sortOrder = 'desc';
        $sortField = 'date';

        if ($request->sortOrder == 'asc' || $request->sortOrder == 'desc') {
            $sortOrder = $request->sortOrder;
        }

        if ($request->sortField == 'date' || $request->sortField == 'votes') {
            $sortField = $request->sortField;
        }

        $comments = collect(Comment::all());
        $commentsData = [];

       foreach ($comments as $key) {
           $user = User::find($key->users_id);

           /* Comment with not exists user id. Skip now.*/
           if (!$user) {
               continue;
           }

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
               "date" => $key->created_at->toDateTimeString(),
               "level" => $key->level
           ]);
       }

        return array_values($this->MapRefsToTree($commentsData, $sortField, $sortOrder));
   }

    /**
     * Maps parent-child relations array to tree
     * @param $input array of items with parent->child relations (each child has ref to it parent)
     * @param $sortField 1 for sort
     * @param $sortOrder 2 direction
     * @return array of root items with chields
     */

    //TODO Add param for select sort order
    // Use Entity
    public function MapRefsToTree($input, $sortField, $sortOrder)
    {
        $arr = collect($input)->prepend(array('id' => '0', 'reply_id' => 'root', 'name' => 'a'))->toArray();

        $topLevel = collect($arr)->reject(function ($val, $key) {
            return $val['reply_id'] == 0;
        });

        $new = array();
        foreach ($arr as $a) {
            $new[$a['reply_id']][] = $a;
        }
        $tree = $this->createTree($new, array($arr[0]), $sortField, $sortOrder);

        $isDesc = ($sortOrder == 'desc' ? true : false);

        $treeRoot = collect($tree)->sortBy($sortField, SORT_REGULAR, $isDesc)->first();

        if (!array_key_exists('children', $treeRoot)) {
            return array();
        };

        return $treeRoot['children'];
    }

    /**
     * Recursive builds hierarchy of objects
     *
     * @param $list list of nodes
     * @param $parent parent node
     * @param $sortField 1 for sort
     * @param $sortOrder 2 direction
     * @return child nodes of parent item
     */
    //TODO Add param for select sort order
    private function createTree(&$list, $parent, $sortField, $sortOrder){
        $tree = array();
        foreach ($parent as $k=>$l){
            if(isset($list[$l['id']])){
                $l['children'] = $this->createTree($list, $list[$l['id']], $sortField, $sortOrder);
            }
            $tree[] = $l;
        }

        // return array_values(collect($tree)->sortByDesc('date')->toArray());

        $isDesc = ($sortOrder == 'desc' ? true : false);
        return array_values(collect($tree)->sortBy($sortField, SORT_REGULAR, $isDesc)->toArray());
    }
}
