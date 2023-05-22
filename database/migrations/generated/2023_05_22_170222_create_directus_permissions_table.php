<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('role')->nullable();
            $table->string('collection', 64)->index('directus_permissions_collection_foreign');
            $table->string('action', 10);
            $table->longText('permissions')->nullable();
            $table->longText('validation')->nullable();
            $table->longText('presets')->nullable();
            $table->text('fields')->nullable();
            
            $table->foreign('role', 'directus_permissions_role_foreign')->references('id')->on('directus_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_permissions');
    }
}
