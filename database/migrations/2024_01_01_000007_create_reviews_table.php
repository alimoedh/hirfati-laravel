<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('craftsman_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('rating');
            $table->tinyInteger('quality_rating')->nullable();
            $table->tinyInteger('punctuality_rating')->nullable();
            $table->tinyInteger('behavior_rating')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
