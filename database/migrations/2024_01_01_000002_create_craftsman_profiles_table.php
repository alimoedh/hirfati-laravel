<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('craftsman_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->integer('experience_years')->default(0);
            $table->text('bio')->nullable();
            $table->string('identity_document')->nullable();
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_emergency')->default(false);
            $table->string('emergency_phone', 20)->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('craftsman_profiles');
    }
};
