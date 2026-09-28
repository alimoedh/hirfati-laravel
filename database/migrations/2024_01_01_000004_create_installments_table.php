<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2);
            $table->integer('installment_count')->default(3);
            $table->integer('current_installment')->default(1);
            $table->date('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
