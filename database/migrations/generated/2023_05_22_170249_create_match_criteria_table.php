<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_criteria', function (Blueprint $table) {
            $table->increments('id');
            $table->float('bpm', 10, 5)->nullable();
            $table->float('bpm_min', 10, 5)->nullable();
            $table->float('bpm_max', 10, 5)->nullable();
            $table->string('key')->nullable();
            $table->string('scale')->nullable();
            $table->string('mood')->nullable();
            $table->float('happy', 10, 5)->nullable();
            $table->float('sad', 10, 5)->nullable();
            $table->string('genre')->nullable();
            $table->float('energy', 10, 5)->nullable();
            $table->float('danceability', 10, 5)->nullable();
            $table->float('aggressiveness', 10, 5)->nullable();
            $table->string('ip')->nullable()->unique('match_criteria_ip_unique');
            $table->text('session_token')->unique('match_criteria_session_token_unique');
            $table->string('status')->default('draft');
            $table->integer('sort')->nullable();
            $table->timestamp('date_created');
            $table->timestamp('date_updated');
            $table->uuid('user_created')->nullable();
            $table->uuid('user_updated')->nullable();
            
            $table->foreign('user_created', 'match_criteria_user_created_foreign')->references('id')->on('directus_users');
            $table->foreign('user_updated', 'match_criteria_user_updated_foreign')->references('id')->on('directus_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('match_criteria');
    }
}
