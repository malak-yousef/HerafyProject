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
        Schema::create('craftmen', function (Blueprint $table) {
            $table->id();
            $table->string('craft_name');
            $table->integer('id_number')->unique();
            $table->unsignedTinyInteger('experience_years');
            $table->text('description')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->integer('hourly_rate')->default(0);
            $table->integer('daily_rate')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('craftmen');
    }
};