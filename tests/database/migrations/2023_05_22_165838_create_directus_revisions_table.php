<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_revisions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('activity');
            $table->string('collection', 64)->index('directus_revisions_collection_foreign');
            $table->string('item');
            $table->longText('data')->nullable();
            $table->longText('delta')->nullable();
            $table->unsignedInteger('parent')->nullable();
            
            $table->foreign('activity', 'directus_revisions_activity_foreign')->references('id')->on('directus_activity')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_revisions');
    }
}
