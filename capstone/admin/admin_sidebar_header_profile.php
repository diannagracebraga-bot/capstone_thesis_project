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

// Show pending work counts only to Super Admins.
$sidebarCounts = [
    'applicants' => 0,
    'payments' => 0,
    'inquiries' => 0,
    'tickets' => 0,
];
if ($isSuperAdmin) {
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
}
$pendingTotal = array_sum($sidebarCounts);

function renderSidebarBadge($count)
{
    if ($count < 1) {
        return '';
    }
    $label = $count > 99 ? '99+' : (string) $count;
    return '<span class="sidebar-notification" aria-label="' . $count . ' pending">' . $label . '</span>';
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
	 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css?v=admin-header-sidebar-9">
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
            <?php if ($isSuperAdmin): ?>
            <div class="dropdown admin-notification-dropdown">
                <button class="admin-notification-button" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                    <i class="bi bi-bell-fill" aria-hidden="true"></i>
                    <?php if ($pendingTotal > 0): ?><span class="admin-notification-total"><?php echo $pendingTotal > 99 ? '99+' : $pendingTotal; ?></span><?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end admin-notification-menu">
                    <strong class="dropdown-header">Pending items</strong>
                    <a class="dropdown-item" href="admin_applicants.php">Applicants <span><?php echo $sidebarCounts['applicants']; ?></span></a>
                    <a class="dropdown-item" href="admin_payment.php">Payments <span><?php echo $sidebarCounts['payments']; ?></span></a>
                    <a class="dropdown-item" href="admin_inquiries.php">Inquiries <span><?php echo $sidebarCounts['inquiries']; ?></span></a>
                    <a class="dropdown-item" href="admin_ticket_management.php">Ticket Management <span><?php echo $sidebarCounts['tickets']; ?></span></a>
                </div>
            </div>
            <?php endif; ?>
            <span class="admin-profile-divider"></span>
            <div class="dropdown admin-profile-dropdown">
                <button class="admin-profile-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="admin-avatar"><i class="bi bi-person-fill" aria-hidden="true"></i></span>
                    <span class="admin-profile-copy">
                        <strong><?php echo htmlspecialchars(trim($admin['f_name'] . ' ' . $admin['m_name'] . ' ' . $admin['l_name'])); ?></strong>
                        <small><?php echo htmlspecialchars($admin['role']); ?></small>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item text-danger" href="../database/logout.php">Logout</a>
                </div>
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
                 echo 'active'; }?>"><i class="bi bi-person-lines-fill sidebar-icon" aria-hidden="true"></i><span>Applicants</span><?php if ($isSuperAdmin) echo renderSidebarBadge($sidebarCounts['applicants']); ?></a>
        <a href="../admin/admin_customer.php" class="<?php
              if ($current_page == 'admin_customer.php') {
                 echo 'active'; } ?>"><i class="bi bi-people-fill sidebar-icon" aria-hidden="true"></i><span>Customer</span></a>
        <a href="../admin/admin_payment.php" class="<?php
              if ($current_page == 'admin_payment.php') {
                 echo 'active';} ?>"><i class="bi bi-credit-card-2-front-fill sidebar-icon" aria-hidden="true"></i><span>Payments</span><?php if ($isSuperAdmin) echo renderSidebarBadge($sidebarCounts['payments']); ?></a>
        <a href="../admin/admin_inquiries.php" class="<?php
              if ($current_page == 'admin_inquiries.php') {
                 echo 'active';} ?>"><i class="bi bi-chat-left-text-fill sidebar-icon" aria-hidden="true"></i><span>Inquiries</span><?php if ($isSuperAdmin) echo renderSidebarBadge($sidebarCounts['inquiries']); ?></a>
        <div class="sidebar-section-label sidebar-section-divider">SUPPORT</div>
        <a href="../admin/admin_ticket_management.php" class="<?php
             if ($current_page == 'admin_ticket_management.php') {
                 echo 'active';}?>"><i class="bi bi-ticket-detailed-fill sidebar-icon" aria-hidden="true"></i><span>Ticket Management</span><?php if ($isSuperAdmin) echo renderSidebarBadge($sidebarCounts['tickets']); ?></a>
        <div class="sidebar-section-label sidebar-section-divider">ADMINISTRATION</div>
        <a href="../admin/admin_user_management.php" class="<?php
             if ($current_page == 'admin_user_management.php') {
                 echo 'active'; } ?>"><i class="bi bi-person-gear sidebar-icon" aria-hidden="true"></i><span>User Management</span></a>
        <a href="../admin/admin_content_management.php" class="<?php
              if ($current_page == 'admin_content_management.php') {
                 echo 'active';}?>"><i class="bi bi-file-earmark-richtext-fill sidebar-icon" aria-hidden="true"></i><span>Content Management</span></a>
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
</body>
</html>
