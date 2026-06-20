<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeBookingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $user;
    public $bookingDate;
    public $startTime;

    public function __construct($employee, $user, $bookingDate, $startTime)
    {
        $this->employee = $employee;
        $this->user = $user;
        $this->bookingDate = $bookingDate;
        $this->startTime = $startTime;
    }

    public function build()
    {
        return $this
            ->subject('Booking Reminder')
            ->view('emails.employee-booking-reminder', [
                'employee' => $this->employee,
                'user' => $this->user,
                'bookingDate' => $this->bookingDate,
                'startTime' => $this->startTime,
            ]);
    }
}
