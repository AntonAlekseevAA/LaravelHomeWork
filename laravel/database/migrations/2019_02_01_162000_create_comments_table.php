<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**  Build comments table */
        Schema::create('comments', function (Blueprint $table) {

            $table->increments('id');

            $table->text('comment');

            $table->integer('votes')->default(0);

            $table->integer('spam')->default(0);

            $table->integer('reply_id')->default(0);

            $table->string('page_id')->default(0);

            $table->integer('users_id');

            $table->timestamps();

        });

        /**  Build comments_user_vote table */
        Schema::create('comment_user_vote', function (Blueprint $table) {

            $table->integer('comment_id');

            $table->integer('user_id');

            $table->string('vote',11);  /** Can use bool instead of string? But req look like (if qString) ?vote=1. Mb change in future */

        });

        /** TODO Table [UserSeenComment] in partial migration */
        /** TODO ForeignKeys and Constraints migration in partial file */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
