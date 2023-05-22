<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusPresetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_presets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bookmark')->nullable();
            $table->uuid('user')->nullable();
            $table->uuid('role')->nullable();
            $table->string('collection', 64)->nullable()->index('directus_presets_collection_foreign');
            $table->string('search', 100)->nullable();
            $table->string('layout', 100)->default('tabular');
            $table->longText('layout_query')->nullable();
            $table->longText('layout_options')->nullable();
            $table->integer('refresh_interval')->nullable();
            $table->longText('filter')->nullable();
            $table->string('icon', 30)->default('bookmark');
            $table->string('color')->nullable();
            
            $table->foreign('role', 'directus_presets_role_foreign')->references('id')->on('directus_roles')->onDelete('cascade');
            $table->foreign('user', 'directus_presets_user_foreign')->references('id')->on('directus_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_presets');
    }
}
