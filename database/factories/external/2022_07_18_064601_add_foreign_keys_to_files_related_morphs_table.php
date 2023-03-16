<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToFilesRelatedMorphsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files_related_morphs', function (Blueprint $table) {
            $table->foreign(['file_id'], 'files_related_morphs_fk')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files_related_morphs', function (Blueprint $table) {
            $table->dropForeign('files_related_morphs_fk');
        });
    }
}
