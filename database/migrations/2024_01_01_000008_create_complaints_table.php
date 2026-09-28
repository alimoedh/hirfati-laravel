<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('craftsman_id')->constrained('users')->cascadeOnDelete();
            $table->enum('reason', ['quality', 'delay', 'price', 'behavior', 'other']);
            $table->text('details');
            $table->string('evidence_image')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'resolved', 'rejected'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
