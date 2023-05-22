<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('storage');
            $table->string('filename_disk')->nullable();
            $table->string('filename_download');
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->uuid('folder')->nullable();
            $table->uuid('uploaded_by')->nullable();
            $table->timestamp('uploaded_on')->default('current_timestamp()');
            $table->uuid('modified_by')->nullable();
            $table->timestamp('modified_on')->default('current_timestamp()');
            $table->string('charset', 50)->nullable();
            $table->bigInteger('filesize')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->string('embed', 200)->nullable();
            $table->text('description')->nullable();
            $table->text('location')->nullable();
            $table->text('tags')->nullable();
            $table->longText('metadata')->nullable();
            
            $table->foreign('folder', 'directus_files_folder_foreign')->references('id')->on('directus_folders')->onDelete('set NULL');
            $table->foreign('modified_by', 'directus_files_modified_by_foreign')->references('id')->on('directus_users');
            $table->foreign('uploaded_by', 'directus_files_uploaded_by_foreign')->references('id')->on('directus_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_files');
    }
}
