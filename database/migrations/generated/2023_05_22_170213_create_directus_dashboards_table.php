<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusDashboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_dashboards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('icon', 30)->default('dashboard');
            $table->text('note')->nullable();
            $table->timestamp('date_created')->nullable()->default('current_timestamp()');
            $table->uuid('user_created')->nullable();
            $table->string('color')->nullable();
            
            $table->foreign('user_created', 'directus_dashboards_user_created_foreign')->references('id')->on('directus_users')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_dashboards');
    }
}
