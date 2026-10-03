<?php

session_start();
include '../database/database_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
SELECT
    c.customer_id,
    c.f_name,
    c.m_name,
    c.l_name,
    c.contact_number,
    u.email
FROM customer_tbl c
INNER JOIN user_accounts_tbl u
    ON c.user_id = u.user_id
WHERE c.user_id = '$user_id'
";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {

    $customer = mysqli_fetch_assoc($result);

    $customer_id = $customer['customer_id'];

    $full_name = $customer['f_name'] . " " .
                 $customer['m_name'] . " " .
                 $customer['l_name'];

    $contact_number = $customer['contact_number'];
    $email_address = $customer['email'];

} else {

    die("Customer information not found.");

}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $concern_type = mysqli_real_escape_string($conn, $_POST['concern_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $date = date("Y-m-d");

    $concern = $concern_type;
    $priority = "Normal";
    $status = "Pending";

    $sql = "INSERT INTO ticket_management_tbl

         ( customer_id, full_name, email_address, contact_number, concern_type, date_received, concern, description,status, date_submitted )
             VALUES( '$customer_id', '$full_name', '$email_address', '$contact_number', '$concern_type', '$date', '$concern', '$description', '$status', '$date')";

    if (mysqli_query($conn, $sql)) {

        // Get the newly created ticket ID
        $ticket_id = mysqli_insert_id($conn);

     $mail = new PHPMailer(true);

try {

    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'mitztianpc_tanza.com@mitztianpctanza.com';
    $mail->Password = 'Mitztianpc_tanza05';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Local XAMPP SSL certificate workaround
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Hostinger email that sends the ticket
    $mail->setFrom(
        'mitztianpc_tanza.com@mitztianpctanza.com',
        'MitztianPC Customer Support'
    );

    // Admin Gmail
    $mail->addAddress(
        'mitztianpctanza@gmail.com',
        'MitztianPC Admin'
    );

    /*
     * IMPORTANT:
     * When the admin clicks Reply in Gmail,
     * the reply will automatically go to the customer.
     */
    $mail->addReplyTo(
        $email_address,
        $full_name
    );

    $mail->isHTML(true);

    $mail->Subject =
        'Support Ticket #' . $ticket_id . ' - ' . $concern_type;

    $mail->Body = "
        <h2>New Customer Support Ticket</h2>

        <p><strong>Ticket ID:</strong>$ticket_id</p>
        <p> <strong>Customer Name:</strong> $full_name</p>
        <p><strong>Contact Number:</strong> $contact_number</p>
        <p> <strong>Customer Email:</strong> $email_address</p>
        <p><strong>Concern Type:</strong>$concern_type</p>
        <p><strong>Status:</strong>$status </p>
        <hr>
        <h3>Customer Message</h3>

        <p> $description</p>

        <hr>

        <p><strong>Reply to this email to communicate directly with the customer.</strong></p>
        <p>This ticket was submitted through the MitztianPC Customer Portal.
        </p>
    ";

    // Send email
    $mail->send();

    echo "<script>
        alert('Ticket submitted successfully!');
        window.location.href='customer_support.php';
    </script>";

    exit();

} catch (Exception $e) {

    echo "<h3>Email Error</h3>";

    echo "<p>" . $mail->ErrorInfo . "</p>";

    echo "<p>
        Ticket was saved successfully in the database.
    </p>";

    exit();
}

    } else {

        echo "<script>
            alert('Error: " . mysqli_error($conn) . "');
        </script>";

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Customer Support</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../css/customer_sidebar_header.css?v=10"
    >

    <link
        rel="stylesheet"
        href="../css/customer_support.css?v=6"
    >

</head>

<body>

<?php include 'customer_sidebar_header.php'; ?>

<div class="support-content">

    <div class="card">

        <div class="card-body">

            <h2>Send Customer Ticket:</h2>
            <div class="form-section">

                <form class="form-box" method="POST">
                    <div class="row">

                        <div class="input-group">

                            <label>Name:</label>

                            <input type="text" name="full_name" value="<?php echo $full_name; ?>" readonly>

                        </div>

                        <div class="input-group">
                            <label>Contact Number:</label>

                            <input type="text" name="contact_number" value="<?php echo $contact_number; ?>" readonly>
                        </div>

                    </div>

                    <div class="row">

                        <div class="input-group">

                            <label>Email Address:</label>

                            <input type="email" name="email_address" value="<?php echo $email_address; ?>" readonly>
                        </div>

                        <div class="input-group">

                            <label>Type of concern:</label>

                            <select name="concern_type" required>

                                <option value="" selected disabled> Select</option>
                                <option> Billing</option>
                                <option> Internet Connection </option>
                                <option>Internet Upgrade</option>
                                <option> Update Information</option>
                                <option> Others</option>

                            </select>

                        </div>

                    </div>

                    <div class="input-group">

                        <label>Description:</label>

                        <textarea name="description" placeholder="Describe your concern..." required ></textarea>

                    </div>
                    <button type="submit" class="submit-btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>