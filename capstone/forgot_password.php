<?php
session_start();
require_once __DIR__ . '/password_reset_config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } else {
        $account = null;
        $accountType = '';
        $stmt = mysqli_prepare(
            $conn,
            'SELECT user_id, email, password FROM user_accounts_tbl WHERE email = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $account = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($account) {
            $accountType = 'customer';
        } else {
            $stmt = mysqli_prepare(
                $conn,
                'SELECT account_id, email, password FROM admin_superadmin_accounts_tbl WHERE email = ? LIMIT 1'
            );
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $account = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($account) {
                $accountType = 'admin';
            }
        }

        if ($account) {
            $token = createResetToken($accountType, $account);
            $id = $accountType === 'customer' ? $account['user_id'] : $account['account_id'];

            $resetLink = getBaseUrl() . '/reset_password.php?type='
                . rawurlencode($accountType)
                . '&id=' . rawurlencode((string)$id)
                . '&token=' . rawurlencode($token);

            if (!sendPasswordResetEmail($account['email'], $resetLink)) {
                $message = 'We could not send the email right now. Please check the email settings and try again.';
            } else {
                $message = 'If that email is registered, a password reset link has been sent.';
            }
        } else {
            $message = 'If that email is registered, a password reset link has been sent.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | MITZTIANPC</title>
</head>
<body>
    <main class="card">
        <h1>Forgot Password?</h1>
        <p>Enter the email address connected to your customer, admin, or super admin account.</p>

        <?php if ($message !== ''): ?>
            <p class="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" autocomplete="email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <p><a href="index.php">Back to Login</a></p>
    </main>
</body>
</html>
