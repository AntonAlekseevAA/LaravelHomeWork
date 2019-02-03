<?php

namespace App\Http\Controllers;

use App\Comment;
use App\CommentVote;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Source: https://www.cloudways.com/blog/comment-system-laravel-vuejs/ */
class CommentController extends Controller
{
    /* Возможно можно вообще слать сюда Date.now()*/
    /* А если не now(), а new Date, то нужно добавлять в конце date string UTC "yyyy-MM-dd hh:mm:ss UTC"*/
    public function getNewComments(Request $request) {
        /* js send timestamp with ms*/
        $timeStamp = round($request->timestamp / 1000);
        $dateTime = Carbon::createFromTimestamp($timeStamp)->toDateTimeString();

        $comments = collect(Comment::where('created_at', '>', $dateTime)->get());
        return $comments;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
            'reply_id' => 'filled',
            'page_id' => 'filled',
            'users_id' => 'required',
        ]);

        $comment = Comment::create($request->all());

        if($comment) {
            return ["status" => "true", "commentId" => $comment->id];
        }
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
        $exludedNestedComments = collect([]);

       foreach ($comments as $key) {
           $user = User::find($key->users_id);
           $name = $user->name;
           $photo = $user->first()->photo_url;

           $replies = $this->replies($key->id);
           $reply = 0;

           $vote = 0;
           $voteStatus = 0;

           if(Auth::user()){
               $voteByUser = CommentVote::where('comment_id',$key->id)->where('user_id',Auth::user()->id)->first();

               if($voteByUser){
                   $vote = 1;
                   $voteStatus = $voteByUser->vote;

               }
           }

           if($replies != null && sizeof($replies) > 0){
               $reply = 1;

               /** Mark replies and then exclude from result (already added in nested array) */
               $replies->map(function ($item) {
                   return $item['commentid'];
               })->each(function($item) use ($exludedNestedComments) {
                       $exludedNestedComments->push($item);
                   });
           }

           array_push($commentsData,[
               "name" => $name,
               "photo_url" => (string)$photo,
               "commentid" => $key->id,
               "comment" => $key->comment,
               "votes" => $key->votes,
               "reply" => $reply,
               "votedByUser" =>$vote,
               "vote" =>$voteStatus,
               "replies" => $replies,
               "date" => $key->created_at->toDateTimeString()
           ]);
       }

       /**TODO Вложенность выше 1 уровня не работает*/
        $collection = collect($commentsData)->reject(function ($val, $key) use ($exludedNestedComments) {
            $currentId = $val['commentid'];
            return $exludedNestedComments->contains($currentId);
        });

        $result = $collection->sortByDesc('votes');
        return $result;
   }

    protected function replies($commentId)
    {
        $comments = Comment::where('reply_id', $commentId)->get();
        $replies = [];

        foreach ($comments as $key) {
            $user = User::find($key->users_id);
            $name = $user->name;
            $photo = $user->first()->photo_url;

            $vote = 0;
            $voteStatus = 0;

            if (Auth::user()) {
                $voteByUser = CommentVote::where('comment_id', $key->id)->where('user_id', Auth::user()->id)->first();

                if ($voteByUser) {
                    $vote = 1;
                    $voteStatus = $voteByUser->vote;
                }

                array_push($replies,[
                    "name" => $name,
                    "photo_url" => $photo,
                    "commentid" => $key->id,
                    "comment" => $key->comment,
                    "votes" => $key->votes,
                    "votedByUser" => $vote,
                    "vote" => $voteStatus,
                    "date" => $key->created_at->toDateTimeString()
                ]);
            }

            $collection = collect($replies);
            return $collection->sortBy('votes');
        }
    }
}
