<?php
include '../database/database_connection.php';

if (isset($_POST['status'])) {

    $new_status = $_POST['status'];
    $ticket_id = $_GET['ticket_id'];

    $sql_update = "UPDATE ticket_management_tbl
                   SET status='$new_status'
                   WHERE ticket_id='$ticket_id'";

  if(mysqli_query($conn, $sql_update)){
    echo "<script>
            alert('Status updated successfully!');
            window.location='admin_ticket_management.php';
          </script>";
} else {
    echo "<script>
            alert('Failed to update status.');
          </script>";
}
exit();
}

if (!isset($_GET['ticket_id'])) {
    die("No ticket selected.");
}

$ticket_id = $_GET['ticket_id'];

$query = "SELECT * FROM ticket_management_tbl WHERE ticket_id='$ticket_id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$ticket = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <link rel="stylesheet" href="../css/admin_view_ticket_management.css">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>

<?php include 'admin_sidebar_header_profile.php'; ?>

<h1>USER MANAGEMENT TRACKING</h1>
<div class="main-content">

    <div class="card shadow">

        <div class="card-body p-4">

            <form method="POST">

                <div class="row">

                   
<div class="col-md-8">

    <div class="row">

        <!-- Customer Details -->
        <div class="col-md-6">

            <label class="section-title">
                Customer Details
            </label>

            <table class="table table-borderless">

                <tr>
                    <td width="150"><strong>Name:</strong></td>
                    <td><?php echo $ticket['full_name']; ?></td>
                </tr>

                <tr>
                    <td><strong>Email Address:</strong></td>
                    <td><?php echo $ticket['email_address']; ?></td>
                </tr>

                <tr>
                    <td><strong>Contact Number:</strong></td>
                    <td><?php echo $ticket['contact_number']; ?></td>
                </tr>

            </table>

        </div>

        <!-- Ticket Details -->
        <div class="col-md-6">

            <label class="section-title">
                Ticket Details
            </label>

            <table class="table table-borderless">

                <tr>
                    <td width="140"><strong>Ticket ID:</strong></td>
                    <td><?php echo $ticket['ticket_id']; ?></td>
                </tr>

                <tr>
                    <td><strong>Concern Type:</strong></td>
                    <td><?php echo $ticket['concern_type']; ?></td>
                </tr>

                <tr>
                    <td><strong>Date Received:</strong></td>
                    <td><?php echo $ticket['date_received']; ?></td>
                </tr>

                <tr>
                    <td><strong>Priority:</strong></td>
                    <td><?php echo $ticket['priority']; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
                    <div class="col-md-4">

                        <label class="section-title">
                            Current Status
                        </label>

                        <div class="mb-3">

                            <?php
                            if($ticket['status']=="Pending"){
                                echo '<span class="badge bg-warning text-dark status-badge">Pending</span>';
                            }
                            elseif($ticket['status']=="Ongoing"){
                                echo '<span class="badge bg-primary status-badge">Ongoing</span>';
                            }
                            else{
                                echo '<span class="badge bg-success status-badge">Resolved</span>';
                            }
                            ?>

                        </div>

                        <label class="mb-2"> Change Status</label>

                        <select name="status" class="form-select mb-4">

                            <option value="Pending" <?php if($ticket['status']=="Pending") echo "selected"; ?>>
                                Pending
                            </option>

                            <option value="Ongoing" <?php if($ticket['status']=="Ongoing") echo "selected"; ?>>
                                Ongoing
                            </option>

                            <option value="Resolved" <?php if($ticket['status']=="Resolved") echo "selected"; ?>>
                                Resolved
                            </option>

                        </select>

                        <label class="mb-2">
                            Priority
                        </label>

                        <input type="text" class="form-control" value="<?php echo $ticket['priority']; ?>" readonly>
                    </div>
                <hr class="my-4">

                <label class="section-title">
                    Description:
                </label>

                <textarea class="form-control" rows="5" readonly><?php echo $ticket['description']; ?></textarea>
                <div class="text-end mt-4">

                    <a href="admin_ticket_management.php" class="btn btn-secondary">  Back
                    </a>

                    <button type="submit" class="btn btn-primary"> Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>