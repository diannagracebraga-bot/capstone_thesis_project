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

        // Check customer accounts first.
        $stmt = mysqli_prepare(
            $conn,
            'SELECT user_id, email, password FROM user_accounts_tbl WHERE email = ? LIMIT 1'
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                $account = mysqli_fetch_assoc($result);
            }
            mysqli_stmt_close($stmt);
        }

        if ($account) {
            $accountType = 'customer';
        } else {
            // Admin and superadmin accounts are stored in this shared table.
            $stmt = mysqli_prepare(
                $conn,
                'SELECT account_id, email, password FROM admin_superadmin_accounts_tbl WHERE email = ? LIMIT 1'
            );

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if ($result) {
                    $account = mysqli_fetch_assoc($result);
                }
                mysqli_stmt_close($stmt);
            }

            if ($account) {
                $accountType = 'admin';
            }
        }

        // Use the same generic message whether the email exists or not.
        if ($account) {
            $token = createResetToken($accountType, $account);
            $id = ($accountType === 'customer')
                ? $account['user_id']
                : $account['account_id'];

            $resetLink = getBaseUrl() . '/reset_password.php?' .
                http_build_query(
                    [
                        'type'  => $accountType,
                        'id'    => (string)$id,
                        'token' => $token
                    ],
                    '',
                    '&',
                    PHP_QUERY_RFC3986
                );

            if (sendPasswordResetEmail($account['email'], $resetLink)) {
                $message = 'If that email is registered, a password reset link has been sent.';
            } else {
                $message = 'We could not send the email right now. Please check the email settings and try again.';
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
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center;
            justify-content: center; padding: 20px; background: #f3f7fc;
            font-family: Arial, sans-serif; color: #173b68;
        }
        .card {
            width: 100%; max-width: 420px; padding: 30px; background: #fff;
            border-radius: 12px; box-shadow: 0 8px 28px rgba(23,59,104,.12);
        }
        h1 { margin-top: 0; font-size: 25px; }
        p { line-height: 1.5; color: #52657a; overflow-wrap: anywhere; }
        label { display: block; margin-top: 18px; }
        input, button {
            display: block; width: 100%; padding: 12px 14px; margin-top: 10px;
            border-radius: 6px; font-size: 15px;
        }
        input { border: 1px solid #cbd5e1; }
        button { border: 0; background: #1875e8; color: #fff; font-weight: bold; cursor: pointer; }
        a { color: #1875e8; }
        .message { padding: 10px; background: #f3f7fc; border-radius: 6px; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Forgot Password?</h1>
        <p>Enter the email address connected to your customer, admin, or superadmin account.</p>

        <?php if ($message !== ''): ?>
            <p class="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" autocomplete="email" required>
            <button type="submit">Send Reset Link</button>
        </form>

        <p><a href="index.php">Back to Login</a></p>
    </main>
</body>
</html>
