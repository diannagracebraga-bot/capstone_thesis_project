<?php
require_once '../database/announcements_helper.php';
ensureAnnouncementsTable($conn);

$headerUserId = mysqli_real_escape_string($conn, (string) ($_SESSION['user_id'] ?? ''));
$headerCustomerResult = mysqli_query($conn, "SELECT customer_id, f_name, m_name, l_name, due_date FROM customer_tbl WHERE user_id = '$headerUserId' LIMIT 1");
$headerCustomer = $headerCustomerResult ? mysqli_fetch_assoc($headerCustomerResult) : null;
$headerName = $headerCustomer ? trim($headerCustomer['f_name'] . ' ' . $headerCustomer['m_name'] . ' ' . $headerCustomer['l_name']) : 'Customer';
$customerPage = basename($_SERVER['PHP_SELF']);
$headerNotices = [];
$paymentStatusText = 'Wala pang naitalang bayad';
$paymentIsPending = false;
$customerTicketUpdates = [];
$customerTicketUpdateCount = 0;

$latestPaymentResult = mysqli_query($conn, "SELECT payment_status, created_at FROM payment_tbl WHERE user_id = '$headerUserId' ORDER BY created_at DESC, id DESC LIMIT 1");
$latestPayment = $latestPaymentResult ? mysqli_fetch_assoc($latestPaymentResult) : null;
if ($latestPayment) {
    $paymentStatusText = trim($latestPayment['payment_status']) === 'Paid'
        ? 'Bayad na · ' . date('M d, Y', strtotime($latestPayment['created_at']))
        : 'Hindi pa bayad / hinihintay ang kumpirmasyon';
    $paymentIsPending = trim($latestPayment['payment_status']) !== 'Paid';
}

if ($paymentIsPending) {
    $headerNotices[] = ['icon' => 'bi-credit-card-2-front-fill', 'title' => 'Kumpirmahin ang bayad', 'message' => 'May huling payment na hindi pa markadong Paid.', 'href' => 'customer-dashboard.php#payment-history', 'urgent' => true];
}

$headerCustomerId = (int) ($headerCustomer['customer_id'] ?? 0);
if ($headerCustomerId > 0) {
    $ticketUpdateCountResult = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM ticket_management_tbl WHERE customer_id = $headerCustomerId AND status = 'Pending'"
    );
    if ($ticketUpdateCountResult) {
        $ticketUpdateCountRow = mysqli_fetch_assoc($ticketUpdateCountResult);
        $customerTicketUpdateCount = (int) ($ticketUpdateCountRow['total'] ?? 0);
    }

    $ticketUpdatesResult = mysqli_query(
        $conn,
        "SELECT ticket_id, concern_type, status FROM ticket_management_tbl WHERE customer_id = $headerCustomerId AND status <> 'Pending' ORDER BY ticket_id DESC LIMIT 5"
    );
    if ($ticketUpdatesResult) {
        while ($ticketUpdate = mysqli_fetch_assoc($ticketUpdatesResult)) {
            $customerTicketUpdates[] = $ticketUpdate;
            $headerNotices[] = [
                'icon' => 'bi-ticket-detailed-fill',
                'title' => 'Update sa Ticket #' . $ticketUpdate['ticket_id'],
                'message' => trim($ticketUpdate['concern_type'] . ' · ' . $ticketUpdate['status']),
                'href' => 'customer_ticket.php#customer-ticket-' . (int) $ticketUpdate['ticket_id'],
                'urgent' => false,
            ];
        }
    }
}

$dueDateString = trim((string) ($headerCustomer['due_date'] ?? ''));
$dueDate = $dueDateString !== '' && $dueDateString !== '0000-00-00' ? DateTime::createFromFormat('!Y-m-d', $dueDateString) : false;
if ($dueDate instanceof DateTime) {
    $today = new DateTime('today');
    $daysToDue = (int) $today->diff($dueDate)->format('%r%a');
    if ($daysToDue < 0) {
        $dueMessage = 'Lampas na sa due date noong ' . $dueDate->format('F d, Y') . '.';
        $dueTitle = 'May overdue na bayarin';
        $dueUrgent = true;
    } elseif ($daysToDue <= 7) {
        $dueMessage = 'Due sa ' . $dueDate->format('F d, Y') . ($daysToDue === 0 ? ' (ngayon).' : ' (' . $daysToDue . ' araw na lang).');
        $dueTitle = 'Malapit na ang payment due date';
        $dueUrgent = true;
    } else {
        $dueMessage = 'Susunod na due date: ' . $dueDate->format('F d, Y') . '.';
        $dueTitle = 'Payment schedule';
        $dueUrgent = false;
    }
    $headerNotices[] = ['icon' => 'bi-calendar-event-fill', 'title' => $dueTitle, 'message' => $dueMessage, 'href' => 'customer-dashboard.php#payment-history', 'urgent' => $dueUrgent];
}

