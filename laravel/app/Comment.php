<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** Source: https://www.cloudways.com/blog/comment-system-laravel-vuejs/ */
class Comment extends Model
{
    protected $fillable = ['comment','votes','reply_id','users_id'];

    protected $dates = ['created_at', 'updated_at'];

    public function replies()
    {
        return $this->hasMany('App\Comment','id','reply_id');
    }

    public function usersId() {
        return $this->attributes['users_id'];
    }

    /* Crack. replyId must return typeof(db_relation). But when builds tree we user array of int*/
    /* Therefore, create property with different name*/
    public function getReplyId() {
        return $this->attributes['reply_id'];
    }

    public function setReplyId($value) {
        $this->attributes['reply_id'] = $value;
    }
}
