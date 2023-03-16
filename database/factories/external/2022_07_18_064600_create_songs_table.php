<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('author')->nullable();
            $table->string('link')->nullable();
            $table->string('source')->nullable();
            $table->string('key')->nullable();
            $table->string('scale')->nullable();
            $table->double('bpm')->nullable();
            $table->double('duration')->nullable();
            $table->double('danceability')->nullable();
            $table->double('happy')->nullable();
            $table->double('sad')->nullable();
            $table->double('relaxed')->nullable();
            $table->double('aggressiveness')->nullable();
            $table->double('energy')->nullable();
            $table->text('comment')->nullable();
            $table->string('path')->nullable();
            $table->string('extension')->nullable();
            $table->string('status')->nullable();
            $table->boolean('analyzed')->nullable();
            $table->string('related_songs')->nullable();
            $table->json('genre')->nullable();
            $table->string('image')->nullable();
            $table->boolean('played')->nullable();
            $table->string('slug')->nullable();
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedInteger('created_by_id')->nullable()->index('songs_created_by_id_fk');
            $table->unsignedInteger('updated_by_id')->nullable()->index('songs_updated_by_id_fk');
            $table->json('classification_properties')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('songs');
    }
}
