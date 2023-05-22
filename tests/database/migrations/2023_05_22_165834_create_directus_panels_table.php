<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusPanelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_panels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('dashboard');
            $table->string('name')->nullable();
            $table->string('icon', 30)->nullable();
            $table->string('color', 10)->nullable();
            $table->boolean('show_header')->default(0);
            $table->text('note')->nullable();
            $table->string('type');
            $table->integer('position_x');
            $table->integer('position_y');
            $table->integer('width');
            $table->integer('height');
            $table->longText('options')->nullable();
            $table->timestamp('date_created')->nullable()->default('current_timestamp()');
            $table->uuid('user_created')->nullable();
            
            $table->foreign('dashboard', 'directus_panels_dashboard_foreign')->references('id')->on('directus_dashboards')->onDelete('cascade');
            $table->foreign('user_created', 'directus_panels_user_created_foreign')->references('id')->on('directus_users')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_panels');
    }
}
