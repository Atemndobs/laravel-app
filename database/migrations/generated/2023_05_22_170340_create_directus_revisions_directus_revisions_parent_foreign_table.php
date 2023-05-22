<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusRevisionsDirectusRevisionsParentForeignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('directus_revisions', function (Blueprint $table) {
            $table->foreign('parent', 'directus_revisions_parent_foreign')->references('id')->on('directus_revisions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('directus_revisions', function(Blueprint $table){
            $table->dropForeign('directus_revisions_parent_foreign');
        });
    }
}
