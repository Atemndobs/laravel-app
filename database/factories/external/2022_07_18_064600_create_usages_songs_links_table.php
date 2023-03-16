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
            $table->unsignedInteger('usage_id')->nullable()->index('usages_songs_links_fk');
            $table->unsignedInteger('song_id')->nullable()->index('usages_songs_links_inv_fk');
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
