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

// Add the optional attachment field when upgrading an existing installation.
$attachment_column = mysqli_query($conn, "SHOW COLUMNS FROM ticket_management_tbl LIKE 'attachment_path'");
if ($attachment_column && mysqli_num_rows($attachment_column) === 0) {
    mysqli_query($conn, "ALTER TABLE ticket_management_tbl ADD attachment_path VARCHAR(255) NULL");
}

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
$form_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $concern_type = trim($_POST['concern_type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $attachment_path = null;
    $stored_file = null;

    if ($concern_type === '' || $description === '') {
        $form_error = 'Punan ang concern at description bago isumite.';
    }

    if ($form_error === '' && isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = $_FILES['attachment'];
        $allowed_types = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
            'gif' => 'image/gif', 'webp' => 'image/webp', 'pdf' => 'application/pdf',
        ];
        $extension = strtolower(pathinfo($upload['name'] ?? '', PATHINFO_EXTENSION));
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = $file_info ? finfo_file($file_info, $upload['tmp_name']) : '';
        if ($file_info) finfo_close($file_info);

        if ($upload['error'] !== UPLOAD_ERR_OK) {
            $form_error = 'Hindi na-upload ang file. Subukan ulit.';
        } elseif ($upload['size'] > 5 * 1024 * 1024) {
            $form_error = 'Hanggang 5 MB lang ang puwedeng i-attach.';
        } elseif (!isset($allowed_types[$extension]) || $allowed_types[$extension] !== $mime_type) {
            $form_error = 'JPG, PNG, GIF, WEBP, o PDF lang ang puwedeng i-attach.';
        } else {
            $upload_directory = __DIR__ . '/../uploads/ticket_attachments';
            if (!is_dir($upload_directory) && !mkdir($upload_directory, 0755, true) && !is_dir($upload_directory)) {
                $form_error = 'Hindi maihanda ang paglalagyan ng attachment.';
            } else {
                $stored_name = bin2hex(random_bytes(16)) . '.' . $extension;
                $stored_file = $upload_directory . '/' . $stored_name;
                if (move_uploaded_file($upload['tmp_name'], $stored_file)) {
                    $attachment_path = 'uploads/ticket_attachments/' . $stored_name;
                } else {
                    $stored_file = null;
                    $form_error = 'Hindi na-save ang attachment. Subukan ulit.';
                }
            }
        }
    }

    $date = date("Y-m-d");

    $concern = $concern_type;
    $priority = "Normal";
    $status = "Pending";

    $full_name = trim($full_name);
    $ticket_statement = null;
    if ($form_error === '') {
        $ticket_sql = "INSERT INTO ticket_management_tbl
            (customer_id, full_name, email_address, contact_number, concern_type, date_received, concern, description, status, date_submitted, attachment_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $ticket_statement = mysqli_prepare($conn, $ticket_sql);
        if ($ticket_statement) {
            mysqli_stmt_bind_param($ticket_statement, 'issssssssss', $customer_id, $full_name, $email_address, $contact_number, $concern_type, $date, $concern_type, $description, $status, $date, $attachment_path);
        } else {
            $form_error = 'Hindi naisumite ang ticket. Subukan ulit mamaya.';
        }
    }

    if ($ticket_statement && mysqli_stmt_execute($ticket_statement)) {

        // Get the newly created ticket ID
        $ticket_id = mysqli_stmt_insert_id($ticket_statement);
        mysqli_stmt_close($ticket_statement);

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

        <p><strong>Ticket ID:</strong> $ticket_id</p>
        <p><strong>Customer Name:</strong> " . htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') . "</p>
        <p><strong>Contact Number:</strong> " . htmlspecialchars($contact_number, ENT_QUOTES, 'UTF-8') . "</p>
        <p><strong>Customer Email:</strong> " . htmlspecialchars($email_address, ENT_QUOTES, 'UTF-8') . "</p>
        <p><strong>Concern Type:</strong> " . htmlspecialchars($concern_type, ENT_QUOTES, 'UTF-8') . "</p>
        <p><strong>Status:</strong> " . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . "</p>
        <hr>
        <h3>Customer Message</h3>

        <p>" . nl2br(htmlspecialchars($description, ENT_QUOTES, 'UTF-8')) . "</p>
        " . ($attachment_path ? '<p><strong>Attachment:</strong> Available in the admin ticket details.</p>' : '') . "

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
        if ($ticket_statement) mysqli_stmt_close($ticket_statement);
        if ($stored_file && is_file($stored_file)) unlink($stored_file);
        if ($form_error === '') $form_error = 'Hindi naisumite ang ticket. Subukan ulit mamaya.';
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
        href="../css/customer_sidebar_header.css?v=customer-shell-11"
    >

    <link
        rel="stylesheet"
        href="../css/customer_support.css?v=attachment-1"
    >

</head>

<body>

<?php include 'customer_sidebar_header.php'; ?>

<div class="support-content">

    <div class="card">

        <div class="card-body">

            <h2>Send Customer Ticket:</h2>
            <div class="form-section">

                <?php if ($form_error !== ''): ?><div class="support-form-error" role="alert"><?php echo htmlspecialchars($form_error); ?></div><?php endif; ?>

                <form class="form-box" method="POST" enctype="multipart/form-data">
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
                    <div class="input-group attachment-group">
                        <label for="ticket-attachment">Attach file or image (optional):</label>
                        <input id="ticket-attachment" type="file" name="attachment" accept="image/jpeg,image/png,image/gif,image/webp,application/pdf">
                    </div>
                    <button type="submit" class="submit-btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
