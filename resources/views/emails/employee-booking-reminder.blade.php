<!DOCTYPE html>
<html>
<head>
    <title>Booking Reminder</title>
</head>
<body>
<h1>Booking Reminder</h1>
<p>Hello {{ $employee->name }},</p>
<p>You have a booking scheduled with {{ $user->name }}:</p>
<ul>
    <li><strong>Date:</strong> {{ $bookingDate }}</li>
    <li><strong>Time:</strong> {{ $startTime }}</li>
    <li><strong>Client Contact:</strong> {{ $user->email }} | {{ $user->phone }}</li>
</ul>
<p>Thank you!</p>
</body>
</html>
