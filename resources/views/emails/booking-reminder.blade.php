<!DOCTYPE html>
<html>
<head>
    <title>Booking Reminder</title>
</head>
<body>
<h1>Hi {{ $user->name }},</h1>
<p>This is a friendly reminder about your booking:</p>
<ul>
    <li>Date: {{ $bookingDate }}</li>
    <li>Time: {{ $startTime }}</li>
</ul>
<p>We look forward to seeing you!</p>
</body>
</html>
