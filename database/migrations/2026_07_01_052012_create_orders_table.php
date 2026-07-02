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
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
       //   $table->foreignId('profession_id')->constrained()->onDelete('cascade');لا داعي له طالما العلاقة متعدد لمتعدد
              $table->foreignId('city_id')->constrained()->onDelete('cascade');
                $table->foreignId('area_id')->constrained()->onDelete('cascade');
                 $table->string('title');
                 $table->text('description');
                 $table->string('adress');
                 $table->date('service_date')->nullable();
                 $table->decimal('budget' ,8,2)->nullable();
                   $table->enum('status',['pending','accepted','in_progress','completed','cancelled'])->default('pending');
            $table->timestamps();
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
