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

<div class="card w-75">
    <div class="card-body">
        <div class="ticket_container">

            <div class="container">

                <div class="top-section">
                    <form method="POST">
                        <div class="status">

                          <label>Status</label>
                             <div>
    <?php

                if($ticket['status'] == "Pending"){
                        echo '<span class="badge status-badge bg-warning text-dark">Pending</span>';
                    }
                elseif($ticket['status'] == "Ongoing"){
                        echo '<span class="badge status-badge bg-primary">Ongoing</span>';
                    }
                elseif($ticket['status'] == "Resolved"){
                        echo '<span class="badge status-badge bg-success">Resolved</span>';
                    }

    ?>
    </div>

    <br>
    <select name="status" class="form-select">
        <option value="Ongoing" <?php if($ticket['status']=='Ongoing') echo 'selected'; ?>>Ongoing</option>
        <option value="Resolved" <?php if($ticket['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
        <option value="Pending" <?php if($ticket['status']=='Pending') echo 'selected'; ?>>Pending</option>
    </select>

</div>


          <div class="status">
             <label>Priority</label>

                  <select name="priority">
                     <option value="Urgent">Urgent</option>
                     <option value="Resolved">Resolved</option>
                     <option value="Pending">Pending</option>
                            </select>
                        </div>

            <div class="action"> <br>
             <label>Action</label>
              <button type="submit" class="btn btn-primary">Update</button>
</div>
  </form>
 </div>

                <div class="customer_details">
                  <label>Customer Details:</label>  
                    <textarea readonly><?php echo "Name: " . $ticket['full_name'] . "\n";
                        echo "Email: " . $ticket['email_address'] . "\n";
                        echo "Contact Number: " . $ticket['contact_number'];?></textarea>
</div>
               <div class="ticket_details">
                  <label>Ticket Details:</label>
                     <textarea readonly><?php echo "Ticket ID: " . $ticket['ticket_id'] . "\n";
                        echo "Concern Type: " . $ticket['concern_type'] . "\n";
                        echo "Date Received: " . $ticket['date_received'] ;?></textarea>
</div>

              <div class="customer_concern_description">
                    <label>Description:</label>
                      <textarea readonly><?php echo $ticket['description']; ?></textarea>
</div>

            </div>

        </div>
    </div>
</div>

</body>
</html>