<?php

declare(strict_types=1);

require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// Local XAMPP database connection
$pdo = new PDO(
    'mysql:host=localhost;dbname=mitztianpc_wired_internet_services;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

$today = new DateTimeImmutable('today');
$todayDate = $today->format('Y-m-d');
$sevenDaysDate = $today->modify('+7 days')->format('Y-m-d');

foreach ($customers as $customer) {
    $mail->clearAddresses();

    $mail->addAddress(
        $customer['customer_email'],
        $customer['customer_name']
    );

    $mail->isHTML(true);
    $mail->Subject = 'Payment Reminder';
    $mail->Body = "
        <p>Hello {$customer['customer_name']},</p>
        <p>This is a reminder that your internet service payment is due on
        <strong>{$customer['due_date']}</strong>.</p>
        <p>Please settle your payment on or before the due date.</p>
        <p>Thank you.</p>
    ";

    $mail->AltBody =
        "Hello {$customer['customer_name']}, your payment is due on "
        . $customer['due_date'] . '.';

    $mail->send();
}
