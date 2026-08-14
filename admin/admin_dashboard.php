<?php
include '../database/database_connection.php';

/* total customers */
$customer_query = "SELECT COUNT(*) AS total_customers FROM customer_tbl";
$customer_result = mysqli_query($conn, $customer_query);
$customer_data = mysqli_fetch_assoc($customer_result);
$total_customers = $customer_data['total_customers'];

/* Pending Applicants */
$pending_query = " SELECT COUNT(*) AS pending_applicants FROM internet_application_tbl WHERE status = 'Pending'";
$pending_result = mysqli_query($conn, $pending_query);
$pending_data = mysqli_fetch_assoc($pending_result);

$pending_applicants = $pending_data['pending_applicants'];

/* Active users */
$active_query = " SELECT COUNT(*) AS active_users FROM customer_tbl WHERE connection_status = 'Connected'";
$active_result = mysqli_query($conn, $active_query);

if (!$active_result) {
    die("Active user query failed: " . mysqli_error($conn));
}
$active_data = mysqli_fetch_assoc($active_result);
$active_users = $active_data['active_users'];
?>

<!DOCTYPE html>
<html>
<head>
	<title>MITZTIANPC WIRED INTERNET SERVICES</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/admin_dashboard.css">
        <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">

</head>
        
<body>
        <?php include 'admin_sidebar_header_profile.php'; ?>

        <h1>USER MANAGEMENT TRACKING</h1>
        <div class="stats-container">
                <div class="stat">
                    
                  <h2><?php echo $total_customers; ?></h2>
                        <p>Total Customer</p>
                </div>
                <div class="stat">
                          <h2><?php echo $active_users; ?></h2>
                                  <p>Active User</p>
                </div>
                <div class="stat">
                        <h2><?php echo $pending_applicants; ?></h2>
                                 <p>Pending Applicants</p>
                </div>
        </div>
        <div class="dashboard">
                <div class="metrics-grid">
                        <div class="metric-card">
                                <div class="metric-header">
                                        Active Users <i class="fa-solid fa-circle-info"></i>
                                </div>
                                <div class="metric-value">$23,000</div>
                                <div class="metric-change positive">
                                        vs past months <i class="fa-solid fa-arrow-up"></i>50%
                                </div>
                                <div class="metric-icon">
                                        <div class="bar-chart-icon">
                                                <div class="bar" style="height: 40px"></div>
                                                <div class="bar" style="height: 50px"></div>
                                                <div class="bar" style="height: 60px"></div>
                                        </div>
                                </div>
                                <a href="#" class="see-details">See Details<i class="fa-solid fa-arrow-right"></i></a> 
                        </div>
                </div>
        </div>
        <script src="js/script.js"></script>
</body>
</html>