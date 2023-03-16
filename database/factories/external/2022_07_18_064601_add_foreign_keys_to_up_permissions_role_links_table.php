<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUpPermissionsRoleLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('up_permissions_role_links', function (Blueprint $table) {
            $table->foreign(['permission_id'], 'up_permissions_role_links_fk')->references(['id'])->on('up_permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['role_id'], 'up_permissions_role_links_inv_fk')->references(['id'])->on('up_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('up_permissions_role_links', function (Blueprint $table) {
            $table->dropForeign('up_permissions_role_links_fk');
            $table->dropForeign('up_permissions_role_links_inv_fk');
        });
    }
}
