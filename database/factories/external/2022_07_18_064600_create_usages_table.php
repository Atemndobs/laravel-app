<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usages', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('has_played')->nullable();
            $table->integer('play_count')->nullable();
            $table->boolean('like')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->dateTime('published_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('usages_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('usages_updated_by_id_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usages');
    }
}
