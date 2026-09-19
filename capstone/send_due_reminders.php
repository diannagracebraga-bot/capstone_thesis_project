<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer
require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

// Database
include __DIR__ . '/database/database_connection.php';

$sql = "
    SELECT
        c.customer_id,
        c.f_name,
        u.email,
        c.due_date
    FROM customer_tbl AS c

    INNER JOIN user_accounts_tbl AS u
        ON c.user_id = u.user_id

    WHERE c.due_date IN (
        CURDATE() + INTERVAL 7 DAY,
        CURDATE() + INTERVAL 2 DAY,
        CURDATE() + INTERVAL 1 DAY
    )
";

$result = mysqli_query($conn, $sql);


// Check SQL
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}


// Check if there are customers
if (mysqli_num_rows($result) == 0) {
    echo "No customers need a due date reminder today.";
    exit();
}
while ($customer = mysqli_fetch_assoc($result)) {

    $mail = new PHPMailer(true);

    try {
$mail->isSMTP();
$mail->Host       = 'smtp.hostinger.com';
$mail->SMTPAuth   = true;

$mail->Username   = 'mitztianpc_tanza.com@mitztianpctanza.com';
$mail->Password   = 'Mitztianpc_tanza05';

$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 587;

$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

        // Sender
        $mail->setFrom(
            'mitztianpc_tanza.com@mitztianpctanza.com',
            'MitztianPC Wired Internet Service'
        );


        // Customer email
        $mail->addAddress(
            $customer['email'],
            $customer['f_name']
        );


        // Calculate days remaining
        $today = new DateTime();
        $dueDate = new DateTime($customer['due_date']);

        $daysLeft = $today->diff($dueDate)->days;


        if ($daysLeft == 1) {

            $message = "
                Your internet plan payment is due
                <strong>tomorrow</strong>.
            ";

        } else {

            $message = "
                Your internet plan payment is due in
                <strong>{$daysLeft} days</strong>.
            ";
        }


        // Email format
        $mail->isHTML(true);

        $mail->Subject = 'Internet Plan Due Date Reminder';


        $mail->Body = "

            <div style='font-family: Arial, sans-serif;'>

                <h2>Payment Due Date Reminder</h2>

                <p>
                    Hello <strong>{$customer['f_name']}</strong>,
                </p>

                <p>
                    This is a reminder regarding your
                    internet plan payment.
                </p>

                <p>
                    {$message}
                </p>

                <p>
                    <strong>Due Date:</strong>
                    {$customer['due_date']}
                </p>

                <p>
                    Please settle your payment on or before
                    the due date to avoid service interruption.
                </p>

                <br>

                <p>
                    Thank you!
                </p>

                <p>
                    <strong>
                        MitztianPC Wired Internet Service
                    </strong>
                </p>

            </div>

        ";

        $mail->AltBody =
            "Hello {$customer['f_name']},

            This is a reminder that your internet plan
            payment is due on {$customer['due_date']}.

            Please settle your payment on or before
            the due date to avoid service interruption.

            Thank you!
            
            MitztianPC Wired Internet Service";


        // Send
        $mail->send();


        echo "
            Reminder successfully sent to:
            <strong>{$customer['email']}</strong>
            <br>
        ";


    } catch (Exception $e) {

        echo "
            Failed to send email to:
            <strong>{$customer['email']}</strong>
            <br>

            Error:
            {$mail->ErrorInfo}

            <br><br>
        ";
    }
}

?>