<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusOperationsDirectusOperationsRejectForeignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('directus_operations', function (Blueprint $table) {
            $table->foreign('reject', 'directus_operations_reject_foreign')->references('id')->on('directus_operations');
            $table->foreign('resolve', 'directus_operations_resolve_foreign')->references('id')->on('directus_operations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('directus_operations', function(Blueprint $table){
            $table->dropForeign('directus_operations_reject_foreign');
            $table->dropForeign('directus_operations_resolve_foreign');
        });
    }
}
