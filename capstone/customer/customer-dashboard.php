<?php
include '../admin/check_customer_access.php';

include '../database/database_connection.php';
require_once '../database/announcements_helper.php';

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
$announcement_result = mysqli_query($conn, 'SELECT announcement_id, title, message, attachment_path, created_at FROM announcements_tbl WHERE is_active = 1 ORDER BY created_at DESC, announcement_id DESC LIMIT 5');
$due_date_value = trim((string) ($customer['due_date'] ?? ''));
$due_date_valid = $due_date_value !== '' && $due_date_value !== '0000-00-00' && strtotime($due_date_value) !== false;

$today = new DateTime(date('Y-m-d'));

$is_overdue = false;
$days_overdue = 0;

if ($due_date_valid) {
    $dueDate = new DateTime($due_date_value);

    if ($dueDate < $today) {
        $is_overdue = true;
        $days_overdue = (int) $dueDate->diff($today)->days;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="/favicon.png?v=2">
<link rel="stylesheet" href="../css/customer_sidebar_header.css?v=customer-shell-14">
<link rel="stylesheet" href="../css/customer_dashboard.css?v=customer-dashboard-layout-4">

</head>
<body>
<?php include 'customer_sidebar_header.php'; ?>
<div class="content">
    <section class="customer-welcome-banner" aria-label="Customer welcome">
        <div class="customer-welcome-copy">
            <p class="customer-welcome-eyebrow">Welcome,</p>
            <h1><?php echo htmlspecialchars(trim(($customer['f_name'] ?? '') . ' ' . ($customer['l_name'] ?? ''))); ?>!</h1>
            <p class="customer-welcome-subtitle">Here's an overview of your internet account.</p>
            <div class="customer-account-actions">
                <div class="customer-account-chip"><i class="bi bi-person-vcard-fill" aria-hidden="true"></i><span>Account No.</span><strong><?php echo htmlspecialchars($customer['account_number'] ?? ''); ?></strong></div>
                <button type="button" class="payment-btn" data-bs-toggle="modal" data-bs-target="#paymentModal">Pay Now</button>
            </div>
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

    <section class="customer-service-overview" aria-label="Plan and service details">
        <article class="customer-plan-summary">
            <h3>Current Plan</h3>
            <div class="customer-plan-details">
                <strong><?php echo htmlspecialchars(strtoupper($customer['plan_name'] ?? '')); ?></strong>
                <span aria-hidden="true">|</span>
                <span class="customer-plan-mbps"><?php echo htmlspecialchars($customer['internet_mbps'] ?? ''); ?> Mbps</span>
                <span aria-hidden="true">|</span>
                <strong>₱<?php echo number_format((float) ($customer['internet_price'] ?? 0)); ?></strong>
            </div>
        </article>
        <div class="customer-overview-side">
            <article class="customer-overview-metric customer-due-metric">
                <h3>Due</h3>
                <div>
                    <?php if (!$due_date_valid): ?>
                        <span>Not set</span>
                    <?php elseif ($is_overdue): ?>
                        <strong class="customer-overdue">Overdue</strong>
                        <small><?php echo date('F d, Y', strtotime($due_date_value)); ?> (<?php echo $days_overdue; ?> day/s overdue)</small>
                    <?php else: ?>
                        <strong><?php echo date('F d, Y', strtotime($due_date_value)); ?></strong>
                    <?php endif; ?>
                </div>
            </article>
            <article class="customer-overview-metric customer-status-metric">
                <h3>Service Status</h3>
                <strong class="<?php echo ($customer['connection_status'] == 'Connected') ? 'status-connected' : 'status-disconnected'; ?>">
                    <?php echo htmlspecialchars($customer['connection_status'] ?? 'Unknown'); ?>
                </strong>
            </article>
        </div>
    </section>
    <p class="customer-payment-reminder">Pay on time to avoid connection interruptions</p>

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
                        <?php if (!empty($announcement['attachment_path'])): ?>
                            <?php $attachmentUrl = '../' . $announcement['attachment_path']; $attachmentExt = strtolower(pathinfo($announcement['attachment_path'], PATHINFO_EXTENSION)); ?>
                            <div class="customer-announcement-attachment">
                            <?php if (in_array($attachmentExt, ['png', 'jpg', 'jpeg'], true)): ?>
                                <a href="<?php echo htmlspecialchars($attachmentUrl); ?>" target="_blank" rel="noopener"><img src="<?php echo htmlspecialchars($attachmentUrl); ?>" alt="Announcement attachment"></a>
                            <?php elseif (in_array($attachmentExt, ['mp4', 'webm', 'mov', 'avi', 'mkv', 'ogv'], true)): ?>
                                <video controls preload="metadata"><source src="<?php echo htmlspecialchars($attachmentUrl); ?>"></video>
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($attachmentUrl); ?>" target="_blank" rel="noopener">View or download PDF attachment</a>
                            <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="customer-announcement-empty">Wala pang bagong announcement.</p>
            <?php endif; ?>
        </div>
    </section>

    <p class="soa-password-note">
        <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
        Your SOA password is your MPC Account Number.
    </p>

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
    <td data-label="No."><?php echo $payment_count - $paymentIndex + 1; ?></td>

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
                    id="paymentFrame"
                    src="customer_payment.php"
                    style="width: 100%; height: 600px; border: none;"
                    title="QRPH Payment">
                </iframe>

            </div>

        </div>

    </div>

</div>
<script>
document.getElementById('paymentModal').addEventListener('hidden.bs.modal', function () {
    const frame = document.getElementById('paymentFrame');
    if (frame) frame.src = 'about:blank';
});
window.addEventListener('message', function (event) {
    if (event.origin !== window.location.origin || event.data?.type !== 'customer-payment-success') return;
    alert('Your payment has been received successfully.');
    const modalElement = document.getElementById('paymentModal');
    modalElement.addEventListener('hidden.bs.modal', function () {
        window.location.reload();
    }, {once: true});
    bootstrap.Modal.getOrCreateInstance(modalElement).hide();
});
</script>
</body>
</html>
