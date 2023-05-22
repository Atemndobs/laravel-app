<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpPermissionsRoleLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_permissions_role_links', function (Blueprint $table) {
            $table->unsignedInteger('permission_id')->nullable();
            $table->unsignedInteger('role_id')->nullable();
            
            $table->foreign('permission_id', 'up_permissions_role_links_fk')->references('id')->on('up_permissions')->onDelete('cascade');
            $table->foreign('role_id', 'up_permissions_role_links_inv_fk')->references('id')->on('up_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_permissions_role_links');
    }
}
