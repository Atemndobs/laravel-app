<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsCommentReportRelatedLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments_comment_report_related_links', function (Blueprint $table) {
            $table->unsignedInteger('comment_report_id')->nullable()->index('comments_comment_report_related_links_fk');
            $table->unsignedInteger('comment_id')->nullable()->index('comments_comment_report_related_links_inv_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments_comment_report_related_links');
    }
}
