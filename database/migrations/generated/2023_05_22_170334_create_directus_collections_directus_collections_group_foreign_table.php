<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusCollectionsDirectusCollectionsGroupForeignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('directus_collections', function (Blueprint $table) {
            $table->foreign('group', 'directus_collections_group_foreign')->references('collection')->on('directus_collections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('directus_collections', function(Blueprint $table){
            $table->dropForeign('directus_collections_group_foreign');
        });
    }
}
