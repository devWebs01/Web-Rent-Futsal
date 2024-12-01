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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pelanggan
            $table->foreignId('field_id')->constrained('fields')->onDelete('cascade'); // Lapangan
            $table->date('booking_date'); // Tanggal booking
            $table->string('start_time'); // Jam mulai
            $table->string('end_time'); // Jam selesai
            $table->string('type'); // Jam selesai
            $table->integer('price'); // Harga per waktu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
