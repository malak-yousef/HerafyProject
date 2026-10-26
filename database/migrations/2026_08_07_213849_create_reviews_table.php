<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // التقييم من 1 إلى 5
            $table->tinyInteger('rating');

            // نص التقييم
            $table->text('comment')->nullable();

            // الحرفي الذي يتم تقييمه
            $table->foreignId('craftsman_id')
                ->constrained('craftmen')
                ->cascadeOnDelete();

            // الزبون الذي يقيّم
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            // الطلب (request) المرتبط بالتقييم
            $table->foreignId('request_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // حالة التقييم (مقبول / مرفوض / معلق)
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
