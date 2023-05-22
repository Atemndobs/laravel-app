<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectusOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directus_operations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('key');
            $table->string('type');
            $table->integer('position_x');
            $table->integer('position_y');
            $table->longText('options')->nullable();
            $table->uuid('resolve')->nullable()->unique('directus_operations_resolve_unique');
            $table->uuid('reject')->nullable()->unique('directus_operations_reject_unique');
            $table->uuid('flow');
            $table->timestamp('date_created')->nullable()->default('current_timestamp()');
            $table->uuid('user_created')->nullable();
            
            $table->foreign('flow', 'directus_operations_flow_foreign')->references('id')->on('directus_flows')->onDelete('cascade');
            $table->foreign('user_created', 'directus_operations_user_created_foreign')->references('id')->on('directus_users')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directus_operations');
    }
}
