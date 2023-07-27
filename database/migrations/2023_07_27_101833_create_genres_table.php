<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        $genres = [
            'Afrobeat', 'Afrobeats', 'Afro House', 'Afro Pop', 'Afro Fusion', 'Afro Dancehall', 'Afro Soul', 'Afro Trap',
            'Afro R&B', 'Afro Highlife', 'Afro EDM', 'Afro Hip Hop', 'Afroswing', 'Moombahton',
            'House', 'Deep House', 'Tech House', 'Progressive House', 'Electro House', 'Future House', 'Tropical House',
            'Soulful House', 'Bass House', 'Funky House', 'Acid House', 'Garage House', 'G-House', 'Jackin\' House',
            'Microhouse', 'Nu-Disco (also known as Disco House)', 'Soul House', 'Tribal House', 'Kwaito', 'Amapiano',
            'Maskandi', 'Gqom', 'Jazz', 'Pop', 'Hip Hop', 'R&B', 'Gospel', 'Rock', 'Reggae', 'Pop Gospel',
            'Isicathamiya', 'Marabi', 'Cape Jazz', 'Boeremusiek', 'soka', 'Contemporary R&B', 'Neo-Soul',
            'Alternative R&B', 'Pop R&B', 'Trap&B (Trap R&B)', 'Funk/Soul', 'New Jack Swing', 'Doo-Wop', 'Motown',
            'Rap', 'Reggaeton', "Salsa",
            "Merengue",
            "Bachata",
            "Reggaeton",
            "Cumbia",
            "Tango",
            "Flamenco",
            "Bossa Nova",
            "Rumba",
            "Bolero",
            "Samba",
            "Vallenato",
            "Mariachi",
            "Banda",
            "Ranchera",
            "Mambo",
            "Cha-Cha-Cha",
            "Son",
            "Perreo",
            "Zouk-Lambada",
            "Forró",
            "Tango Nuevo",
            "Soca",
            "Pasodoble",
            "Huayno",
            "Timba",
            "Porro",
            "Latin Pop",
            "Latin Rock",
            "Latin Jazz",
            "Latin Hip Hop",
            "Latin Trap",
            "Latin Reggae",
            "Latin Fusion",
            "Latin Electronic",
            "Latin Alternative"
        ];

        foreach ($genres as $genre) {
            DB::table('genres')->insert(['name' => $genre]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};
