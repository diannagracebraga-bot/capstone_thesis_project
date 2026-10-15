<?php
session_start();
require_once __DIR__ . '/password_reset_config.php';

$message = '';
$validLink = false;
$account = null;
$type = $_GET['type'] ?? $_POST['type'] ?? '';
$idRaw = $_GET['id'] ?? $_POST['id'] ?? '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

$id = filter_var($idRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!in_array($type, ['customer', 'admin'], true) || !$id ||
    !is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $message = 'This password reset link is invalid.';
} else {
    if ($type === 'customer') {
        $stmt = mysqli_prepare(
            $conn,
            'SELECT user_id, email, password FROM user_accounts_tbl WHERE user_id = ? LIMIT 1'
        );
    } else {
        $stmt = mysqli_prepare(
            $conn,
            'SELECT account_id, email, password FROM admin_superadmin_accounts_tbl WHERE account_id = ? LIMIT 1'
        );
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $account = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($account) {
        $expectedToken = createResetToken($type, $account);
        $validLink = hash_equals($expectedToken, $token);
    }

    if (!$validLink) {
        $message = 'This password reset link is invalid or has already been used.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validLink) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {
        $message = 'Your password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $message = 'The passwords do not match.';
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if ($type === 'customer') {
            $stmt = mysqli_prepare(
                $conn,
                'UPDATE user_accounts_tbl SET password = ? WHERE user_id = ?'
            );
        } else {
            $stmt = mysqli_prepare(
                $conn,
                'UPDATE admin_superadmin_accounts_tbl SET password = ? WHERE account_id = ?'
            );
        }

        mysqli_stmt_bind_param($stmt, 'si', $passwordHash, $id);
        $updated = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($updated) {
            // The token is derived from the old password hash, so it is now invalid.
            $validLink = false;
            $message = 'Your password has been reset successfully. You can now log in.';
        } else {
            $message = 'Unable to update your password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | MITZTIANPC</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:#f3f7fc;font-family:Arial,sans-serif;color:#173b68}
        .card{width:100%;max-width:420px;padding:30px;background:#fff;border-radius:12px;box-shadow:0 8px 28px rgba(23,59,104,.12)}
        h1{margin-top:0;font-size:25px}
        p{line-height:1.5;color:#52657a;overflow-wrap:anywhere}
        input,button{display:block;width:100%;padding:12px 14px;margin-top:12px;border-radius:6px;font-size:15px}
        input{border:1px solid #cbd5e1}
        button{border:0;background:#1875e8;color:#fff;font-weight:bold;cursor:pointer}
        a{color:#1875e8}
    </style>
</head>
<body>
    <main class="card">
        <h1>Reset Password</h1>

        <?php if ($message !== ''): ?>
            <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if ($validLink): ?>
            <form method="post" action="">
                <input type="hidden" name="type" value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= (int)$id ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

                <label for="password">New password</label>
                <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" required>

                <label for="confirm_password">Confirm new password</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="8" autocomplete="new-password" required>

                <button type="submit">Update Password</button>
            </form>
        <?php else: ?>
            <p><a href="index.php">Back to Login</a></p>
        <?php endif; ?>
    </main>
</body>
</html>
