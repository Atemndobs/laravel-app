<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUsagesSongsLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usages_songs_links', function (Blueprint $table) {
            $table->foreign(['usage_id'], 'usages_songs_links_fk')->references(['id'])->on('usages')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['song_id'], 'usages_songs_links_inv_fk')->references(['id'])->on('songs')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usages_songs_links', function (Blueprint $table) {
            $table->dropForeign('usages_songs_links_fk');
            $table->dropForeign('usages_songs_links_inv_fk');
        });
    }
}
