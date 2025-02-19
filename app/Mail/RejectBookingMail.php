<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RejectBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Penyewaan Dibatalkan')
            ->view('emails.reject_booking')
            ->with([
                'name' => $this->booking->user->name,
                'booking' => $this->booking,
            ]);
    }
}
