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
            $table->tinyInteger('menu_attached')->nullable();
            $table->integer('order')->nullable();
            $table->tinyInteger('collapsed')->nullable();
            $table->timestamps(, 6);
            $table->unsignedInteger('created_by_id')->nullable();
            $table->unsignedInteger('updated_by_id')->nullable();
            
            $table->foreign('created_by_id', 'navigations_items_created_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
            $table->foreign('updated_by_id', 'navigations_items_updated_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
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
