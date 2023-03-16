<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usages', function (Blueprint $table) {
            $table->foreign(['created_by_id'], 'usages_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign(['updated_by_id'], 'usages_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usages', function (Blueprint $table) {
            $table->dropForeign('usages_created_by_id_fk');
            $table->dropForeign('usages_updated_by_id_fk');
        });
    }
}
