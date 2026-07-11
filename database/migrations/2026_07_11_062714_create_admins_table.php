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
<<<<<<<< HEAD:database/migrations/2026_07_11_062714_create_admins_table.php
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('password');
========
        Schema::create('portfolio_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('image_name');
            $table->string('image_description')->nullable();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->softDeletes();
>>>>>>>> da7b582f1583a261e15ba612281b161133645675:database/migrations/2026_07_06_184917_create_portfolio_images_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:database/migrations/2026_07_11_062714_create_admins_table.php
        Schema::dropIfExists('admins');
========
        Schema::dropIfExists('portfolio_images');
>>>>>>>> da7b582f1583a261e15ba612281b161133645675:database/migrations/2026_07_06_184917_create_portfolio_images_table.php
    }
};
