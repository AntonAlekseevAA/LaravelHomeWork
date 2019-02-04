<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNotSeenComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_not_seen_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('comment_id', false, true);
            $table->integer('user_id', false, true);
            $table->timestamps();
        });

        Schema::table('table_not_seen_comments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
        Schema::table('table_not_seen_comments', function (Blueprint $table) {
            $table->foreign('comment_id')->references('id')->on('comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_not_seen_comments');
    }
}
