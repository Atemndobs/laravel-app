<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('alternative_text')->nullable();
            $table->string('caption')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->longText('formats')->nullable();
            $table->string('hash')->nullable();
            $table->string('ext')->nullable();
            $table->string('mime')->nullable();
            $table->decimal('size', 10, 2)->nullable();
            $table->string('url')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('provider')->nullable();
            $table->longText('provider_metadata')->nullable();
            $table->timestamps(, 6);
            $table->unsignedInteger('created_by_id')->nullable();
            $table->unsignedInteger('updated_by_id')->nullable();
            
            $table->foreign('created_by_id', 'files_created_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
            $table->foreign('updated_by_id', 'files_updated_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
