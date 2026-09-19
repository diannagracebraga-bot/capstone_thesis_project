<?php
include '../database/database_connection.php';

$sql = "SELECT * FROM internet_plan_tbl ORDER BY plan_id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
    <link rel="stylesheet" href="../css/plan.css">
    <link rel="stylesheet" href="../css/index_style.css">
</head>
<body>
      <div class="header">
        <div class = "logo-section">
                 <img src="../images/bg_logo.png" alt="Logo" class="icon2">
                  <h5>MITZTIANPC WIRED INTERNET SERVICES</h5>              
                </div>
    
            <div class="dropdown">
                <button class="dropdown_menu"> <img src="../images/hamburger.png" alt="menu icon" class="icon">
                </button>
                 
                 <div class="dropdown-content">
                    <a href=..\index.php>Home</a>
                    <a href= inquire.php>Inquire</a>
                    <a href=plan.php>Plan</a>
                    <a href=about.php>About</a>
                    <a href=login.php>Login</a>
                 </div>
            </div>
    </div>

<section class="plans">

<?php while($row = mysqli_fetch_assoc($result)){ ?>

    <div class="plan-card">

        <h3><?php echo $row['internet_mbps']; ?> Mbps</h3>

        <h2>
            ₱<?php echo number_format($row['internet_price']); ?> / Month
        </h2>

        <a href="apply_internet.php"> <button>APPLY NOW</button> </a>

    </div>

<?php } ?>

</section>
           <!--  <div class="bottom-header"></div>-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>