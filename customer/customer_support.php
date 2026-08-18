<?php
session_start();
include '../database/database_connection.php';

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
    ON c.user_id = u.id
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
    $description  = mysqli_real_escape_string($conn, $_POST['description']);

    $date = date("Y-m-d");
    $concern = $concern_type;
    $priority = "Normal";
    $status = "Pending";

    $sql = "INSERT INTO ticket_management_tbl
    (
        customer_id,
        full_name,
        email_address,
        contact_number,
        concern_type,
        date_received,
        concern,
        description,
        priority,
        status,
        date_submitted
    )
    VALUES
    (
        '$customer_id',
        '$full_name',
        '$email_address',
        '$contact_number',
        '$concern_type',
        '$date',
        '$concern',
        '$description',
        '$priority',
        '$status',
        '$date'
    )";

 if (mysqli_query($conn, $sql)) {

    echo "<script>
        alert('Ticket submitted successfully!');
        window.location.href='customer_support.php';
    </script>";
    exit();

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/customer_sidebar_header.css?v=9">
    <link rel="stylesheet" href="../css/customer_support.css?v=6">
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
                    <label >Name:</label>
                   <input type="text"  name="full_name" value="<?php echo $full_name; ?>" readonly>
                </div>

                <div class="input-group">
                       <label >Contact Number:</label>
                 <input type="text" name="contact_number" value="<?php echo $contact_number; ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="input-group">
                       <label >Email Address:</label>
                   <input type="email"name="email_address" value="<?php echo $email_address; ?>"readonly>
                </div>

                <div class="input-group">
                    <label>Type of concern:</label>
                    <select name="concern_type" required>
                        <option value="" selected disabled>Select</option>
                        <option>Billing</option>
                        <option>Internet Connection</option>
                        <option>Internet Upgrade</option>
                        <option>Update Information</option>
                        <option>Others</option>
                    </select>
                </div>
            </div>

            <div class="input-group">
                <label>Description:</label>
                <textarea name="description" placeholder="Describe your concern..." required></textarea>
            </div>

            <button type="submit" class="submit-btn">Submit</button>
        </form>
</body>
</html>



