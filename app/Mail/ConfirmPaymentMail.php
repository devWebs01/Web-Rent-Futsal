<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public $name; // Tambahkan variabel name

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Konfirmasi Pembayaran')
            ->view('emails.confirm_payment')
            ->with([
                'booking' => $this->booking,
                'name' => $this->booking->user->name,
                'expired_at' => $this->booking->expired_at,
            ]);
    }
}
