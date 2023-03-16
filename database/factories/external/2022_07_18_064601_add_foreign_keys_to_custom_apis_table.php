<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCustomApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_apis', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'custom_apis_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'custom_apis_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_apis', function (Blueprint $table) {
            $table->dropForeign('custom_apis_created_by_id_fk');
            $table->dropForeign('custom_apis_updated_by_id_fk');
        });
    }
}
