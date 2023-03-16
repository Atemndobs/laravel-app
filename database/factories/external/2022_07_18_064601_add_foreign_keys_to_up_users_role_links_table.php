<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUpUsersRoleLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('up_users_role_links', function (Blueprint $table) {
            $table->foreign(['user_id'], 'up_users_role_links_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['role_id'], 'up_users_role_links_inv_fk')->references(['id'])->on('up_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('up_users_role_links', function (Blueprint $table) {
            $table->dropForeign('up_users_role_links_fk');
            $table->dropForeign('up_users_role_links_inv_fk');
        });
    }
}
