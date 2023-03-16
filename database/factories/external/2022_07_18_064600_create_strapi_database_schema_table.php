<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStrapiDatabaseSchemaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strapi_database_schema', function (Blueprint $table) {
            $table->increments('id');
            $table->json('schema')->nullable();
            $table->dateTime('time')->nullable();
            $table->string('hash')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('strapi_database_schema');
    }
}
