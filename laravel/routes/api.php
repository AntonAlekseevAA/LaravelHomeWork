<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/** Source: https://www.cloudways.com/blog/comment-system-laravel-vuejs/ */
/** Api Routes for CommentController */

Route::get('comments', 'CommentController@index');

Route::post('comments', 'CommentController@store');

Route::post('comments/{commentId}/{type}', 'CommentController@update');

Route::post('comments/getNotSeenComments', 'CommentController@getNotSeenComments');

Route::post('comments/setSeen', 'CommentController@deleteSeenComment');
