<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('many_collection', 64)->index('directus_relations_many_collection_foreign');
            $table->string('many_field', 64);
            $table->string('one_collection', 64)->nullable()->index('directus_relations_one_collection_foreign');
            $table->string('one_field', 64)->nullable();
            $table->string('one_collection_field', 64)->nullable();
            $table->text('one_allowed_collections')->nullable();
            $table->string('junction_field', 64)->nullable();
            $table->string('sort_field', 64)->nullable();
            $table->string('one_deselect_action')->default('nullify');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_relations');
    }
}
