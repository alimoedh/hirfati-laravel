<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('craftsman_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('request_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['escrow_in', 'escrow_release', 'commission', 'withdraw', 'refund']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->timestamps();

            $table->index(['craftsman_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
