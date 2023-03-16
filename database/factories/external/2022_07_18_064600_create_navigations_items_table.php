<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavigationsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigations_items', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('title')->nullable();
            $table->string('type')->nullable();
            $table->longText('path')->nullable();
            $table->longText('external_path')->nullable();
            $table->string('ui_router_key')->nullable();
            $table->boolean('menu_attached')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('collapsed')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('navigations_items_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('navigations_items_updated_by_id_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navigations_items');
    }
}
