<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('name');
            $table->string('email')->unique('users_email_unique');
            $table->boolean('super')->default(0);
            $table->longText('preferences')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('password');
            $table->string('avatar')->default('users/default.png');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->text('settings')->nullable();
            $table->timestamps();
            
            $table->foreign('role_id', 'users_role_id_foreign')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
