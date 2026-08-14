<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../database/database_connection.php';

$user_id = $_SESSION['user_id'];


$current_page = basename($_SERVER['PHP_SELF']);

$query = "SELECT * FROM admin_superadmin_accounts_tbl WHERE id='$user_id'";
$admin_result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($admin_result);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark ">
    <div class="container-fluid">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>  </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="dropdown ms-auto">
              <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">

                     <img src="" width="30" height="30" class="rounded-circle me-2">
                         <?php
                          echo $admin['f_name'] . " " . $admin['m_name'] . " " . $admin['l_name'] . " (" . $admin['role'] . ")";?>
            </button>

                <div class="dropdown-menu dropdown-menu-end">
                    <div>
                        <a class="dropdown-item text-danger" href="../database/logout.php">
                            Logout
                        </a>
</div>
</div>
            </div>
        </div>
    </div>
</nav>

        <div class="sidebar">
                <div class="image-container">
                        <img src="../images/bg_logo.png" alt="Logo" class="icon2">
        </div>
       <a href="admin_dashboard.php" class="<?php
              if ($current_page == 'admin_dashboard.php') {
                 echo 'active';}?>">  Dashboard</a> <br>
          <a href="admin_applicants.php" class="<?php
              if ($current_page == 'admin_applicants.php') {
                 echo 'active'; }?>"> Applicants</a> <br>
        <a href="admin_customer.php" class="<?php
              if ($current_page == 'admin_customer.php') {
                 echo 'active'; } ?>">Customer</a> <br>
        <a href="admin_payment.php" class="<?php
              if ($current_page == 'admin_payment.php') {
                 echo 'active';} ?>"> Payments</a> <br>
        <a href="admin_inquiries.php" class="<?php
              if ($current_page == 'admin_inquiries.php') {
                 echo 'active';} ?>"> Inquiries </a> <br>
        <a href="admin_ticket_management.php" class="<?php
             if ($current_page == 'admin_ticket_management.php') {
                 echo 'active';}?>"> Ticket Management</a> <br>
        <a href="admin_user_management.php" class="<?php
             if ($current_page == 'admin_user_management.php') {
                 echo 'active'; } ?>">User Management</a> <br>
        <a href="admin_content_management.php" class="<?php
             if ($current_page == 'admin_content_management.php') {
                 echo 'active';}?>"> Content Management </a>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>