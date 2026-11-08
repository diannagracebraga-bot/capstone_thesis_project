<?php
include '../database/database_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

if (isset($_POST['update_status'])) {

    $ticket_id = mysqli_real_escape_string($conn, $_POST['ticket_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    $sql = "UPDATE ticket_management_tbl
            SET status='$status',
                remarks='$remarks'
            WHERE ticket_id='$ticket_id'";

    if (mysqli_query($conn, $sql)) {

        $customer_sql = "SELECT full_name, email_address, concern_type
                         FROM ticket_management_tbl
                         WHERE ticket_id='$ticket_id'";

        $customer_result = mysqli_query($conn, $customer_sql);
        $customer = mysqli_fetch_assoc($customer_result);

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mpc@mitztianpctanza.com';
            $mail->Password = 'mpcTANZA05@';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;


          $mail->SMTPOptions = array(
                    'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                     )
                    );

            $mail->setFrom('mpc@mitztianpctanza.com', 'MitztianPC Customer Support');
            $mail->addAddress($customer['email_address'], $customer['full_name']);

            $mail->addReplyTo(
                 'mitztianpctanza@gmail.com',
                 'MitztianPC Wired Internet Services'
                );

            $mail->isHTML(true);
            $mail->Subject = 'Update Regarding Your Support Ticket';

            $mail->Body = "
                <h2>MitztianPC Customer Support</h2>

                <p>Dear " . htmlspecialchars($customer['full_name']) . ",</p>

                <p>Your support ticket has been updated by our admin.</p>

                <p><strong>Ticket ID:</strong> " . htmlspecialchars($ticket_id) . "</p>

                <p><strong>Concern Type:</strong> " . htmlspecialchars($customer['concern_type']) . "</p>

                <p><strong>Ticket Status:</strong> " . htmlspecialchars($status) . "</p>

                <p><strong>Admin Remarks:</strong></p>

                <p>" . nl2br(htmlspecialchars($remarks)) . "</p>

                <p>Thank you for contacting MitztianPC.</p>

                <p>Best regards,<br>
                <strong>MitztianPC Customer Support Team</strong></p>
            ";

            $mail->send();

            echo "<script>
                    alert('Ticket Status and Remarks Updated Successfully! Email Notification Sent.');
                    window.location='../admin/admin_ticket_management.php';
                  </script>";

            exit();

        } catch (Exception $e) {

            echo "<script>
                    alert('Ticket Status and Remarks Updated Successfully, but Email Notification Failed.');
                    window.location='../admin/admin_ticket_management.php';
                  </script>";

            exit();
        }

    } else {

        echo "Error: " . mysqli_error($conn);
    }
}
?>