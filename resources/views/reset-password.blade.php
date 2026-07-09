<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; padding: 24px; }
        .card { max-width: 480px; margin: 40px auto; background: white; padding: 24px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin-bottom: 12px; box-sizing: border-box; }
        button { padding: 10px 14px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; }
        .error { color: #dc2626; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Reset Your Password</h2>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm New Password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
