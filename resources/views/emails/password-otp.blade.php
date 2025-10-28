<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Password Reset OTP</title>
</head>

<body>
    <p>Hi {{ $user->name ?? $user->email }},</p>
    <p>Your password reset OTP is: <strong>{{ $otp }}</strong></p>
    <p>This code will expire shortly. If you didn't request a password reset, please ignore this email.</p>
    <p>Thanks,<br />The Team</p>
</body>

</html>
