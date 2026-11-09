
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/database/database_connection.php';
require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';

// Password reset security secret
define('RESET_SECRET', 'REPLACE_WITH_A_NEW_LONG_RANDOM_SECRET');

// Hostinger SMTP Configuration
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_USERNAME', 'mpc@mitztianpctanza.com');
define('SMTP_PASSWORD', 'mpcTANZA05@');
define('SMTP_PORT', 587);

// Localhost URL for XAMPP
define('RESET_BASE_URL', 'https://mitztianpctanza.com');


// Create Password Reset Token
function createResetToken(string $tableType, array $account): string
{
    $id = ($tableType === 'customer')
        ? (string)$account['user_id']
        : (string)$account['account_id'];

    $data = $tableType . '|' .
        $id . '|' .
        (string)$account['email'] . '|' .
        (string)$account['password'];

    return hash_hmac('sha256', $data, RESET_SECRET);
}

// Get Base URL
function getBaseUrl(): string
{
    return rtrim(RESET_BASE_URL, '/');
}

// Send Password Reset Email
function sendPasswordResetEmail(string $email, string $resetLink): bool
{
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mpc@mitztianpctanza.com';
        $mail->Password   = 'mpcTANZA05@';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->CharSet    = 'UTF-8';
        $mail->SMTPDebug  = 0;

           $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

        $mail->setFrom(SMTP_USERNAME, 'MITZTIANPC Password Reset');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your MITZTIANPC Password';

        $safeLink = htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8');

        $mail->Body = '
            <div style="font-family:Arial,sans-serif;line-height:1.6">
                <h2>Password Reset</h2>
                <p>We received a request to reset your MITZTIANPC password.</p>
                <p>
                    <a href="' . $safeLink . '"
                       style="display:inline-block;background:#1875e8;color:#fff;
                       padding:12px 20px;text-decoration:none;border-radius:5px">
                       Reset Password
                    </a>
                </p>
                <p>If you did not request this, you can ignore this email.</p>
            </div>';

        $mail->AltBody = 'Reset your MITZTIANPC password using this link: ' . $resetLink;

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log('PHPMailer password reset error: ' . $mail->ErrorInfo);

        return false;
    }
}
