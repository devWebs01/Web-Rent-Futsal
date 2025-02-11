<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 'user_id',
     * 'invoice',
     * 'status',
     * 'total_price',
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'PAID',
                'UNPAID',
                'CANCEL',
                'PROCESS',
                'CONFIRM',
                'COMPLETE',
                'PENDING',
                'VERIFICATION',
            ])->default('UNPAID');
            $table->enum(
                'payment_method',
                [
                    'draf',
                    'fullpayment',
                    'downpayment',
                ]
            )->default('draf');
            $table->string('total_price');
            $table->longText('message')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
