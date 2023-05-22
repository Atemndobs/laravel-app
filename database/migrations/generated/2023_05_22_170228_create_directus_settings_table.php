<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('project_name', 100)->default('Directus');
            $table->string('project_url')->nullable();
            $table->string('project_color', 50)->nullable();
            $table->uuid('project_logo')->nullable();
            $table->uuid('public_foreground')->nullable();
            $table->uuid('public_background')->nullable();
            $table->text('public_note')->nullable();
            $table->unsignedInteger('auth_login_attempts')->default(25);
            $table->string('auth_password_policy', 100)->nullable();
            $table->string('storage_asset_transform', 7)->default('all');
            $table->longText('storage_asset_presets')->nullable();
            $table->text('custom_css')->nullable();
            $table->uuid('storage_default_folder')->nullable();
            $table->longText('basemaps')->nullable();
            $table->string('mapbox_key')->nullable();
            $table->longText('module_bar')->nullable();
            $table->string('project_descriptor', 100)->nullable();
            $table->longText('translation_strings')->nullable();
            $table->string('default_language')->default('en-US');
            $table->longText('custom_aspect_ratios')->nullable();
            
            $table->foreign('project_logo', 'directus_settings_project_logo_foreign')->references('id')->on('directus_files');
            $table->foreign('public_background', 'directus_settings_public_background_foreign')->references('id')->on('directus_files');
            $table->foreign('public_foreground', 'directus_settings_public_foreground_foreign')->references('id')->on('directus_files');
            $table->foreign('storage_default_folder', 'directus_settings_storage_default_folder_foreign')->references('id')->on('directus_folders')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_settings');
    }
}
