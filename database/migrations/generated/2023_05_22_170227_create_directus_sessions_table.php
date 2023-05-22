<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_sessions', function (Blueprint $table) {
            $table->string('token', 64)->primary();
            $table->uuid('user')->nullable();
            $table->timestamp('expires');
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->uuid('share')->nullable();
            $table->string('origin')->nullable();
            
            $table->foreign('share', 'directus_sessions_share_foreign')->references('id')->on('directus_shares')->onDelete('cascade');
            $table->foreign('user', 'directus_sessions_user_foreign')->references('id')->on('directus_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_sessions');
    }
}
