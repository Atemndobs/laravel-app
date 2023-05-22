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
            $table->string('title')->nullable()->index('songs_title_index');
            $table->string('author')->nullable()->index('songs_author_index');
            $table->string('link')->nullable();
            $table->string('source')->nullable();
            $table->string('key')->nullable()->index('songs_key_index');
            $table->string('scale')->nullable();
            $table->double('bpm')->nullable()->index('songs_bpm_index');
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
            $table->tinyInteger('analyzed')->nullable();
            $table->string('related_songs')->nullable();
            $table->longText('genre')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('played')->nullable();
            $table->string('slug')->nullable()->index('songs_slug_index');
            $table->timestamps(, 6);
            $table->longText('classification_properties')->nullable();
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
