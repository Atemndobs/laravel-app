<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminPermissionsRoleLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_permissions_role_links', function (Blueprint $table) {
            $table->unsignedInteger('permission_id')->nullable();
            $table->unsignedInteger('role_id')->nullable();
            
            $table->foreign('permission_id', 'admin_permissions_role_links_fk')->references('id')->on('admin_permissions')->onDelete('cascade');
            $table->foreign('role_id', 'admin_permissions_role_links_inv_fk')->references('id')->on('admin_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_permissions_role_links');
    }
}
