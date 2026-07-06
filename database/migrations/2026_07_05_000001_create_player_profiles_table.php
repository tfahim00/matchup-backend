<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('preferred_position')->nullable();
            $table->enum('skill_level', ['beginner','intermediate','advanced','pro'])->default('beginner');
            $table->integer('age')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->enum('dominant_foot', ['right','left','both'])->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('matches_played')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_profiles');
    }
};
