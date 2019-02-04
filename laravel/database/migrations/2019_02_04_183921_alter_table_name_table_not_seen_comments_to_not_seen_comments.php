<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableNameTableNotSeenCommentsToNotSeenComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('table_not_seen_comments', function (Blueprint $table) {
            $table->rename('not_seen_comments');
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
            $table->rename('table_not_seen_comments');
        });
    }
}
