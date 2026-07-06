<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('home_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('away_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->enum('skill_level', ['beginner','intermediate','advanced','mixed'])->default('mixed');
            $table->enum('match_type', ['5v5','7v7','11v11','custom'])->default('custom');
            $table->integer('slots_available')->default(0);
            $table->dateTime('match_date');
            $table->enum('status', ['open','full','ongoing','completed','cancelled'])->default('open');
            $table->enum('visibility', ['public','private'])->default('public');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
