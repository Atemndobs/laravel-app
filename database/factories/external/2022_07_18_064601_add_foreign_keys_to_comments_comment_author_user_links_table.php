<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommentsCommentAuthorUserLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments_comment_author_user_links', function (Blueprint $table) {
            $table->foreign(['comment_id'], 'comments_comment_author_user_links_fk')->references(['id'])->on('comments_comment')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['user_id'], 'comments_comment_author_user_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments_comment_author_user_links', function (Blueprint $table) {
            $table->dropForeign('comments_comment_author_user_links_fk');
            $table->dropForeign('comments_comment_author_user_links_inv_fk');
        });
    }
}
