<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->string('booking_date');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('price');
            $table->string('type');
            $table->enum('status', ['WAITING', 'START', 'STOP', 'CANCEL'])->default('WAITING');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_times');
    }
};
