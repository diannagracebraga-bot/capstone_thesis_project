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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/customer_sidebar_header.css?v=4">
<link rel="stylesheet" href="../css/customer_dashboard.css?v=2">

</head>
<body>
<?php include 'customer_sidebar_header.php'; ?>
<div class="content">
    <div class="card w-100">
  				<div class="card-body">

    <div class="top-section">
        <div class="welcome">
            <h2>Welcome, Dianna Braga</h2>
            <p><strong>Account No:</strong> MTZ-10027</p>
        </div>
        <div class="right-top">
            <div class="card">
                <p>Tuesday</p>  
                <h3>July 8, 2026</h3>
            </div>
            <div class="card">
                <h3>Current Plan</h3>
               ₱<?php echo number_format($customer['internet_price']); ?>
                <?php echo $customer['internet_mbps']; ?> Mbps
            </div>

        </div>
    </div>
    <div class="summary">
     
        <div class="card">
            <h4>Service Status</h4>
            <span class="online">● Connected</span>
        </div>
    
        <div class="card">
            <h4>Next Due Date</h4>
            <span>July 15, 2026</span>
        </div>
    </div>

    <div class="payment-box">
        <div class="payment-header">
            <h3>Payment History</h3>
            <input
                type="text"
                placeholder="Search payment..."
            >
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Payment</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>SOA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>027</td>
                    <td>05-12-2026</td>
                    <td>₱800</td>
                    <td>Cash</td>
                    <td>Paid</td>
                    <td>
                        <button class="download">
                            Download
                        </button>
                    </td>
                </tr>

                <tr>

                    <td>028</td>
                    <td>04-12-2026</td>
                    <td>₱800</td>
                    <td>QR PH</td>
                    <td>Paid</td>

                    <td>
                        <button class="download">
                            Download
                        </button>
                    </td>

                </tr>

            </tbody>

        </table>
</div>
</div>
    </div>

</div>
</body>
</html>
