<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToNavigationsItemsRelatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('navigations_items_related', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'navigations_items_related_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'navigations_items_related_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('navigations_items_related', function (Blueprint $table) {
            $table->dropForeign('navigations_items_related_created_by_id_fk');
            $table->dropForeign('navigations_items_related_updated_by_id_fk');
        });
    }
}
