<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('super')->default(false);
            $table->json('preferences')->nullable();
            $table->timestamp('last_login')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'super')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('super');
            });
        }
        if (Schema::hasColumn('users', 'preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('preferences');
            });
        }
        if (Schema::hasColumn('users', 'last_login')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_login');
            });
        }

    }
};
