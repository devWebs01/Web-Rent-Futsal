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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade'); // ID keranjang
            $table->foreignId('field_id')->constrained('fields')->onDelete('cascade'); // Lapangan
            $table->date('booking_date'); // Tanggal booking
            $table->time('start_time'); // Jam mulai
            $table->time('end_time'); // Jam selesai
            $table->integer('price'); // Harga per waktu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
