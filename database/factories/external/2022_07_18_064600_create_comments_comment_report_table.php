<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsCommentReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments_comment_report', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('content')->nullable();
            $table->string('reason')->nullable();
            $table->boolean('resolved')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('comments_comment-report_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('comments_comment-report_updated_by_id_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments_comment_report');
    }
}
