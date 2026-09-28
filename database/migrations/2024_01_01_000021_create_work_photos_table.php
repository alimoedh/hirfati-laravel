<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['before', 'after']);
            $table->string('image_path');
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['request_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_photos');
    }
};
