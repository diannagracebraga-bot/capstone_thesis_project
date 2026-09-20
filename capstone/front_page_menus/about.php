<?php
include '../database/database_connection.php';

$sql = "SELECT * FROM content_management_about_tbl WHERE about_id = 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<title>MITZTIANPC WIRED INTERNET SERVICES</title>
	<link rel="stylesheet" href="../css/about.css">
	<link rel="stylesheet" href="../css/index_style.css">
</head>
<body>
			
		<div class= "header"> 
			    <div class = "logo-section">
                 <img src="../images/bg_logo.png" alt="Logo" class="icon2">
				 <h5>MITZTIANPC WIRED INTERNET SERVICES</h5>
				</div>
         
            <div class="dropdown">
                <button class="dropdown_menu"> <img src="../images/hamburger.png" alt="menu icon" class="icon">
                </button>
                 
                 <div class="dropdown-content">
                    <a href="..\index.php">Home</a>
                    <a href= inquire.php>Inquire</a>
                    <a href=plan.php>Plan</a>
                    <a href="about.php">About</a>
                    <a href="login.php">Login</a>
                 </div>
            </div>
    </div>
        
		</div>

		<div class= "pad">
			<h1><?php echo $row['business_name']; ?></h1>
			<h3>ALWAYS CONNECTED</h3>
			<p><?php echo $row ['business_description'];?></p>
			<h1>CONTACT</h1>
			<div class= "details">
			<img src="../images/mail.png" alt="mail">
			<p><?php echo $row ['business_email'];?></p>
			<img src="../images/phone.png" alt="phone">
			<p><?php echo $row ['business_contact'];?></p>
			<img src="../images/fb.png" alt="fb">
			<p><?php echo $row ['business_social_media'];?></p>
			<img src="../images/location.png" alt="location">
			<p><?php echo $row ['business_address'];?></p>
			</div>
		</div>
		<!--<div class="bottom-header"></div>-->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>