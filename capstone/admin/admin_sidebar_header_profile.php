<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../database/database_connection.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: ../index.php");
    exit();
}
$account_id = $_SESSION['account_id'];

$query = "SELECT * FROM admin_superadmin_accounts_tbl WHERE account_id='$account_id'";

$current_page = basename($_SERVER['PHP_SELF']);

$admin_result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($admin_result);
$isSuperAdmin = isset($admin['role']) && strcasecmp(trim($admin['role']), 'Super Admin') === 0;
$canViewAdminNotifications = isset($admin['role']) && in_array(strtolower(trim($admin['role'])), ['admin', 'super admin'], true);
require_once __DIR__ . '/../database/admin_payment_notifications.php';

// Show pending work counts to both Admin and Super Admin accounts.
$sidebarCounts = [
    'applicants' => 0,
    'payments' => 0,
    'inquiries' => 0,
    'tickets' => 0,
];
$sidebarLatestNoticeAt = array_fill_keys(array_keys($sidebarCounts), 0);
if ($canViewAdminNotifications) {
    $pendingCountQueries = [
        'applicants' => "SELECT COUNT(*) AS total FROM internet_application_tbl WHERE status = 'Pending'",
        'payments' => "SELECT COUNT(*) AS total FROM payment_tbl WHERE payment_status = 'Pending'",
        'inquiries' => "SELECT COUNT(*) AS total FROM inquiries_tbl WHERE status = 'Pending'",
        'tickets' => "SELECT COUNT(*) AS total FROM ticket_management_tbl WHERE status = 'Pending'",
    ];

    foreach ($pendingCountQueries as $key => $countQuery) {
        $countResult = mysqli_query($conn, $countQuery);
        if ($countResult) {
            $countRow = mysqli_fetch_assoc($countResult);
            $sidebarCounts[$key] = (int) ($countRow['total'] ?? 0);
        }
    }

    $latestPendingQueries = [
        'applicants' => "SELECT MAX(date_received) AS latest_at FROM internet_application_tbl WHERE status = 'Pending'",
        'payments' => "SELECT MAX(created_at) AS latest_at FROM payment_tbl WHERE payment_status = 'Pending'",
        'inquiries' => "SELECT MAX(date_received) AS latest_at FROM inquiries_tbl WHERE status = 'Pending'",
        'tickets' => "SELECT MAX(COALESCE(date_submitted, date_received)) AS latest_at FROM ticket_management_tbl WHERE status = 'Pending'",
    ];
    foreach ($latestPendingQueries as $key => $latestQuery) {
        $latestResult = mysqli_query($conn, $latestQuery);
        if ($latestResult) {
            $latestRow = mysqli_fetch_assoc($latestResult);
            $sidebarLatestNoticeAt[$key] = strtotime($latestRow['latest_at'] ?? '') ?: 0;
        }
    }
}
$adminPendingCountTotal = array_sum($sidebarCounts);
$unreadPaidPaymentCount = $canViewAdminNotifications
    ? getUnreadAdminPaidPaymentCount($conn, (int) $account_id, $current_page === 'admin_payment.php')
    : 0;
$pendingTotal = $adminPendingCountTotal + $unreadPaidPaymentCount;
$latestReadPaidPaymentId = 0;
if ($canViewAdminNotifications) {
    $readIdResult = mysqli_query(
        $conn,
        "SELECT last_seen_payment_id FROM admin_payment_notification_reads WHERE account_id = '" . (int) $account_id . "' LIMIT 1"
    );
    if ($readIdResult) {
        $readIdRow = mysqli_fetch_assoc($readIdResult);
        $latestReadPaidPaymentId = (int) ($readIdRow['last_seen_payment_id'] ?? 0);
    }
}
$recentPaidPayments = [];
if ($canViewAdminNotifications) {
    $paidPaymentsResult = mysqli_query(
        $conn,
        "SELECT id, f_name, m_name, l_name, amount, created_at
         FROM payment_tbl
         WHERE payment_status = 'Paid'
         ORDER BY created_at DESC, id DESC
         LIMIT 5"
    );
    if ($paidPaymentsResult) {
        while ($paidPayment = mysqli_fetch_assoc($paidPaymentsResult)) {
            $recentPaidPayments[] = $paidPayment;
        }
    }
}
$adminNotificationItems = [
    'applicants' => ['label' => 'Applicants', 'href' => 'admin_applicants.php'],
    'payments' => ['label' => 'Payments', 'href' => 'admin_payment.php'],
    'inquiries' => ['label' => 'Inquiries', 'href' => 'admin_inquiries.php'],
    'tickets' => ['label' => 'Ticket Management', 'href' => 'admin_ticket_management.php'],
];
foreach ($adminNotificationItems as $key => &$item) {
    $item['sort_at'] = $sidebarLatestNoticeAt[$key] ?? 0;
}
unset($item);
uasort($adminNotificationItems, static function ($first, $second) {
    return ($second['sort_at'] ?? 0) <=> ($first['sort_at'] ?? 0);
});

