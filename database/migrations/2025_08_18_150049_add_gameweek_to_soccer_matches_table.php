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
        Schema::table('soccer_matches', function (Blueprint $table) {
            $table->integer('gameweek')->nullable()->after('league_id')->index();
            $table->string('gameweek_label')->nullable()->after('gameweek'); // e.g., "Matchday 1", "Week 5"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soccer_matches', function (Blueprint $table) {
            $table->dropColumn(['gameweek', 'gameweek_label']);
        });
    }
};
