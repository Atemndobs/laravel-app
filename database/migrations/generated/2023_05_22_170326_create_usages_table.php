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
            $table->tinyInteger('like')->nullable();
            $table->timestamps(, 6);
            $table->dateTime('published_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable();
            $table->unsignedInteger('updated_by_id')->nullable();
            
            $table->foreign('created_by_id', 'usages_created_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
            $table->foreign('updated_by_id', 'usages_updated_by_id_fk')->references('id')->on('admin_users')->onDelete('set NULL');
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
