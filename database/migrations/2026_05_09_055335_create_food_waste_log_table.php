<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_waste_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_menu')
                  ->constrained('menu', 'id_menu')
                  ->cascadeOnDelete();
            $table->integer('jumlah');
            $table->enum('alasan', ['kadaluarsa','rusak','sisa_hari','lainnya'])
                  ->default('lainnya');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_waste_log');
    }
};