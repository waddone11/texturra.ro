<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeBookingReminder;

class SendBookingReminderEmailToEmployee implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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

    public function handle()
    {
        Mail::to($this->employee->email)->send(new EmployeeBookingReminder(
            $this->employee,
            $this->user,
            $this->bookingDate,
            $this->startTime
        ));
    }
}
