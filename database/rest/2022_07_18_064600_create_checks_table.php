<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('host_id')->index('checks_host_id_foreign');
            $table->string('type');
            $table->string('status')->nullable();
            $table->boolean('enabled')->default(true);
            $table->text('last_run_message')->nullable();
            $table->json('last_run_output')->nullable();
            $table->timestamp('last_ran_at')->nullable();
            $table->integer('next_run_in_minutes')->nullable();
            $table->timestamp('started_throttling_failing_notifications_at')->nullable();
            $table->json('custom_properties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checks');
    }
}
