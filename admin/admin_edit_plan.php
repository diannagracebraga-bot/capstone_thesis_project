<?php include '../database/database_connection.php';

$sql_plan = "SELECT * FROM internet_plan_tbl";
$plan_result = mysqli_query($conn, $sql_plan);

if(isset($_POST['save_plans'])){
foreach($_POST['internet_price'] as $plan_id => $internet_price){

    $plan_name = $_POST['plan_name'][$plan_id];
    $mbps = $_POST['internet_mbps'][$plan_id];

    $update = "UPDATE internet_plan_tbl
       SET plan_name='$plan_name',S
           internet_price='$internet_price',
           internet_mbps='$mbps'
       WHERE plan_id='$plan_id'";

    mysqli_query($conn, $update);
}
    echo "<script>
            alert('Internet plans updated successfully!');
            window.location='admin_edit_plan.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../css/admin_edit_plan.css">
     <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>

<?php include 'admin_sidebar_header_profile.php'; ?>
<h1> USER MANAGEMENT TRACKING </h1>
<div class="card w-75">
  				<div class="card-body">
<form method="POST" class="plan-container">

         <div class="pill-nav offset-sm-9 mb-3">
         <a href="admin_content_management.php">Edit Services</a>
         <a class="active" href="admin_edit_plan.php">Edit Internet Plan</a>
 
         
</div>
<div class = "container">

<?php while($plan = mysqli_fetch_assoc($plan_result)){ ?>

<div class="form-group row mb-4">

    <label class="col-sm-2 col-form-label">Plan Name:</label>
    <div class="col-sm-2">
        <input
            type="text"
            class="form-control"
            name="plan_name[<?php echo $plan['plan_id']; ?>]"
            value="<?php echo $plan['plan_name']; ?>">
    </div>

    <label class="col-sm-2 col-form-label">Internet Price:</label>
    <div class="col-sm-2">
        <input
            type="text"
            class="form-control"
            name="internet_price[<?php echo $plan['plan_id']; ?>]"
            value="<?php echo $plan['internet_price']; ?>">
    </div>

    <label class="col-sm-1 col-form-label">Mbps:</label>
    <div class="col-sm-2">
        <input
            type="text"
            class="form-control"
            name="internet_mbps[<?php echo $plan['plan_id']; ?>]"
            value="<?php echo $plan['internet_mbps']; ?>">
    </div>

</div>

<?php } ?>
<br>
  <div class="form-group row">
        <div class="col-sm-9 offset-sm-6">
            <button type="submit" name="save_plans" class="btn btn-success save-btn">
                Save
            </button>
        </div>
</div>

    </div>
</div>
</div>

</body>
</html>