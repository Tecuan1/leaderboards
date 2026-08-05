<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('score');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');

            $table->index(['game_id', 'score' => 'desc']);
            $table->index(['game_id', 'user_id', 'score']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
