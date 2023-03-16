<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToNavigationsItemsRelatedLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('navigations_items_related_links', function (Blueprint $table) {
            $table->foreign(['navigation_item_id'], 'navigations_items_related_links_fk')->references(['id'])->on('navigations_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['navigations_items_related_id'], 'navigations_items_related_links_inv_fk')->references(['id'])->on('navigations_items_related')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('navigations_items_related_links', function (Blueprint $table) {
            $table->dropForeign('navigations_items_related_links_fk');
            $table->dropForeign('navigations_items_related_links_inv_fk');
        });
    }
}
