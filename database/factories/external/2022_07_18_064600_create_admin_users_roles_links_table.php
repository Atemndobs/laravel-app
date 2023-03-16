<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminUsersRolesLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users_roles_links', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->index('admin_users_roles_links_fk');
            $table->unsignedInteger('role_id')->nullable()->index('admin_users_roles_links_inv_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users_roles_links');
    }
}
