<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('song_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key_name', 10);
            $table->integer('key_number');
            $table->string('scale', 6);
        });

        DB::table('song_keys')->insert([
            ['key_name' => 'C', 'key_number' => 1, 'scale' => 'Major'],
            ['key_name' => 'C#', 'key_number' => 2, 'scale' => 'Major'],
            ['key_name' => 'Db', 'key_number' => 2, 'scale' => 'Major'],
            ['key_name' => 'Cminor', 'key_number' => 3, 'scale' => 'Minor'],
            ['key_name' => 'C#minor', 'key_number' => 4, 'scale' => 'Minor'],
            ['key_name' => 'D', 'key_number' => 5, 'scale' => 'Major'],
            ['key_name' => 'D#', 'key_number' => 6, 'scale' => 'Major'],
            ['key_name' => 'Eb', 'key_number' => 6, 'scale' => 'Major'],
            ['key_name' => 'Dminor', 'key_number' => 7, 'scale' => 'Minor'],
            ['key_name' => 'D#minor', 'key_number' => 8, 'scale' => 'Minor'],
            ['key_name' => 'E', 'key_number' => 9, 'scale' => 'Major'],
            ['key_name' => 'F', 'key_number' => 10, 'scale' => 'Major'],
            ['key_name' => 'F#', 'key_number' => 11, 'scale' => 'Major'],
            ['key_name' => 'Gb', 'key_number' => 11, 'scale' => 'Major'],
            ['key_name' => 'Fminor', 'key_number' => 12, 'scale' => 'Minor'],
            ['key_name' => 'F#minor', 'key_number' => 13, 'scale' => 'Minor'],
            ['key_name' => 'G', 'key_number' => 14, 'scale' => 'Major'],
            ['key_name' => 'G#', 'key_number' => 15, 'scale' => 'Major'],
            ['key_name' => 'Ab', 'key_number' => 15, 'scale' => 'Major'],
            ['key_name' => 'Gminor', 'key_number' => 16, 'scale' => 'Minor'],
            ['key_name' => 'G#minor', 'key_number' => 17, 'scale' => 'Minor'],
            ['key_name' => 'A', 'key_number' => 18, 'scale' => 'Major'],
            ['key_name' => 'A#', 'key_number' => 19, 'scale' => 'Major'],
            ['key_name' => 'Bb', 'key_number' => 19, 'scale' => 'Major'],
            ['key_name' => 'Aminor', 'key_number' => 20, 'scale' => 'Minor'],
            ['key_name' => 'A#minor', 'key_number' => 21, 'scale' => 'Minor'],
            ['key_name' => 'B', 'key_number' => 22, 'scale' => 'Major'],
            ['key_name' => 'Bminor', 'key_number' => 23, 'scale' => 'Minor'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_keys');
    }
};
