<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthCheckResultHistoryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_check_result_history_items', function (Blueprint $table) {
            $table->id();
            $table->string('check_name');
            $table->string('check_label');
            $table->string('status');
            $table->text('notification_message')->nullable();
            $table->string('short_summary')->nullable();
            $table->longText('meta');
            $table->timestamp('ended_at');
            $table->uuid('batch')->index('health_check_result_history_items_batch_index');
            $table->timestamps();
            
            $table->index(['created_at'], 'health_check_result_history_items_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_check_result_history_items');
    }
}
