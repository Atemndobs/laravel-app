<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAdminPermissionsRoleLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_permissions_role_links', function (Blueprint $table) {
            $table->foreign(['permission_id'], 'admin_permissions_role_links_fk')->references(['id'])->on('admin_permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['role_id'], 'admin_permissions_role_links_inv_fk')->references(['id'])->on('admin_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_permissions_role_links', function (Blueprint $table) {
            $table->dropForeign('admin_permissions_role_links_fk');
            $table->dropForeign('admin_permissions_role_links_inv_fk');
        });
    }
}
