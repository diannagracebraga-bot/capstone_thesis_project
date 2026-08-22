<?php
include '../database/database_connection.php';

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
     tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2"></i>Applicant Information
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <form action="../crud/update_applicants.php" method="POST">

                <input type="hidden"
                       name="applicant_id"
                       value="<?php echo $row['applicant_id']; ?>">

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Applicant ID</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['applicant_id']; ?>"
                                   readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Received</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['date_received']; ?>"
                                   readonly>
                        </div>

                    </div>
                    <h6 class="text-black border-bottom pb-2 mt-3 mb-3">
                        Personal Information
                    </h6>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['first_name']; ?>"
                                   readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['middle_name']; ?>"
                                   readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['last_name']; ?>"
                                   readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Birth Date</label>
                            <input type="date"
                                   class="form-control bg-light"
                                   value="<?php echo $row['birth_date']; ?>"
                                   readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sex</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo ucfirst($row['sex']); ?>"
                                   readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['contact_number']; ?>"
                                   readonly>
                        </div>

                    </div>
                    <h6 class="text-black border-bottom pb-2 mt-3 mb-3">
                        Address Information
                    </h6>

                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Barangay</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['barangay']; ?>"
                                   readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Subdivision</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['subdivision']; ?>"
                                   readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Street</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['street']; ?>"
                                   readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">House Number</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['house_number']; ?>"
                                   readonly>
                        </div>

                    </div>
                    <h6 class="text-black border-bottom pb-2 mt-3 mb-3">
                        Internet Service
                    </h6>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Internet Plan</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['internet_plan']; ?>"
                                   readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>

                            <select class="form-select" name="status">
                                <option value="Pending"
                                    <?php if($row['status']=="Pending") echo "selected"; ?>>
                                    Pending
                                </option>

                                <option value="Ongoing"
                                    <?php if($row['status']=="Ongoing") echo "selected"; ?>>
                                    Ongoing
                                </option>

                                <option value="Resolved"
                                    <?php if($row['status']=="Resolved") echo "selected"; ?>>
                                    Resolved
                                </option>
                            </select>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-danger"
                            data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="submit"
                            name="update_status"
                            class="btn btn-success">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>
			
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>			
</body>
</html>