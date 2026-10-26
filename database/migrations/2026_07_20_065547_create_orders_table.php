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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('city_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('area_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description');

            $table->decimal('budget', 10, 2)->nullable();

            $table->date('service_date')->nullable();

            $table->enum('status', [
                'pending',
                'accepted',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('pending');

            // إضافات أنصح بها
            $table->string('address')->nullable();

            $table->string('phone')->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
