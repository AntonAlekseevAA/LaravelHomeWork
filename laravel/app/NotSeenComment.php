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
}
