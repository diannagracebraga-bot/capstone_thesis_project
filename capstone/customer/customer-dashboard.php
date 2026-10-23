<?php
session_start();
include '../database/database_connection.php';
require_once '../database/announcements_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "customer") {
    header("Location: ../index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
ensureAnnouncementsTable($conn);

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
                ORDER BY p.created_at DESC, p.id DESC";

$payment_result = mysqli_query($conn, $payment_sql);
$payment_count = $payment_result ? mysqli_num_rows($payment_result) : 0;
$announcement_result = mysqli_query($conn, 'SELECT announcement_id, title, message, created_at FROM announcements_tbl WHERE is_active = 1 ORDER BY created_at DESC, announcement_id DESC LIMIT 5');
$due_date_value = trim((string) ($customer['due_date'] ?? ''));
$due_date_valid = $due_date_value !== '' && $due_date_value !== '0000-00-00' && strtotime($due_date_value) !== false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="/favicon.png">
<link rel="stylesheet" href="../css/customer_sidebar_header.css?v=customer-shell-12">
<link rel="stylesheet" href="../css/customer_dashboard.css?v=customer-dashboard-mobile-soa-5">

</head>
<body>
<?php include 'customer_sidebar_header.php'; ?>
<div class="content">
    <section class="customer-welcome-banner" aria-label="Customer welcome">
        <div class="customer-welcome-copy">
            <p class="customer-welcome-eyebrow">Welcome back,</p>
            <h1><?php echo htmlspecialchars(trim(($customer['f_name'] ?? '') . ' ' . ($customer['m_name'] ?? '') . ' ' . ($customer['l_name'] ?? ''))); ?>!</h1>
            <p class="customer-welcome-subtitle">Here's an overview of your internet account.</p>
            <div class="customer-account-chip"><i class="bi bi-person-vcard-fill" aria-hidden="true"></i><span>Account No.</span><strong><?php echo htmlspecialchars($customer['account_number'] ?? ''); ?></strong></div>
        </div>
        <div class="customer-welcome-art" aria-hidden="true">
            <span class="customer-wifi-orbit customer-wifi-orbit-one"></span>
            <span class="customer-wifi-orbit customer-wifi-orbit-two"></span>
            <i class="bi bi-router-fill"></i>
            <i class="bi bi-wifi customer-wifi-signal"></i>
        </div>
    </section>
    <div class="card w-100">
  				<div class="card-body">

    <div class="top-section">
        <div class="right-top">
            <div class="card date-card">
                <h3>Today is:</h3>
             <h4><?php echo date('l, F d, Y');
                ?></h4>
            </div>
            <div class="card plan-card">
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
        <?php echo $due_date_valid ? date('F d, Y', strtotime($due_date_value)) : 'Not set'; ?>
    </span>
        </div>
<button type="button" class="payment-btn" data-bs-toggle="modal" data-bs-target="#paymentModal">
    Payment
</button>   
</div>

    <section class="customer-announcements" aria-labelledby="customer-announcements-title">
        <div class="customer-announcements-heading">
            <span class="customer-announcement-icon"><i class="bi bi-megaphone-fill" aria-hidden="true"></i></span>
            <div><h3 id="customer-announcements-title">Announcements</h3><p>Updates from MITZTIANPC</p></div>
        </div>
        <div class="customer-announcement-list">
            <?php if ($announcement_result && mysqli_num_rows($announcement_result) > 0): ?>
                <?php while ($announcement = mysqli_fetch_assoc($announcement_result)): ?>
                    <article class="customer-announcement-item" id="announcement-<?php echo (int) $announcement['announcement_id']; ?>">
                        <div class="customer-announcement-date"><i class="bi bi-calendar3" aria-hidden="true"></i> <?php echo htmlspecialchars(date('F d, Y', strtotime($announcement['created_at']))); ?></div>
                        <h4><?php echo htmlspecialchars($announcement['title']); ?></h4>
                        <p><?php echo nl2br(htmlspecialchars($announcement['message'])); ?></p>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="customer-announcement-empty">Wala pang bagong announcement.</p>
            <?php endif; ?>
        </div>
    </section>

        <table id="payment-history">
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
if ($payment_count > 0) {
    $paymentIndex = 0;
    while ($payment = mysqli_fetch_assoc($payment_result)) {
        $paymentIndex++;
        $hidePaymentRow = $paymentIndex > 5;
?>

<tr<?php echo $hidePaymentRow ? ' class="payment-history-extra" hidden' : ''; ?>>
    <td data-label="No."><?php echo $payment['id']; ?></td>

    <td data-label="Date of Payment">
        <?php echo date('m-d-Y', strtotime($payment['created_at'])); ?>
    </td>

    <td data-label="Amount">
        ₱<?php echo number_format($payment['amount'], 2); ?>
    </td>

    <td data-label="Payment Method">
        <?php echo $payment['payment_method']; ?>
    </td>

    <td data-label="Status">
        <?php echo $payment['payment_status']; ?>
    </td>

    <td data-label="SOA">
    <?php
    $paymentDate = strtotime($payment['created_at']);
    $soaMonth = date('n', $paymentDate);
    $soaYear = date('Y', $paymentDate);
    ?>

    <a
        class="download"
        href="download_soa.php?month=<?php echo $soaMonth; ?>&amp;year=<?php echo $soaYear; ?>"
        style="text-decoration: none;"
    >
        Download SOA
    </a>
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
        <?php if ($payment_count > 5): ?>
            <button type="button" id="payment-history-toggle" class="payment-history-toggle" aria-expanded="false">
                Tingnan lahat (<?php echo $payment_count; ?>)
            </button>
        <?php endif; ?>
</div>
</div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('payment-history-toggle');
    if (!toggle) return;

    var expanded = false;
    toggle.addEventListener('click', function () {
        expanded = !expanded;
        document.querySelectorAll('#payment-history .payment-history-extra').forEach(function (row) {
            row.hidden = !expanded;
        });
        toggle.setAttribute('aria-expanded', String(expanded));
        toggle.textContent = expanded ? 'Ipakita lang ang 5 pinakabago' : 'Tingnan lahat (<?php echo $payment_count; ?>)';
    });
});
</script>
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
