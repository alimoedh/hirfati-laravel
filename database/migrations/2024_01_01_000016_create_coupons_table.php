<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->integer('discount_percent')->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('min_order', 10, 2)->default(0);
            $table->integer('max_uses')->default(1);
            $table->integer('used_count')->default(0);
            $table->date('expiry_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
