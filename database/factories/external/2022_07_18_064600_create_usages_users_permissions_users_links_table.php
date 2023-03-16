<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsagesUsersPermissionsUsersLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usages_users_permissions_users_links', function (Blueprint $table) {
            $table->unsignedInteger('usage_id')->nullable()->index('usages_users_permissions_users_links_fk');
            $table->unsignedInteger('user_id')->nullable()->index('usages_users_permissions_users_links_inv_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usages_users_permissions_users_links');
    }
}
