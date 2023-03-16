<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsCommentAuthorUserLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments_comment_author_user_links', function (Blueprint $table) {
            $table->unsignedInteger('comment_id')->nullable()->index('comments_comment_author_user_links_fk');
            $table->unsignedInteger('user_id')->nullable()->index('comments_comment_author_user_links_inv_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments_comment_author_user_links');
    }
}
