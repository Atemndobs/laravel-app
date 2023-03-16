<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTaskFrequenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_frequencies', function (Blueprint $table) {
            $table->foreign(['task_id'], 'task_frequencies_task_id_fk')->references(['id'])->on('tasks')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_frequencies', function (Blueprint $table) {
            $table->dropForeign('task_frequencies_task_id_fk');
        });
    }
}
