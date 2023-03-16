<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('key')->index();
            $table->string('table_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('permissions', 'key')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropColumn('key');
            });
        }
        if (Schema::hasColumn('permissions', 'table_name')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropColumn('table_name');
            });
        }

    }
}
