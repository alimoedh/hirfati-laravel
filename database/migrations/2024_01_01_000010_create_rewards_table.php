<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points_earned')->default(0);
            $table->integer('points_spent')->default(0);
            $table->enum('action_type', ['request_completed', 'review_given', 'referral', 'bonus']);
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
