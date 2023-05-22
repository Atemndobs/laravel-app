<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadFoldersParentLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_folders_parent_links', function (Blueprint $table) {
            $table->unsignedInteger('folder_id')->nullable();
            $table->unsignedInteger('inv_folder_id')->nullable();
            
            $table->foreign('folder_id', 'upload_folders_parent_links_fk')->references('id')->on('upload_folders')->onDelete('cascade');
            $table->foreign('inv_folder_id', 'upload_folders_parent_links_inv_fk')->references('id')->on('upload_folders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upload_folders_parent_links');
    }
}
