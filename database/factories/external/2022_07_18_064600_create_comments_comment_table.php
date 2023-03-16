<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments_comment', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('content')->nullable();
            $table->boolean('blocked')->nullable();
            $table->boolean('blocked_thread')->nullable();
            $table->string('block_reason')->nullable();
            $table->string('author_id')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_email')->nullable();
            $table->string('author_avatar')->nullable();
            $table->boolean('removed')->nullable();
            $table->string('approval_status')->nullable();
            $table->string('related')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('comments_comment_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('comments_comment_updated_by_id_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments_comment');
    }
}
