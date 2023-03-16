<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavigationsItemsRelatedLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigations_items_related_links', function (Blueprint $table) {
            $table->unsignedInteger('navigation_item_id')->nullable()->index('navigations_items_related_links_fk');
            $table->unsignedInteger('navigations_items_related_id')->nullable()->index('navigations_items_related_links_inv_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navigations_items_related_links');
    }
}
