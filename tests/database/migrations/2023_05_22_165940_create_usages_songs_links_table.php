<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsagesSongsLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usages_songs_links', function (Blueprint $table) {
            $table->unsignedInteger('usage_id')->nullable();
            $table->unsignedInteger('song_id')->nullable();
            
            $table->foreign('usage_id', 'usages_songs_links_fk')->references('id')->on('usages')->onDelete('cascade');
            $table->foreign('song_id', 'usages_songs_links_inv_fk')->references('id')->on('songs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usages_songs_links');
    }
}