function renderSidebarBadge($count, $id = null)
{
    if ($count < 1 && $id !== 'admin-payment-notification-badge') {
        return '';
    }
    $displayCount = $count > 99 ? '99+' : (string) $count;
    $idAttribute = $id !== null ? ' id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '"' : '';
    $ariaLabel = $id === 'admin-payment-notification-badge' ? 'payment notifications' : 'pending';
    $hiddenAttribute = $count < 1 ? ' style="display:none;"' : '';
    return '<span' . $idAttribute . ' class="sidebar-notification" aria-label="' . $count . ' ' . $ariaLabel . '"' . $hiddenAttribute . '>' . $displayCount . '</span>';
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
     
	 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	 <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css?v=admin-header-payment-notice-1">

    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark admin-topbar">
    <div class="container-fluid">
        <a class="admin-brand" href="admin_dashboard.php" aria-label="MITZTIANPC Dashboard">
            <img src="../images/bg_logo.png" alt="MITZTIANPC logo">
            <span><strong>MITZTIANPC</strong><small>WIRED INTERNET SERVICES</small></span>
        </a>
        <button class="navbar-toggler" type="button" aria-label="Toggle sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="admin-topbar-actions ms-auto">
            <div class="dropdown admin-notification-dropdown">
                <button class="admin-notification-button" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                    <i class="bi bi-bell-fill" aria-hidden="true"></i>
                    <span id="admin-notification-total" class="admin-notification-total" style="<?php echo $pendingTotal > 0 ? '' : 'display:none;'; ?>"><?php echo $pendingTotal > 99 ? '99+' : $pendingTotal; ?></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end admin-notification-menu">
                    <strong class="dropdown-header">Pending items</strong>
                    <?php foreach ($adminNotificationItems as $key => $item): ?>
                        <a class="dropdown-item" href="<?php echo htmlspecialchars($item['href']); ?>"><?php echo htmlspecialchars($item['label']); ?> <span><?php echo $sidebarCounts[$key]; ?></span></a>
                    <?php endforeach; ?>
                    <?php if ($canViewAdminNotifications): ?>
                        <div class="dropdown-divider"></div>
                        <strong class="dropdown-header">Recent payments <span id="admin-new-payment-count" class="admin-new-payment-count" style="<?php echo $unreadPaidPaymentCount > 0 ? '' : 'display:none;'; ?>"><?php if ($unreadPaidPaymentCount > 0): ?><?php echo $unreadPaidPaymentCount > 99 ? '99+' : $unreadPaidPaymentCount; ?> new<?php endif; ?></span></strong>
                        <div id="admin-recent-payments-list">
                        <?php if (empty($recentPaidPayments)): ?>
                            <span class="dropdown-item-text small text-muted">No payments yet.</span>
                        <?php else: ?>
                            <?php foreach ($recentPaidPayments as $paidPayment): ?>
                                <?php
                                    $payerName = trim(implode(' ', array_filter([
                                        $paidPayment['f_name'] ?? '',
                                        $paidPayment['m_name'] ?? '',
                                        $paidPayment['l_name'] ?? '',
                                    ], static function ($part) { return trim((string) $part) !== ''; })));
                                    $paidAt = !empty($paidPayment['created_at']) ? strtotime($paidPayment['created_at']) : false;
                                ?>
                                <a class="dropdown-item admin-paid-notice" href="admin_payment.php">
                                    <span class="admin-paid-notice-details">
                                        <strong><?php echo htmlspecialchars($payerName !== '' ? $payerName : 'Customer'); ?></strong>
                                        <small><?php echo $paidAt ? htmlspecialchars(date('M j, Y · g:i A', $paidAt)) : 'Paid'; ?></small>
                                    </span>
                                    <span class="admin-paid-notice-trailing">
                                        <?php if ((int) $paidPayment['id'] > $latestReadPaidPaymentId): ?><small class="admin-paid-new-label">NEW</small><?php endif; ?>
                                        <span class="admin-paid-notice-amount">₱<?php echo number_format((float) $paidPayment['amount'], 2); ?></span>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <span class="admin-profile-divider"></span>
                <div class="dropdown admin-profile-dropdown">
                    <button class="admin-profile-button" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account profile">    
                        <span class="admin-avatar">
                            <?php
                                $photo_path = __DIR__ . '/../uploads/admin_profiles/';
                                $photo_url = '../uploads/admin_profiles/';

                                   if (!empty($admin['profile_photo']) && file_exists($photo_path . basename($admin['profile_photo']))) {
                                    ?> <img src="<?php echo htmlspecialchars($photo_url . rawurlencode(basename($admin['profile_photo']))); ?>"alt="Profile Photo" class="admin-profile-photo">
                             <?php
                                         } else {
                                        ?> <i class="bi bi-person-fill" aria-hidden="true"></i> <?php } ?>
                                </span>
                         </button>

                            <div class="dropdown-menu dropdown-menu-end admin-profile-menu">
                                 <div class="admin-profile-info">
                                     <strong> <?php echo htmlspecialchars(trim($admin['f_name'] . ' ' . $admin['m_name'] . ' ' . $admin['l_name'])); ?> </strong>
                                          <small> <?php echo htmlspecialchars($admin['role']); ?> </small>
                                     </div>
                                <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="../database/logout.php"> <i class="bi bi-box-arrow-right me-2"></i> Logout</a> </div>
                                    </div>
                                </div>
                            </div>
                            </nav>
<h1>USER MANAGEMENT TRACKING</h1>

        <aside class="sidebar">
        <div class="sidebar-section-label">MAIN</div>
       <a href="../admin/admin_dashboard.php" class="<?php
              if ($current_page == 'admin_dashboard.php') {
                 echo 'active';}?>"><i class="bi bi-grid-1x2-fill sidebar-icon" aria-hidden="true"></i><span>Dashboard</span></a>
        <?php if ($isSuperAdmin): ?>
        <a href="../admin/admin_announcement_management.php" class="<?php echo $current_page === 'admin_announcement_management.php' ? 'active' : ''; ?>"><i class="bi bi-megaphone-fill sidebar-icon" aria-hidden="true"></i><span>Announcements</span></a>
        <?php endif; ?>
        <a href="../admin/admin_applicants.php" class="<?php
              if ($current_page == 'admin_applicants.php') {
                 echo 'active'; }?>"><i class="bi bi-person-lines-fill sidebar-icon" aria-hidden="true"></i><span>Applicants</span><?php echo renderSidebarBadge($sidebarCounts['applicants']); ?></a>
        <a href="../admin/admin_customer.php" class="<?php
              if ($current_page == 'admin_customer.php') {
                 echo 'active'; } ?>"><i class="bi bi-people-fill sidebar-icon" aria-hidden="true"></i><span>Customer</span></a>
        <a href="../admin/admin_payment.php" class="<?php
              if ($current_page == 'admin_payment.php') {
                 echo 'active';} ?>"><i class="bi bi-credit-card-2-front-fill sidebar-icon" aria-hidden="true"></i><span>Payments</span><?php echo renderSidebarBadge($sidebarCounts['payments'] + $unreadPaidPaymentCount, 'admin-payment-notification-badge'); ?></a>
        <a href="../admin/admin_inquiries.php" class="<?php
              if ($current_page == 'admin_inquiries.php') {
                 echo 'active';} ?>"><i class="bi bi-chat-left-text-fill sidebar-icon" aria-hidden="true"></i><span>Inquiries</span><?php echo renderSidebarBadge($sidebarCounts['inquiries']); ?></a>
        <div class="sidebar-section-label sidebar-section-divider">SUPPORT</div>
        <a href="../admin/admin_ticket_management.php" class="<?php
             if ($current_page == 'admin_ticket_management.php') {
                 echo 'active';}?>"><i class="bi bi-ticket-detailed-fill sidebar-icon" aria-hidden="true"></i><span>Ticket Management</span><?php echo renderSidebarBadge($sidebarCounts['tickets']); ?></a>
        <?php if ($isSuperAdmin): ?>
        <div class="sidebar-section-label sidebar-section-divider">ADMINISTRATION</div>
        <a href="../admin/admin_user_management.php" class="<?php
             if ($current_page == 'admin_user_management.php') {
                 echo 'active'; } ?>"><i class="bi bi-person-gear sidebar-icon" aria-hidden="true"></i><span>User Management</span></a>
        <a href="../admin/admin_content_management.php" class="<?php
              if ($current_page == 'admin_content_management.php') {
                 echo 'active';}?>"><i class="bi bi-file-earmark-richtext-fill sidebar-icon" aria-hidden="true"></i><span>Content Management</span></a>
        <?php endif; ?>
        </aside>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.querySelector('.navbar-toggler');
            var sidebar = document.querySelector('.sidebar');
            if (!toggle || !sidebar) return;
            toggle.addEventListener('click', function () {
                if (window.matchMedia('(max-width: 1024px)').matches || navigator.maxTouchPoints > 0) {
                    sidebar.classList.toggle('mobile-open');
                }
            });
            sidebar.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () { sidebar.classList.remove('mobile-open'); });
            });
        });
        </script>
        <?php if ($canViewAdminNotifications): ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var pendingTotal = <?php echo (int) $adminPendingCountTotal; ?>;
            var paymentPending = <?php echo (int) $sidebarCounts['payments']; ?>;
            var sidebarBadge = document.getElementById('admin-payment-notification-badge');
            var totalBadge = document.getElementById('admin-notification-total');
            var maxBadgeCount = function (count) { return count > 99 ? '99+' : String(count); };

            function updateBadge(element, count) {
                if (!element) return;
                element.textContent = maxBadgeCount(count);
                element.style.display = count > 0 ? '' : 'none';
            }

            function refreshPaymentNotifications() {
                fetch('../database/admin_payment_notification_count.php', {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                    credentials: 'same-origin'
                }).then(function (response) {
                    if (!response.ok) throw new Error('Unable to refresh payment notifications');
                    return response.json();
                }).then(function (data) {
                    var unreadPaid = Number(data.unread_paid_payments) || 0;
                    updateBadge(sidebarBadge, paymentPending + unreadPaid);
                    updateBadge(totalBadge, pendingTotal + unreadPaid);

                    var newPaymentLabel = document.getElementById('admin-new-payment-count');
                    if (newPaymentLabel) {
                        newPaymentLabel.textContent = unreadPaid > 0 ? maxBadgeCount(unreadPaid) + ' new' : '';
                        newPaymentLabel.style.display = unreadPaid > 0 ? '' : 'none';
                    }

                    var paymentList = document.getElementById('admin-recent-payments-list');
                    if (paymentList && Array.isArray(data.recent_payments)) {
                        paymentList.replaceChildren();
                        if (data.recent_payments.length === 0) {
                            var emptyState = document.createElement('span');
                            emptyState.className = 'dropdown-item-text small text-muted';
                            emptyState.textContent = 'No payments yet.';
                            paymentList.appendChild(emptyState);
                        } else {
                            data.recent_payments.forEach(function (payment) {
                                var link = document.createElement('a');
                                link.className = 'dropdown-item admin-paid-notice';
                                link.href = 'admin_payment.php';

                                var details = document.createElement('span');
                                details.className = 'admin-paid-notice-details';
                                var name = document.createElement('strong');
                                name.textContent = payment.name || 'Customer';
                                var date = document.createElement('small');
                                date.textContent = payment.created_at || 'Paid';
                                details.append(name, date);

                                var trailing = document.createElement('span');
                                trailing.className = 'admin-paid-notice-trailing';
                                if (payment.is_new) {
                                    var newLabel = document.createElement('small');
                                    newLabel.className = 'admin-paid-new-label';
                                    newLabel.textContent = 'NEW';
                                    trailing.appendChild(newLabel);
                                }
                                var amount = document.createElement('span');
                                amount.className = 'admin-paid-notice-amount';
                                amount.textContent = '₱' + Number(payment.amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                trailing.appendChild(amount);
                                link.append(details, trailing);
                                paymentList.appendChild(link);
                            });
                        }
                    }
                }).catch(function () {});
            }

            window.setInterval(refreshPaymentNotifications, 15000);
        });
        </script>
        <?php endif; ?>
        <script>
        document.addEventListener('click', function (event) {
            var toggle = event.target.closest('.password-visibility-toggle');
            if (!toggle) return;
            var field = toggle.parentElement;
            var input = field.querySelector('input');
            var icon = toggle.querySelector('i');
            if (!input) return;

            var showPassword = input.type === 'password';
            input.type = showPassword ? 'text' : 'password';
            toggle.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            toggle.setAttribute('aria-pressed', showPassword ? 'true' : 'false');
            if (icon) icon.className = showPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
        </script>
</body>
</html>
