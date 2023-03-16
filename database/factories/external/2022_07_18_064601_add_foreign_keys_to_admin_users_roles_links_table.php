<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAdminUsersRolesLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_users_roles_links', function (Blueprint $table) {
            $table->foreign(['user_id'], 'admin_users_roles_links_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['role_id'], 'admin_users_roles_links_inv_fk')->references(['id'])->on('admin_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_users_roles_links', function (Blueprint $table) {
            $table->dropForeign('admin_users_roles_links_fk');
            $table->dropForeign('admin_users_roles_links_inv_fk');
        });
    }
}
