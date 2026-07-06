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
            $table->string('name');
            $table->string('craft_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedTinyInteger('experience_years');
            $table->text('description')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->integer('hourly_rate')->default(0);
            $table->integer('daily_rate')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('profile_image')->nullable();
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