<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** Source: https://www.cloudways.com/blog/comment-system-laravel-vuejs/ */
class Comment extends Model
{
    //

    /**

     * Fillable fields for a course

     *

     * @return array

     */

    protected $fillable = ['comment','votes','spam','reply_id','page_id','users_id'];

    protected $dates = ['created_at', 'updated_at'];

    public function replies()
    {
        return $this->hasMany('App\Comment','id','reply_id');

    }
}
