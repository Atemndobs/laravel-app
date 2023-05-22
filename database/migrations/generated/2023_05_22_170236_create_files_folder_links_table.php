<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesFolderLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_folder_links', function (Blueprint $table) {
            $table->unsignedInteger('file_id')->nullable();
            $table->unsignedInteger('folder_id')->nullable();
            
            $table->foreign('file_id', 'files_folder_links_fk')->references('id')->on('files')->onDelete('cascade');
            $table->foreign('folder_id', 'files_folder_links_inv_fk')->references('id')->on('upload_folders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files_folder_links');
    }
}
