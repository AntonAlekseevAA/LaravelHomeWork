<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNotSeenCommentsAddUniqueKeyOnUserIdCommentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('not_seen_comments', function (Blueprint $table) {
            $table->unique(array('user_id', 'comment_id'), 'comment_id_user_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('not_seen_comments', function (Blueprint $table) {
            $table->dropUnique('comment_id_user_id_unique');
        });
    }
}
