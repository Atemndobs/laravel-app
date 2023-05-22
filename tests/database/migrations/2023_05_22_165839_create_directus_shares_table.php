<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_shares', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('collection', 64)->nullable();
            $table->string('item')->nullable();
            $table->uuid('role')->nullable();
            $table->string('password')->nullable();
            $table->uuid('user_created')->nullable();
            $table->timestamp('date_created')->nullable()->default('current_timestamp()');
            $table->timestamp('date_start')->nullable();
            $table->timestamp('date_end')->nullable();
            $table->integer('times_used')->default(0);
            $table->integer('max_uses')->nullable();
            
            $table->foreign('collection', 'directus_shares_collection_foreign')->references('collection')->on('directus_collections')->onDelete('cascade');
            $table->foreign('role', 'directus_shares_role_foreign')->references('id')->on('directus_roles')->onDelete('cascade');
            $table->foreign('user_created', 'directus_shares_user_created_foreign')->references('id')->on('directus_users')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_shares');
    }
}
