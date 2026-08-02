<?php
include '../database/database_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $plan = $_POST['plan'];
    $f_name = $_POST['f_name'];
    $m_name = $_POST['m_name'];
    $l_name = $_POST['l_name'];
    $payment_method = $_POST['payment_method'];
    $due_date = $_POST['due_date'];
    $remarks = $_POST['remarks'];
    $amount = $_POST['amount'];

    $query = "INSERT INTO payment_tbl
              (plan, f_name, m_name, l_name, payment_method, due_date, remarks, amount)
              VALUES
              ('$plan', '$f_name','$m_name','$l_name','$payment_method', '$due_date', '$remarks', '$amount')";

    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: admin_payment.php");
        exit();
    } else {
        echo "Insert failed: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <link rel="stylesheet" href="../css/admin_add_payment.css">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>    <?php include 'admin_sidebar_header_profile.php'; ?>
<h1>USER MANAGEMENT TRACKING</h1>
		<div class="card w-75">
  				<div class="card-body">
<div class="add_payment">
    <h3>Add Payment</h3>

    <form action="" method="post">

        <div class="form_group">
            <label>Plan</label>
            <select name="plan" required>
                <option value="">-- Select --</option>
                <option>50 mbps</option>
                <option>100 mbps</option>
                <option>150 mbps</option>
                <option>200 mbps</option>
                <option>250 mbps</option>
            </select>
        </div>
        <div class="form_grid">
            <div class="form_group">
                <label>First Name</label>
                <input type="text" name="f_name" required>
            </div>
            <div class="form_group">
                <label>Middle Name</label>
                <input type="text" name="m_name" required>
            </div>
            <div class="form_group">
                <label>Last Name</label>
                <input type="text" name="l_name" required>
            </div>
            <div class="form_group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="">-- Select --</option>
                    <option>Cash</option>
                    <option>Gcash</option>
                </select>
            </div>

            <div class="form_group">
                <label>Due Datesss</label>
                <input type="date" name="due_date" required>
            </div>

            <div class="form_group">
                <label>Remarks</label>
                <select name="remarks" required>
                    <option value="">-- Select --</option>
                    <option>Paid</option>
                    <option>Not Paid</option>
                </select>
            </div>

            <div class="form_group">
                <label>Amount</label>
                <input type="text" name="amount" required>
            </div>

            <div class="form_group full_width">
                <button type="submit" name="add" class="payment-plus">
                    Add Customer
                </button>
                
            </div>

        </div>
    </form>
</div>
</div>
</div>
</body>
</html>