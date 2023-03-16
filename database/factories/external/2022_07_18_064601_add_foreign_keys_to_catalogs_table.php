<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCatalogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'catalogs_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'catalogs_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropForeign('catalogs_created_by_id_fk');
            $table->dropForeign('catalogs_updated_by_id_fk');
        });
    }
}
