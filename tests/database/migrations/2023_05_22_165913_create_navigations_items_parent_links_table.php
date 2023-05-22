<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavigationsItemsParentLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigations_items_parent_links', function (Blueprint $table) {
            $table->unsignedInteger('navigation_item_id')->nullable();
            $table->unsignedInteger('inv_navigation_item_id')->nullable();
            
            $table->foreign('navigation_item_id', 'navigations_items_parent_links_fk')->references('id')->on('navigations_items')->onDelete('cascade');
            $table->foreign('inv_navigation_item_id', 'navigations_items_parent_links_inv_fk')->references('id')->on('navigations_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navigations_items_parent_links');
    }
}
