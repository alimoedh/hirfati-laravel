<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('craftsman_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('craftsman_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('escrow_balance', 12, 2)->default(0);
            $table->decimal('total_earned', 12, 2)->default(0);
            $table->decimal('total_commission', 12, 2)->default(0);
            $table->decimal('total_withdrawn', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('craftsman_wallets');
    }
};
