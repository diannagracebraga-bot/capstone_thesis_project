<?php
include '../database/database_connection.php';

if(isset($_POST['update_status'])){

    $applicant_id = $_POST['applicant_id'];
    $status = $_POST['status'];

    $sql = "UPDATE internet_application_tbl 
            SET status='$status'
            WHERE applicant_id='$applicant_id'";

    if(mysqli_query($conn,$sql)){
        echo "<script>
                alert('Status Updated Successfully!');
                window.location='admin_applicants.php';
              </script>";
    }else{
        echo "Error: ".mysqli_error($conn);
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$last_name = $_POST['last_name'];
$birth_date = $_POST['birth_date'];
$sex = $_POST['sex'];
$contact_number =$_POST['contact_number'];
$barangay = $_POST['barangay'];
$house_number = $_POST['house_number'];
$street = $_POST['street'];
$subdivision = $_POST['subdivision'];
$internet_plan = $_POST['internet_plan'];
$date_received = date("Y-m-d H:i:s");
$status = "Pending";

$sql = "INSERT INTO internet_application_tbl
(first_name, middle_name, last_name, birth_date , sex, contact_number,barangay,house_number, street , subdivision,
internet_plan, date_received, status)
VALUES
('$first_name', '$middle_name', '$last_name','$birth_date','$sex','$contact_number', '$barangay',
'$house_number','$street','$subdivision','$internet_plan','$date_received', '$status')";

if(mysqli_query($conn, $sql)){
    echo "<script>  
            alert('Internet Application submitted successfully!');
            window.location='../index.php';
          </script>";
}else{
    echo "Failed to Submit inquiry " . mysqli_error($conn);
}}
?>

<!DOCTYPE html>
<html>
<head>
	  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
	 <link rel="stylesheet" href="../css/admin_applicants.css">
	 <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
	<title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>
	<?php include 'admin_sidebar_header_profile.php'; ?>
	
        	<h1>USER MANAGEMENT TRACKING</h1>
			<div class="card w-75">
  				<div class="card-body">
			<div class = "table-container">
       <div class= "aligned">
			<div class="searchbar-container">
                <input type="text" placeholder="Search.." name="search">
                <button type="submit">Search</button>
		</div>
        </div>
		<br>
				<table class = "table table-secondary table-hover">
					<thead class = "table-info">
					<tr>
						<th>APPLICANT ID</th>
						<th>FIRST NAME</th>
						<th>MIDDLE NAME</th>
						<th>LAST NAME</th>
						<th>CONTACT NUMBER</th>
						<th>INTERNET PLAN</th>
						<th>DATE RECEIVED</th>
						<th>STATUS</th>
						<th>ACTION</th>
					</tr>
					</thead>
					<tbody>
<?php
$sql = "SELECT * FROM internet_application_tbl";
$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
?>
    <tr>
        <td><?php echo $row['applicant_id']; ?></td>
        <td><?php echo $row['first_name']; ?></td>
        <td><?php echo $row['middle_name']; ?></td>
        <td><?php echo $row['last_name']; ?></td>
        <td><?php echo $row['contact_number']; ?></td>
        <td><?php echo $row['internet_plan']; ?></td>
        <td><?php echo $row['date_received']; ?></td>
        <td>
            <?php

                if($row['status']=="Pending"){
                     echo '<span class="badge status-badge bg-warning text-dark">Pending</span>';
                    }
                elseif($row['status']=="Ongoing"){
                     echo '<span class="badge status-badge bg-primary">Ongoing</span>';
                    }
                    elseif($row['status']=="Resolved"){
                     echo '<span class="badge status-badge bg-success">Resolved</span>';
                    }
?>
</td>
        <td>
            <button class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#viewApplicant<?php echo $row['applicant_id']; ?>">
				View</button>
        </td>
    </tr>
<?php
}
?>
    </tbody>
</table>
 </div>
</div>
<?php
$result = mysqli_query($conn, "SELECT * FROM internet_application_tbl");

while($row = mysqli_fetch_assoc($result)){
?>

<div class="modal fade"
     id="viewApplicant<?php echo $row['applicant_id']; ?>"
     tabindex="-1"
     aria-labelledby="viewApplicantLabel<?php echo $row['applicant_id']; ?>"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow">

            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="viewApplicantLabel<?php echo $row['applicant_id']; ?>">
                    APPLICANT INFORMATION</h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
					<thead class = "thead-info">
                    <tr><th class="w-25">APPLICANT ID</th><td><?php echo $row['applicant_id']; ?></td> </tr>

                    <tr><th>FIRST NAME</th><td><?php echo $row['first_name']; ?></td></tr>

                    <tr><th>MIDDLE NAME</th><td><?php echo $row['middle_name']; ?></td></tr>

                    <tr><th>LAST NAME</th><td><?php echo $row['last_name']; ?></td></tr>

                    <tr><th>BIRTH DATE</th><td><?php echo $row['birth_date']; ?></td> </tr>

                    <tr><th>SEX</th><td><?php echo ucfirst($row['sex']); ?></td></tr>

                    <tr><th>CONTACT NUMBER</th><td><?php echo $row['contact_number']; ?></td></tr>

                    <tr><th>BARANGAY</th><td><?php echo $row['barangay']; ?></td></tr>

                    <tr><th>HOUSE NUMBER</th><td><?php echo $row['house_number']; ?></td></tr>

                    <tr><th>STREET</th><td><?php echo $row['street']; ?></td></tr>

                    <tr><th>SUBDIVISION</th><td><?php echo $row['subdivision']; ?></td></tr>

                    <tr><th>INTERNET PLAN</th><td><?php echo $row['internet_plan']; ?></td></tr>

                    <tr><th>DATE RECEIVED</th><td><?php echo $row['date_received']; ?></td></tr>

                   <form method="POST">

                        <input type="hidden" name="applicant_id" value="<?php echo $row['applicant_id']; ?>">
                        
                            <tr>
                                <th>STATUS</th><td>

                                <select class="form-select" name="status">
                                    <option value="Pending"
                                    <?php if($row['status']=="Pending") echo "selected"; ?>>Pending</option>

                                    <option value="Ongoing"
                                    <?php if($row['status']=="Ongoing") echo "selected"; ?>>Ongoing</option>

                                    <option value="Resolved"<?php if($row['status']=="Resolved") echo "selected"; ?>>Resolved
                                    </option>

    </select>
</td>
</tr>
				
                </table>
				
            </div>
            <div class="modal-footer">
			    <button type="submit" name="update_status" class="btn btn-success">
                         Save Changes</button>
                 <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close
                </button>
           </form>     </div>
        </div>
    </div>
</div>
<?php } ?>
			
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>			
</body>
</html>