<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('command');
            $table->string('parameters')->nullable();
            $table->string('expression')->nullable();
            $table->string('timezone')->default('UTC');
            $table->boolean('is_active')->default(1)->index('tasks_is_active_idx');
            $table->boolean('dont_overlap')->default(0)->index('tasks_dont_overlap_idx');
            $table->boolean('run_in_maintenance')->default(0)->index('tasks_run_in_maintenance_idx');
            $table->string('notification_email_address')->nullable();
            $table->string('notification_phone_number')->nullable();
            $table->string('notification_slack_webhook')->nullable();
            $table->timestamps();
            $table->integer('auto_cleanup_num')->default(0)->index('tasks_auto_cleanup_num_idx');
            $table->string('auto_cleanup_type', 20)->nullable()->index('tasks_auto_cleanup_type_idx');
            $table->boolean('run_on_one_server')->default(0)->index('tasks_run_on_one_server_idx');
            $table->boolean('run_in_background')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
