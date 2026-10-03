<?php
include '../database/database_connection.php';

$filled_by_column = mysqli_query($conn, "SHOW COLUMNS FROM internet_application_tbl LIKE 'filled_up_by'");
if ($filled_by_column && mysqli_num_rows($filled_by_column) === 0) {
    mysqli_query($conn, "ALTER TABLE internet_application_tbl ADD filled_up_by VARCHAR(150) NOT NULL DEFAULT ''");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$last_name = $_POST['last_name'];
$sex = $_POST['sex'];
$contact_number = $_POST['contact_number'];
$email = $_POST['email'];
$facebook_account = $_POST['facebook_account'];
$barangay = $_POST['barangay'];
$address = $_POST['address'];
$internet_plan = $_POST['internet_plan'];
$desired_installation_date = $_POST['desired_installation_date'];
$phase_7_carissa = $_POST['phase_7_carissa'];
$filled_up_by = mysqli_real_escape_string($conn, trim($_POST['filled_up_by'] ?? ''));
$date_received = date("Y-m-d H:i:s");
$status = "Pending";

$hoa_certificate = "";

if (isset($_FILES['hoa_certificate']) && $_FILES['hoa_certificate']['error'] == 0) {

    $upload_folder = "../uploads/hoa_certificates/";

    if (!is_dir($upload_folder)) {
        mkdir($upload_folder, 0777, true);
    }

    $file_name = $_FILES['hoa_certificate']['name'];
    $tmp_name = $_FILES['hoa_certificate']['tmp_name'];

    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

    if (in_array($file_extension, $allowed_extensions)) {

        $new_file_name = time() . "_" . basename($file_name);

        move_uploaded_file(
            $tmp_name,
            $upload_folder . $new_file_name
        );

        $hoa_certificate = $new_file_name;
    }
}


$sql = "INSERT INTO internet_application_tbl
(first_name, middle_name, last_name, sex, contact_number, email, facebook_account, barangay, address,
internet_plan, desired_installation_date, phase_7_carissa, hoa_certificate, date_received, status, filled_up_by)
VALUES
('$first_name', '$middle_name', '$last_name','$sex', '$contact_number', '$email', '$facebook_account', '$barangay', '$address',
'$internet_plan', '$desired_installation_date', '$phase_7_carissa', '$hoa_certificate', '$date_received', '$status', '$filled_up_by')";

if(mysqli_query($conn, $sql)){

    $applicant_id = mysqli_insert_id($conn);

    echo "<script>  
            alert('Internet Application submitted successfully!\\n\\nYour reference number is: " . $applicant_id . "');
            window.location='../index.php';
          </script>";

}else{
    echo "Failed to Submit Application " . mysqli_error($conn);
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
	
			<div class="card w-75">
  				<div class="card-body">
			<div class = "table-container">
       <div class="searchbar-container">
    <input type="text" id="searchInput" placeholder="Search applicant..." name="search">
    <button type="button" id="searchButton"> Search</button>
</div>
		<br>
				<table id="applicantTable" class="table table-secondary table-hover">
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
$sql = "SELECT 
            a.*,
            p.plan_name,
            p.internet_price,
            p.internet_mbps
        FROM internet_application_tbl a
        LEFT JOIN internet_plan_tbl p
            ON a.internet_plan = p.plan_id
        ORDER BY a.applicant_id ASC";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
?>
    <tr>
        <td><?php echo $row['applicant_id']; ?></td>
        <td><?php echo $row['first_name']; ?></td>
        <td><?php echo $row['middle_name']; ?></td>
        <td><?php echo $row['last_name']; ?></td>
        <td><?php echo $row['contact_number']; ?></td>
        <td>
            <?php echo $row['plan_name']; ?> - ₱<?php echo number_format($row['internet_price'], 2); ?> - <?php echo $row['internet_mbps']; ?> Mbps
        </td>
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
$result = mysqli_query($conn, "SELECT 
                                    a.*,
                                    p.plan_name,
                                    p.internet_price,
                                    p.internet_mbps
                                FROM internet_application_tbl a
                                LEFT JOIN internet_plan_tbl p
                                    ON a.internet_plan = p.plan_id
                                ORDER BY a.applicant_id DESC");

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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../crud/update_applicants.php" method="POST">
                <input type="hidden" name="applicant_id" value="<?php echo $row['applicant_id']; ?>">
                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Applicant ID</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['applicant_id']; ?>" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Received</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['date_received']; ?>" readonly>
                        </div>

                    </div>

                    <h6 class="text-black border-bottom pb-2 mt-3 mb-3">
                        Personal Information
                    </h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['first_name']; ?>" readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['middle_name']; ?>" readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['last_name']; ?>" readonly>
                        </div>


                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sex</label>
                            <input type="text" class="form-control bg-light" value="<?php echo ucfirst($row['sex']); ?>" readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['contact_number']; ?>" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['email']; ?>" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Facebook Account</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['facebook_account']; ?>" readonly>
                        </div>
                    </div>

                    <h6 class="text-black border-bottom pb-2 mt-3 mb-3"> Address Information</h6>
                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Barangay</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['barangay']; ?>" readonly>
                        </div>

                        <div class="col-md-8 mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['address']; ?>" readonly>
                        </div>

                    </div>

                    <h6 class="text-black border-bottom pb-2 mt-3 mb-3">
                        Internet Service
                    </h6>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Internet Plan</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['plan_name']; ?> - ₱<?php echo number_format($row['internet_price'], 2); ?> - <?php echo $row['internet_mbps']; ?> Mbps"
                                   readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Desired Installation Date</label>
                            <input type="date" class="form-control bg-light" value="<?php echo $row['desired_installation_date']; ?>" readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phase 7 Carissa</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $row['phase_7_carissa']; ?>"  readonly>
                        </div>

                    </div>

                    <h6 class="text-black border-bottom pb-2 mt-3 mb-3">
                        HOA Requirement
                    </h6>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">HOA Certificate</label>

                            <?php if (!empty($row['hoa_certificate'])) { ?>

                                <br>

                                <a href="../uploads/hoa_certificates/<?php echo $row['hoa_certificate']; ?>"
                                   target="_blank"
                                   class="btn btn-outline-primary">
                                    View HOA Certificate
                                </a>

                            <?php } else { ?>

                                <input type="text" class="form-control bg-light" value="No HOA Certificate uploaded" readonly>
                            <?php } ?>

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

                    <div class="row mt-2">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Filled Up By:</label>

                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($row['filled_up_by'] ?? ''); ?>" readonly>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close  </button>
                    <button type="submit" name="update_status" class="btn btn-success"> Save Changes</button>

                </div>
            </form>

        </div>
    </div>
</div>

<?php
}
?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="../javascript/admin_applicants.js"></script>

</body>
</html>