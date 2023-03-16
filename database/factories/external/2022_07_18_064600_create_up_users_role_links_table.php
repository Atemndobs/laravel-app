<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpUsersRoleLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_users_role_links', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->index('up_users_role_links_fk');
            $table->unsignedInteger('role_id')->nullable()->index('up_users_role_links_inv_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_users_role_links');
    }
}
