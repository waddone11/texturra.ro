<!DOCTYPE html>
<html>
<head>
    <title>Your Account Has Been Created</title>
</head>
<body>
<h1>Welcome, {{ $user->name }}!</h1>
<p>Your account has been successfully created. You can log in using the following credentials:</p>
<ul>
    <li>Email: {{ $user->email }}</li>
    <li>Password: {{ $password }}</li>
</ul>
<p>Please <a href="{{ route('login') }}">log in</a> to activate your account and view your booking details.</p>
<p>Thank you!</p>
</body>
</html>
