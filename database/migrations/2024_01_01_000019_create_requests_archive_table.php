<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests_archive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('craftsman_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->string('title', 200);
            $table->text('description');
            $table->string('address', 255);
            $table->date('preferred_date');
            $table->time('preferred_time')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->decimal('ai_estimate', 10, 2)->nullable();
            $table->string('status');
            $table->boolean('is_emergency')->default(false);
            $table->boolean('use_installment')->default(false);
            $table->integer('installment_count')->default(0);
            $table->string('problem_image')->nullable();
            $table->boolean('has_warranty')->default(false);
            $table->date('warranty_end_date')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
            $table->timestamp('archived_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests_archive');
    }
};
