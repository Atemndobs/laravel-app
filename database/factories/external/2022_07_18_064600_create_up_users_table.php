<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('up_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('provider')->nullable();
            $table->string('password')->nullable();
            $table->string('reset_password_token')->nullable();
            $table->string('confirmation_token')->nullable();
            $table->boolean('confirmed')->nullable();
            $table->boolean('blocked')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('up_users_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('up_users_updated_by_id_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('up_users');
    }
}
