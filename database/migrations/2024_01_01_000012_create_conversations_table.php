<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('craftsman_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('last_message_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
