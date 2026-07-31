<?php
include '../database/database_connection.php';

$customer_id = 1;
$query = "SELECT * FROM ticket_management_tbl WHERE customer_id = $customer_id ORDER BY ticket_id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Ticket query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/customer_sidebar_header.css?v=4">
    <link rel="stylesheet" href="../css/customer_ticket.css?v=5">
</head>
<body>
<?php include 'customer_sidebar_header.php'; ?>

<main class="ticket-content">
        <div class="card">
  			<div class="card-body">
    <div class="ticket-section">
        <h2>My Tickets</h2>

        <table class = "table table-secondary table hover">
            <thead class = "table-info">
                <tr>
                    <td>Ticket ID</td>
                    <td>Full Name</td>
                    <td>Email Address</td>
                    <td>Contact Number</td>
                    <th>Concern Type</th>
                    <th>Date Received</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($ticket = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $ticket['ticket_id']; ?></td>
                        <td><?php echo ($ticket['full_name']); ?></td>
                        <td><?php echo ($ticket['email_address']); ?></td>
                        <td><?php echo ($ticket['contact_number']); ?></td>
                        <td><?php echo ($ticket['concern_type']); ?></td>
                        <td><?php echo ($ticket['date_received']); ?></td>
                        <td><?php echo ($ticket['status']); ?></td>
                        <td>
                            <button type="button" class="view-btn" data-bs-toggle="modal" data-bs-target="#ticketModal<?php echo $ticket['ticket_id']; ?>">
                                View
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="ticketModal<?php echo $ticket['ticket_id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content ticket-modal">
                                <div class="modal-header">
                                    <h5 class="modal-title">Ticket Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body  ">
                                    <div class="ticket-detail-box">
                                        <p><strong>Ticket ID:</strong> <?php echo $ticket['ticket_id']; ?></p>
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($ticket['full_name']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($ticket['email_address']); ?></p>
                                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($ticket['contact_number']); ?></p>
                                        <p><strong>Concern Type:</strong> <?php echo htmlspecialchars($ticket['concern_type']); ?></p>
                                        <p><strong>Date Received:</strong> <?php echo htmlspecialchars($ticket['date_received']); ?></p>
                                    </div>

                                    <label class="ticket-description-label">Description:</label>
                                    <textarea class="form-control ticket-description" rows="5" readonly><?php echo htmlspecialchars($ticket['description']); ?></textarea>
                                </div>
                                <div><p><strong>Admin Reply:</strong></p>
                                    <textarea readonly><?php echo $ticket['admin_reply']; ?></textarea>
                            </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
                </div>
                </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




