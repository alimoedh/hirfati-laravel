<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('craftsman_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description');
            $table->string('address', 255);
            $table->date('preferred_date');
            $table->time('preferred_time')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->decimal('ai_estimate', 10, 2)->nullable();
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'])
                ->default('pending');
            $table->boolean('is_emergency')->default(false);
            $table->boolean('use_installment')->default(false);
            $table->integer('installment_count')->default(0);
            $table->string('problem_image')->nullable();
            $table->boolean('has_warranty')->default(false);
            $table->date('warranty_end_date')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
