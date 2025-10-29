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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_a')->nullable()->constrained('teams');
            $table->foreignId('team_b')->nullable()->constrained('teams');
            $table->tinyInteger('round');
            $table->integer('score_a')->nullable();
            $table->integer('score_b')->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('teams');
            $table->foreignId('tournament_id')->constrained('tournaments');
            $table->foreignId('next_match_id')->nullable()->constrained('matches');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
