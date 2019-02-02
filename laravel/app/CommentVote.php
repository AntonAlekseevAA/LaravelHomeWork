<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** Source: https://www.cloudways.com/blog/comment-system-laravel-vuejs/ */
class CommentVote extends Model
{
    //
    protected $fillable = ['comment_id','user_id','vote'];

    protected $table = "comment_user_vote";

    public $timestamps = false;
}
