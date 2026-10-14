<?php

include '../database/database_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {

    $applicant_id = $_POST['applicant_id'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    /* Get applicant information */

    $query = "SELECT 
                applicant_id,
                first_name,
                last_name,
                email
              FROM internet_application_tbl
              WHERE applicant_id='$applicant_id'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {

        $row = mysqli_fetch_assoc($result);

        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $email = $row['email'];

        /* Update status and remarks */

        $update_sql = "UPDATE internet_application_tbl
                       SET status='$status',
                           remarks='$remarks'
                       WHERE applicant_id='$applicant_id'";

        if (mysqli_query($conn, $update_sql)) {

            /* Send email */

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

                $mail->setFrom(
                    'mpc@mitztianpctanza.com',
                    'MitztianPC Wired Internet Services'
                );
                $mail->addReplyTo(
                     'mitztianpctanza@gmail.com',
                     'MitztianPC Wired Internet Services'
                    );


                $mail->addAddress($email, $first_name . ' ' . $last_name);

                $mail->isHTML(true);

                $mail->Subject = 'Internet Application Status Update';

                $mail->Body = '
                    <div style="font-family: Arial, sans-serif;">

                        <h2>MitztianPC Wired Internet Services</h2>

                        <p>Dear <strong>' . $first_name . ' ' . $last_name . '</strong>,</p>

                        <p>
                            We would like to inform you that there has been an update
                            regarding your internet application.
                        </p>

                        <p>
                            <strong>Reference Number:</strong> ' . $applicant_id . '<br>
                            <strong>Status:</strong> ' . $status . '
                        </p>

                        <p>
                            <strong>Remarks:</strong>
                        </p>

                        <div style="
                            background:#f5f5f5;
                            padding:15px;
                            border-left:4px solid #0d6efd;
                            margin-bottom:20px;
                        ">
                            ' . nl2br($remarks) . '
                        </div>

                        <p>
                            Please keep your reference number for future inquiries.
                        </p>

                        <p>
                            Thank you for choosing
                            <strong>MitztianPC Wired Internet Services</strong>.
                        </p>

                    </div>
                ';

                $mail->send();

                echo "<script>
                        alert('Application status and remarks submitted successfully! The applicant has been notified through email.');
                        window.location='../admin/admin_applicants.php';
                      </script>";

            } catch (Exception $e) {

                echo "<script>
                        alert('Application was updated, but the email could not be sent. Please check the email settings.');
                        window.location='../admin/admin_applicants.php';
                      </script>";
            }

        } else {

            echo "<script>
                    alert('Failed to update application.');
                    window.location='../admin/admin_applicants.php';
                  </script>";
        }

    } else {

        echo "<script>
                alert('Applicant not found.');
                window.location='../admin/admin_applicants.php';
              </script>";
    }
}
?>