$customerAnnouncements = [];
$announcementResult = mysqli_query($conn, 'SELECT announcement_id, title, message, created_at FROM announcements_tbl WHERE is_active = 1 ORDER BY created_at DESC, announcement_id DESC LIMIT 5');
if ($announcementResult) {
    while ($announcement = mysqli_fetch_assoc($announcementResult)) {
        $customerAnnouncements[] = $announcement;
        $headerNotices[] = [
            'icon' => 'bi-megaphone-fill',
            'title' => $announcement['title'],
            'message' => $announcement['message'],
            'href' => 'customer-dashboard.php#announcement-' . (int) $announcement['announcement_id'],
            'urgent' => false,
        ];
    }
}
$customerNotificationCount = count(array_filter($headerNotices, static function ($notice) {
    return $notice['urgent'] || in_array($notice['icon'], ['bi-megaphone-fill', 'bi-calendar-event-fill', 'bi-ticket-detailed-fill'], true);
}));
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/customer_sidebar_header.css?v=customer-shell-13">
<nav class="navbar navbar-expand-lg navbar-dark customer-topbar">
    <div class="container-fluid">
        <a class="customer-brand" href="customer-dashboard.php" aria-label="MITZTIANPC Customer Dashboard">
            <img src="../images/bg_logo.png" alt="MITZTIANPC logo">
            <span><strong>MITZTIANPC</strong><small>WIRED INTERNET SERVICES</small></span>
        </a>
        <button class="navbar-toggler" type="button" aria-label="Toggle customer navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="customer-topbar-actions ms-auto">
            <div class="dropdown customer-notification-dropdown">
                <button class="customer-notification-button" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Customer notifications">
                    <i class="bi bi-bell-fill" aria-hidden="true"></i>
                    <?php if ($customerNotificationCount > 0): ?><span class="customer-notification-count"><?php echo $customerNotificationCount > 99 ? '99+' : $customerNotificationCount; ?></span><?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end customer-notification-menu">
                    <strong class="dropdown-header">Payments, Tickets &amp; Announcements</strong>
                    <div class="customer-payment-status"><span>Latest payment</span><strong><?php echo htmlspecialchars($paymentStatusText); ?></strong></div>
                    <?php if ($headerNotices): ?>
                        <?php foreach ($headerNotices as $notice): ?>
                            <a class="dropdown-item customer-notice-item" href="<?php echo htmlspecialchars($notice['href']); ?>">
                                <i class="bi <?php echo htmlspecialchars($notice['icon']); ?>" aria-hidden="true"></i>
                                <span><strong><?php echo htmlspecialchars($notice['title']); ?></strong><small><?php echo htmlspecialchars($notice['message']); ?></small></span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="customer-no-notices">Wala pang bagong notification.</div>
                    <?php endif; ?>
                </div>
            </div>
            <span class="customer-profile-divider"></span>
            <div class="dropdown customer-profile-dropdown">
                <button class="customer-profile-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="customer-avatar"><i class="bi bi-person-fill" aria-hidden="true"></i></span>
                    <span class="customer-profile-copy"><strong><?php echo htmlspecialchars($headerName); ?></strong><small>Customer</small></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="customer_profile.php">My Profile</a>
                    <a class="dropdown-item text-danger" href="../database/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
</nav>
<aside class="sidebar customer-sidebar">
    <div class="customer-sidebar-label">MAIN</div>
    <a class="dashboard <?php echo $customerPage === 'customer-dashboard.php' ? 'active' : ''; ?>" href="customer-dashboard.php" <?php echo $customerPage === 'customer-dashboard.php' ? 'aria-current="page"' : ''; ?>><i class="bi bi-grid-1x2-fill sidebar-icon" aria-hidden="true"></i><span>Dashboard</span></a>
    <div class="customer-sidebar-label customer-sidebar-divider">SUPPORT</div>
    <a class="dashboard <?php echo $customerPage === 'customer_support.php' ? 'active' : ''; ?>" href="customer_support.php" <?php echo $customerPage === 'customer_support.php' ? 'aria-current="page"' : ''; ?>><i class="bi bi-chat-left-text-fill sidebar-icon" aria-hidden="true"></i><span>Support</span></a>
    <a class="dashboard <?php echo $customerPage === 'customer_ticket.php' ? 'active' : ''; ?>" href="customer_ticket.php" <?php echo $customerPage === 'customer_ticket.php' ? 'aria-current="page"' : ''; ?>><i class="bi bi-ticket-detailed-fill sidebar-icon" aria-hidden="true"></i><span>My Tickets</span><?php if ($customerTicketUpdateCount > 0): ?><span class="customer-ticket-badge" aria-label="<?php echo $customerTicketUpdateCount; ?> pending tickets"><?php echo $customerTicketUpdateCount > 99 ? '99+' : $customerTicketUpdateCount; ?></span><?php endif; ?></a>
    <div class="customer-sidebar-label customer-sidebar-divider">ACCOUNT</div>
    <a class="dashboard <?php echo $customerPage === 'customer_profile.php' ? 'active' : ''; ?>" href="customer_profile.php" <?php echo $customerPage === 'customer_profile.php' ? 'aria-current="page"' : ''; ?>><i class="bi bi-person-gear sidebar-icon" aria-hidden="true"></i><span>Profile</span></a>
</aside>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.querySelector('.customer-topbar .navbar-toggler');
    var sidebar = document.querySelector('.customer-sidebar');
    if (!toggle || !sidebar) return;
    toggle.addEventListener('click', function () { sidebar.classList.toggle('mobile-open'); });
    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () { sidebar.classList.remove('mobile-open'); });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
