<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_results', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id');
            $table->timestamp('ran_at')->default('current_timestamp()')->index('task_results_ran_at_idx');
            $table->longText('result');
            $table->timestamps();
            $table->decimal('duration', 24, 14)->default(0.00000000000000);
            
            $table->index(['created_at'], 'task_results_created_at_index');
            $table->foreign('task_id', 'task_id_fk')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_results');
    }
}
