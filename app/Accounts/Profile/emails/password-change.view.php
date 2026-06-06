<?php

declare(strict_types=1);

/**
 * @var string $url   The signed confirmation URL
 * @var \App\Accounts\User $user
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Change your password</title>
    <style>
        body { font-family: sans-serif; background: #fdf8f0; margin: 0; padding: 40px 16px; color: #2d2a26; }
        .card { max-width: 520px; margin: 0 auto; background: #fff; border-radius: 24px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,.06); }
        h1 { font-size: 1.5rem; font-weight: 800; margin: 0 0 16px; color: #1a1714; }
        p { font-size: 0.9rem; line-height: 1.6; margin: 0 0 20px; color: #6b6560; }
        .btn { display: inline-block; background: #f97056; color: #fff; text-decoration: none; font-weight: 700; font-size: 0.9rem; padding: 14px 28px; border-radius: 14px; }
        .footer { margin-top: 32px; font-size: 0.75rem; color: #a09893; }
        .url { word-break: break-all; color: #6b6560; font-size: 0.75rem; }
    </style>
</head>
<body>
<div class="card">
    <h1>Change your password</h1>
    <p>Hi <?= htmlspecialchars($user->name ?: $user->email) ?>,</p>
    <p>We received a request to change the password on your Ducky account. Click the button below to set a new password. This link expires in <strong>1 hour</strong>. All active sessions will be signed out once the change is made.</p>
    <p><a href="<?= htmlspecialchars(
        $url,
    ) ?>" class="btn">Change Password</a></p>
    <p>If you didn't request this, please change your password immediately and contact support.</p>
    <div class="footer">
        <p>Or copy this link into your browser:</p>
        <p class="url"><?= htmlspecialchars($url) ?></p>
    </div>
</div>
</body>
</html>
