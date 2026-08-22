<?php
include '../database/database_connection.php';

$sql = "SELECT * FROM content_management_about_tbl WHERE about_id = 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/admin_content_management.css">   
	<title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>
		<?php include 'admin_sidebar_header_profile.php'; ?>

		

         <!--<div class="col-sm-9 offset-sm-9 mb-4">
                  <a href="admin_edit_plan.php">  <button type="submit" name="edit_internet_plan" 
                  class="btn btn-info edit-btn">
                        Edit Internet Plan
                    </button></a>
    </div>-->
    
        <h1> USER MANAGEMENT TRACKING </h1>
    <div class="card w-75">
  				<div class="card-body">
                    <div class="content-management-container">
   <form action="../crud/update_content_management.php" method="POST" class="form-horizontal">
    <div class="content-wrapper">

    <div class="pill-nav offset-sm-9 mb-3">
         <a class="active" href="admin_content_management.php">Edit Services</a>
         <a href="admin_edit_plan.php">Edit Internet Plan</a>
 
</div>
        
        <div class="business-info-card">
            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">BUSINESS NAME:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="business_name"
                           value="<?php echo $row['business_name']; ?>">
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">BUSINESS EMAIL:</label>
                <div class="col-sm-7">
                    <input type="email" class="form-control" name="business_email"
                           value="<?php echo $row['business_email']; ?>">
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">BUSINESS CONTACT:</label>
                <div class="col-sm-7">
                    <input type="number" class="form-control" name="business_contact"
                           value="<?php echo $row['business_contact']; ?>">
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">FACEBOOK <i class="fa fa-pagelines" aria-hidden="true"></i>:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="business_social_media"
                           value="<?php echo $row['business_social_media']; ?>">
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">BUSINESS ADDRESS:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="business_address"
                           value="<?php echo $row['business_address']; ?>">
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">BUSINESS DESCRIPTION:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="business_description"
                           value="<?php echo $row['business_description']; ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-9 offset-sm-6">
                    <button type="submit" name="update" class="btn btn-success save-btn">Save</button>
                </div>
            </div>

        </div>
    </div>
</form>
</div>
</div>
<br>
 
	
</div>
			
</body>
</html>