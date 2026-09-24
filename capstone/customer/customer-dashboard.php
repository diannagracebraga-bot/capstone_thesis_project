<?php
session_start();
include '../database/database_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "customer") {
    header("Location: ../index.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT
            c.*,
            p.plan_name,
            p.internet_price,
            p.internet_mbps
        FROM customer_tbl c
        INNER JOIN internet_plan_tbl p
            ON c.internet_plan = p.plan_id
        WHERE c.user_id = '$user_id'";

$result = mysqli_query($conn, $sql);
$customer = mysqli_fetch_assoc($result);

$payment_sql = "SELECT 
                    p.id,
                    p.amount,
                    p.payment_method,
                    p.payment_status,
                    p.created_at
                FROM payment_tbl p
                WHERE p.user_id = '$user_id'
                ORDER BY p.id ASC";

$payment_result = mysqli_query($conn, $payment_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/customer_sidebar_header.css?v=9">
<link rel="stylesheet" href="../css/customer_dashboard.css">

</head>
<body>
<?php include 'customer_sidebar_header.php'; ?>
<div class="content">
    <div class="card w-100">
  				<div class="card-body">

    <div class="top-section">
      <div class="welcome">
    <h2>
        Welcome, 
        <?php echo $customer['f_name'] . ' ' . $customer['m_name'] . ' ' . $customer['l_name']; ?>!
    </h2>

    <h2>
    <strong>Account No:</strong>
    <?php echo $customer['account_number']; ?>
</h2>
</div>
        <div class="right-top">
            <div class="card">
                <h3>Today is:</h3>
             <h4><?php echo date('l, F d, Y');
                ?></h4>
            </div>
            <div class="card">
                <h3>Current Plan:</h3>
              <h4> ₱<?php echo number_format($customer['internet_price']); ?>
                <?php echo $customer['internet_mbps']; ?> Mbps </h4>
            </div>

        </div>
    </div>
    <div class="summary">
     
        <div class="card">
            <h4>Service Status</h4>
            <span class="<?php echo ($customer['connection_status'] == 'Connected') ? 'status-connected' : 'status-disconnected'; ?>">
                 ● <?php echo $customer['connection_status']; ?>
</span>
        </div>
    
        <div class="card">
            <h4>Next Due Date</h4>
            <span>
        <?php echo date('F d, Y', strtotime($customer['due_date'])); ?>
    </span>
        </div>
<button type="button" class="payment-btn" data-bs-toggle="modal" data-bs-target="#paymentModal">
    Payment
</button>   
    </div>

        <table>
                   <thead>
            <tr>
                <th>No.</th>
                <th>Date of Payment</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>SOA</th>
            </tr>
        </thead>
         <tbody>
               <?php
if (mysqli_num_rows($payment_result) > 0) {

    while ($payment = mysqli_fetch_assoc($payment_result)) {
?>

<tr>
    <td><?php echo $payment['id']; ?></td>

    <td>
        <?php echo date('m-d-Y', strtotime($payment['created_at'])); ?>
    </td>

    <td>
        ₱<?php echo number_format($payment['amount'], 2); ?>
    </td>

    <td>
        <?php echo $payment['payment_method']; ?>
    </td>

    <td>
        <?php echo $payment['payment_status']; ?>
    </td>

    <td>
        <button class="download">
            Download
        </button>
    </td>
</tr>

<?php
    }

} else {
?>

<tr>
    <td colspan="6" style="text-align: center;">
        No payment history found.
    </td>
</tr>

<?php
}
?>

            </tbody>

        </table>
</div>
</div>
    </div>

</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">
                    QRPH Payment
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">

                <iframe
                    src="customer_payment.php"
                    style="width: 100%; height: 600px; border: none;"
                    title="QRPH Payment">
                </iframe>

            </div>

        </div>

    </div>

</div>
</body>
</html>
