<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $bookingDate;
    public $startTime;

    public function __construct($user, $bookingDate, $startTime)
    {
        $this->user = $user;
        $this->bookingDate = $bookingDate;
        $this->startTime = $startTime;
    }

    public function build()
    {
        return $this
            ->subject('Booking Reminder')
            ->view('emails.booking-reminder', [
                'user' => $this->user,
                'bookingDate' => $this->bookingDate,
                'startTime' => $this->startTime,
            ]);
    }
}
