<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


/** Source: https://www.cloudways.com/blog/comment-system-laravel-vuejs/ */
/** Routes for CommentController */

/** Надо убрать наверно роут, и оставить 1 на статичную страницу. И тогда столбец Page_id в comments тоже не нужен будет */
Route::get('/{pageId}', function($pageId){

    return view('page',['pageId' => $pageId]);

});

Route::get('comments/{pageId}', 'CommentController@index');
