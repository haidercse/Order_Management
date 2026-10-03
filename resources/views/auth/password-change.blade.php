<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change password</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 20px; background: #f4faf1; color: #17301f; font: 16px/1.5 "Segoe UI", system-ui, sans-serif; }
        main { width: min(440px, 100%); padding: 26px; border: 1px solid #dbe8d5; border-radius: 14px; background: #fff; box-shadow: 0 8px 30px #17301f12; }
        h1 { margin: 0 0 8px; font-size: 24px; }
        p { margin: 0 0 20px; color: #5f7565; }
        label { display: block; margin: 14px 0 5px; font-weight: 650; }
        input { width: 100%; padding: 11px; border: 1px solid #dbe8d5; border-radius: 8px; font: inherit; }
        button { width: 100%; margin-top: 20px; padding: 12px; border: 0; border-radius: 8px; background: #2f9e4f; color: white; cursor: pointer; font: inherit; font-weight: 750; }
        .error { margin-top: 5px; color: #b42318; font-size: 13px; }
    </style>
</head>
<body>
<main>
    <h1>Change your password</h1>
    <p>A temporary password was issued for this account. Choose a new password to continue.</p>
    <form method="POST" action="{{ route('password.change.update') }}">
        @csrf
        <label for="current_password">Temporary/current password</label>
        <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
        @error('current_password')<div class="error">{{ $message }}</div>@enderror

        <label for="password">New password</label>
        <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
        @error('password')<div class="error">{{ $message }}</div>@enderror

        <label for="password_confirmation">Confirm new password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
        <button type="submit">Save new password</button>
    </form>
</main>
</body>
</html>
