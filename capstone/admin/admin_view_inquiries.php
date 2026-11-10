<?php
include '../admin/check_admin_access.php';
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
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css?v=announcement-layout-2">
    <link rel="stylesheet" href="../css/admin_view_inquiries.css?v=announcement-layout-2">
    <title>View Inquiry</title>
</head>
<body>
<?php include 'admin_sidebar_header_profile.php'; ?>
<main class="view-inquiry-page">
    <section class="view-inquiry-card">
        <div class="view-inquiry-card-body">
            <form action="../crud/update_inquiries.php" method="POST" id="inquiryForm">
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
                      <select name="status" id="status" class="form-select mb-3">
                            <option value="Pending" <?php if($row['status']=="Pending") echo "selected"; ?>> Pending </option>
                             <option value="Ongoing" <?php if($row['status']=="Ongoing") echo "selected"; ?>> Ongoing</option>
                               <option value="Resolved" <?php if($row['status']=="Resolved") echo "selected"; ?>>  Resolved </option>
                        </select>
                    </div>
                </div>
                <hr class="my-2">
              <label class="section-title"> Description: </label>
<textarea class="form-control" rows="4" readonly><?php echo $row['description']; ?></textarea>
<label class="section-title mt-2"> Admin Remarks: </label>
<textarea name="remarks" id="remarks" class="form-control" rows="4" placeholder="Enter remarks or updates for the customer..."><?php echo $row['remarks'] ?? ''; ?></textarea>
                <div class="text-end mt-3">
                    <a href="admin_inquiries.php"  class="btn btn-secondary"> Back </a>
                   <button type="submit" name="update_status" id="updateButton" class="btn btn-primary" disabled> Update Status </button>
                </div>    
            </form>
        </div>
    </section>
</main>
<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>
<script>

const originalStatus = document.getElementById('status').value;
const originalRemarks = document.getElementById('remarks').value;
const statusField = document.getElementById('status');
const remarksField = document.getElementById('remarks');
const updateButton = document.getElementById('updateButton');
function checkChanges() {
    const statusChanged = statusField.value !== originalStatus;
    const remarksChanged = remarksField.value !== originalRemarks;
    if (statusChanged || remarksChanged) {
        updateButton.disabled = false;
    } else {
        updateButton.disabled = true;
    }
}
statusField.addEventListener('change', checkChanges);
remarksField.addEventListener('input', checkChanges);

</script>
</body>
</html>