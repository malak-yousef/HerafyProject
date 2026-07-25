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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title' ,200);
            $table->text('description');
            $table->string('portfolioCover_image');
            $table->string('location')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('craftman_id')->constrained()->onDelete('cascade');
             $table->date('project_date')->nullable();
$table->integer('views_count')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};