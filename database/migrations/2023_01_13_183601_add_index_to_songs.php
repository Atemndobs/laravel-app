<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->index('slug');
            $table->index('title');
            $table->index('author');
            $table->index('key');
            $table->index('bpm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropIndex('slug');
            $table->dropIndex('title');
            $table->dropIndex('author');
            $table->dropIndex('key');
            $table->dropIndex('bpm');
        });
    }
};
