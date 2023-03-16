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
            $table->unsignedInteger('task_id')->index('task_results_task_id_idx');
            $table->timestamp('ran_at')->useCurrent()->index('task_results_ran_at_idx');
            $table->longText('result');
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();
            $table->decimal('duration', 24, 14)->default(0);
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
