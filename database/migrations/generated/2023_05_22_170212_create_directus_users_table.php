<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('email', 128)->nullable()->unique('directus_users_email_unique');
            $table->string('password')->nullable();
            $table->string('location')->nullable();
            $table->string('title', 50)->nullable();
            $table->text('description')->nullable();
            $table->longText('tags')->nullable();
            $table->uuid('avatar')->nullable();
            $table->string('language')->nullable();
            $table->string('theme', 20)->default('auto');
            $table->string('tfa_secret')->nullable();
            $table->string('status', 16)->default('active');
            $table->uuid('role')->nullable();
            $table->string('token')->nullable()->unique('directus_users_token_unique');
            $table->timestamp('last_access')->nullable();
            $table->string('last_page')->nullable();
            $table->string('provider', 128)->default('default');
            $table->string('external_identifier')->nullable()->unique('directus_users_external_identifier_unique');
            $table->longText('auth_data')->nullable();
            $table->boolean('email_notifications')->default(1);
            
            $table->foreign('role', 'directus_users_role_foreign')->references('id')->on('directus_roles')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_users');
    }
}
