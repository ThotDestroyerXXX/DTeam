<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Your publisher account</title>
</head>

<body>
    <p>Hi {{ $user->nickname ?? $user->email }},</p>
    <p>An admin has created a publisher account for you.</p>
    <p>Login details:</p>
    <ul>
        <li>Email: {{ $user->email }}</li>
        <li>Password: <strong>{{ $password }}</strong></li>
    </ul>
    <p>Please login and change your password as soon as possible.</p>
    <p>Thanks,<br />The Team</p>
</body>

</html>
