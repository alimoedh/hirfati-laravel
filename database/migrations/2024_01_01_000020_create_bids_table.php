<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('craftsman_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->integer('duration_days')->default(1);
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamps();

            // لا يمكن لحرفي واحد أن يقدم أكثر من عرض لنفس الطلب
            $table->unique(['request_id', 'craftsman_id']);

            $table->index(['request_id', 'status']);
            $table->index('craftsman_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
