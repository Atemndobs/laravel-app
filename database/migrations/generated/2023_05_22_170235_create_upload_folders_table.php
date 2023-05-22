<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_folders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('path_id')->nullable()->unique('upload_folders_path_id_index');
            $table->string('path')->nullable()->unique('upload_folders_path_index');
            $table->timestamps(, 6);
            $table->unsignedInteger('created_by_id')->nullable();
            $table->unsignedInteger('updated_by_id')->nullable();
            
            $table->foreign('created_by_id', 'upload_folders_created_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
            $table->foreign('updated_by_id', 'upload_folders_updated_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upload_folders');
    }
}
