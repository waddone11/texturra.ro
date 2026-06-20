<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingReminder;

class SendBookingReminderEmail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $bookingDate;
    public $startTime;

    public function __construct($user, $bookingDate, $startTime)
    {
        $this->user = $user;
        $this->bookingDate = $bookingDate;
        $this->startTime = $startTime;
    }

    public function handle()
    {
        Mail::to($this->user->email)->send(new BookingReminder($this->user, $this->bookingDate, $this->startTime));
    }
}
