<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotSeenComment extends Model
{
    //

    /**

     * Fillable fields for a course

     *

     * @return array

     */

    protected $fillable = ['user_id','comment_id'];

    protected $dates = ['created_at', 'updated_at'];

    public function commentId()
    {
        return $this->hasOne('App\Comment','id','comment_id');
    }

    public function setCommentId($value)
    {
        $this->attributes['comment_id'] = $value;
    }

    public function userId()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function setUserId($value)
    {
        $this->attributes['user_id'] = $value;
    }
}
