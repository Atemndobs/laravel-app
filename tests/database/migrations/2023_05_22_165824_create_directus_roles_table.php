<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('icon', 30)->default('supervised_user_circle');
            $table->text('description')->nullable();
            $table->text('ip_access')->nullable();
            $table->boolean('enforce_tfa')->default(0);
            $table->boolean('admin_access')->default(0);
            $table->boolean('app_access')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_roles');
    }
}
