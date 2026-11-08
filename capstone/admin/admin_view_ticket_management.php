<?php
include '../admin/check_admin_access.php';

include '../database/database_connection.php';

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
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css?v=announcement-theme-4">
    <link rel="stylesheet" href="../css/admin_view_ticket_management.css?v=announcement-theme-4">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>

<?php include 'admin_sidebar_header_profile.php'; ?>

<main class="ticket-detail-page">

    <section class="card shadow ticket-detail-card">

        <div class="card-body p-4 ticket-detail-card-body">

           <form action="../crud/update_ticket.php" method="POST" id="ticketForm">

               <input type="hidden" name="ticket_id" value="<?php echo $ticket['ticket_id']; ?>">
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

                     <select name="status" id="status" class="form-select mb-4">

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

                       
                    </div>
                </div>
                <hr class="my-2">

                <label class="section-title">
                    Description:
                </label>

                <textarea class="form-control" rows="4" readonly><?php echo $ticket['description']; ?></textarea>

               <label class="section-title mt-3">
    Admin Remarks:
</label>

<textarea name="remarks" id="remarks" class="form-control" rows="3"
    placeholder="Enter remarks or updates for the customer..."><?php echo $ticket['remarks'] ?? ''; ?></textarea>
                <?php
                $ticketAttachmentName = basename((string) ($ticket['attachment_path'] ?? ''));
                $ticketAttachmentExtension = strtolower(pathinfo($ticketAttachmentName, PATHINFO_EXTENSION));
                $ticketAttachmentUrl = '../uploads/ticket_attachments/' . rawurlencode($ticketAttachmentName);
                ?>
                <?php if ($ticketAttachmentName !== ''): ?>
                    <div class="ticket-attachment-preview mt-3">
                        <strong>Customer attachment:</strong>
                        <?php if (in_array($ticketAttachmentExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)): ?>
                            <a href="<?php echo htmlspecialchars($ticketAttachmentUrl); ?>" target="_blank" rel="noopener">Open image</a>
                            <img src="<?php echo htmlspecialchars($ticketAttachmentUrl); ?>" alt="Customer's uploaded problem image" style="display:block;max-width:100%;max-height:420px;margin-top:10px;border-radius:8px;">
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($ticketAttachmentUrl); ?>" target="_blank" rel="noopener">View attached PDF</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="text-end mt-4">

                    <a href="admin_ticket_management.php" class="btn btn-secondary">  Back
                    </a>

                   <button type="submit" name="update_status" id="updateButton" class="btn btn-primary" disabled>
    Update Status
</button>
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
