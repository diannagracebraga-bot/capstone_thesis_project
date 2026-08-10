<?php
include '../database/database_connection.php';
if(isset($_GET['id'])){

    $id = $_GET['id'];

    $query = "SELECT * FROM payment_tbl WHERE id='$id'";
    $result = mysqli_query($conn,$query);

    $row = mysqli_fetch_assoc($result);
}
if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $plan = $_POST['plan'];
    $f_name = $_POST['f_name'];
    $m_name = $_POST['m_name'];
    $l_name = $_POST['l_name'];
    $payment_method = $_POST['payment_method'];
    $due_date = $_POST['due_date'];
    $amount = $_POST['amount'];
    $remarks = $_POST['remarks'];

    $query = "UPDATE payment_tbl SET plan='$plan', f_name='$f_name', m_name='$m_name', l_name='$l_name', payment_method='$payment_method', due_date='$due_date', amount='$amount', remarks='$remarks' WHERE id='$id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Record updated successfully.'); window.location.href='../admin/admin_payment.php';</script>";
    } else {
        echo "<script>alert('Error updating record: " . mysqli_error($conn) . "'); window.location.href='../admin/admin_payment.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>UPDATE PAYMENT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_update_payment.css">

    
</head>
<body>
<div class="container mt-5">
     <?php include 'admin_sidebar_header_profile.php'; ?>

<h1>USER MANAGEMENT TRACKING</h1>
<div class="card">
    <div class="card-body">
<form method="POST">

    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<div class="row">
    <div class="col-md-4 mb-3">
        <label>First Name</label>
        <input  type="text" name="f_name" class="form-control" value="<?php echo $row['f_name']; ?>"  required >
    </div>

    <div class="col-md-4 mb-3">
        <label>Middle Name</label>
        <input type="text" name="m_name" class="form-control"value="<?php echo $row['m_name']; ?>" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>Last Name</label>
        <input type="text"  name="l_name"  class="form-control" value="<?php echo $row['l_name']; ?>"  required >
    </div>

</div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label>Plan</label>
            <select name="plan" class="form-control" required>
                <option value="50 mbps" <?php if($row['plan']=="50 mbps") echo "selected"; ?>>50 mbps</option>
                <option value="100 mbps" <?php if($row['plan']=="100 mbps") echo "selected"; ?>>100 mbps</option>
                <option value="150 mbps" <?php if($row['plan']=="150 mbps") echo "selected"; ?>>150 mbps</option>
                <option value="200 mbps" <?php if($row['plan']=="200 mbps") echo "selected"; ?>>200 mbps</option>
                <option value="250 mbps" <?php if($row['plan']=="250 mbps") echo "selected"; ?>>250 mbps</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label>Payment Method</label>
            <select name="payment_method" class="form-control" required>
                <option value="Cash" <?php if($row['payment_method']=="Cash") echo "selected"; ?>>Cash</option>
                <option value="Gcash" <?php if($row['payment_method']=="Gcash") echo "selected"; ?>>Gcash</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label>Due Date</label>
            <input type="date" name="due_date" class="form-control"value="<?php echo $row['due_date']; ?>" required  >
        </div>
    </div>
    <div class="row">

        <div class="col-md-6 mb-3">
            <label>Amount</label>
            <input type="text" name="amount" class="form-control"value="<?php echo $row['amount']; ?>" required >
        </div>
        <div class="col-md-6 mb-3">
            <label>Remarks</label>
            <select name="remarks" class="form-control" required>
                <option value="Paid" <?php if($row['remarks']=="Paid") echo "selected"; ?>>
                    Paid
                </option>

                <option value="Not Paid" <?php if($row['remarks']=="Not Paid") echo "selected"; ?>>
                    Not Paid
                </option>
            </select>
        </div>

    </div>
    <div class="mt-3">

        <button type="submit" name="update" class="btn btn-primary" >
            Update Payment
        </button>

        <a href="../admin/admin_payment.php" class="btn btn-secondary" >
            Cancel
        </a>
    </div>

</form>
</div>
</div>
</div>
</body>
</html>