<?php
include '../database/database_connection.php';

$inquiries_id = $_GET['inquiries_id'];

$sql = "SELECT * FROM inquiries_tbl WHERE inquiries_id='$inquiries_id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <link rel="stylesheet" href="../css/admin_view_inquiries.css">
    <title>View Inquiry</title>
</head>

<body>

<?php include 'admin_sidebar_header_profile.php'; ?>
 <h1>
        USER MANAGEMENT TRACKING
    </h1>
<div class="main-content">

    <div class="card shadow">

        <div class="card-body p-4">

            <form action="../crud/update_inquiries.php"  method="POST">

                <input type="hidden" name="inquiries_id" value="<?php echo $row['inquiries_id']; ?>">
                <div class="row">
               
                    <div class="col-md-8">

                        <label class="section-title">
                            Customer Details
                        </label>
                        <table class="table table-borderless">
                            <tr>
                                <td width="150"><strong>Name:</strong></td>
                                <td><?php echo $row['full_name']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email Address:</strong></td>
                                <td><?php echo $row['email_address']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Contact Number:</strong></td>
                                <td><?php echo $row['contact_number']; ?></td>
                            </tr>
                        </table>
                    </div>
                     <!-- LEFT SIDE -->
                    <div class="col-md-4">

                        <label class="section-title">
                            Current Status
                        </label>

                        <div class="mb-3">

                        <?php

                        if($row['status']=="Pending"){
                            echo '<span class="badge bg-warning text-dark status-badge">Pending</span>';
                        }
                        elseif($row['status']=="Ongoing"){
                            echo '<span class="badge bg-primary status-badge">Ongoing</span>';
                        }
                        else{
                            echo '<span class="badge bg-success status-badge">Resolved</span>';
                        }

                        ?>

                        </div>

                        <label class="mb-3">
                            Change Status
                        </label>

                        <select name="status" class="form-select mb-3">

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
                <hr class="my-4">

                <label class="section-title">
                    Description:
                </label>
                <textarea
                    class="form-control"rows="8" readonly><?php echo $row['description']; ?></textarea>
                <div class="text-end mt-4">

                    <a href="admin_inquiries.php"  class="btn btn-secondary">
                        Back
                    </a>
                    <button type="submit" name = "update_status"class="btn btn-primary"> Update Status</button>
                </div>

                
            </form>
        </div>
    </div>
</div>
</body>
</html>