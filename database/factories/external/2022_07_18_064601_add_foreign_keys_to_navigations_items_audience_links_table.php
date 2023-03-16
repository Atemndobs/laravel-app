<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToNavigationsItemsAudienceLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('navigations_items_audience_links', function (Blueprint $table) {
            $table->foreign(['navigation_item_id'], 'navigations_items_audience_links_fk')->references(['id'])->on('navigations_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['audience_id'], 'navigations_items_audience_links_inv_fk')->references(['id'])->on('audience')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('navigations_items_audience_links', function (Blueprint $table) {
            $table->dropForeign('navigations_items_audience_links_fk');
            $table->dropForeign('navigations_items_audience_links_inv_fk');
        });
    }
}
