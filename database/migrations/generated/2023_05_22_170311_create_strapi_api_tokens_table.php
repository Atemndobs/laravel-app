<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStrapiApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strapi_api_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->string('access_key')->nullable();
            $table->timestamps(, 6);
            $table->unsignedInteger('created_by_id')->nullable();
            $table->unsignedInteger('updated_by_id')->nullable();
            
            $table->foreign('created_by_id', 'strapi_api_tokens_created_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
            $table->foreign('updated_by_id', 'strapi_api_tokens_updated_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('strapi_api_tokens');
    }
}
