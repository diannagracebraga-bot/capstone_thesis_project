<?php
include '../database/database_connection.php';

function paymentValue($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$result = mysqli_query($conn, 'SELECT * FROM payment_tbl ORDER BY id DESC');
if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_payment.css">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <title>Payments | MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>
<?php include 'admin_sidebar_header_profile.php'; ?>

<h1>USER MANAGEMENT TRACKING</h1>
<div class="card w-75">
    <div class="card-body">
        <div class="table-container">
            <div class="aligned">
                <div class="searchbar-container">
                    <input type="text" placeholder="Search.." name="search" aria-label="Search payments">
                    <button type="button">Search</button>
                </div>
                <div class="payment-plus">
                    <a href="admin_add_payment.php" class="btn btn-success">Add Payment</a>
                </div>
            </div>

            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">Payment updated successfully.</div>
            <?php elseif (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">Payment deleted successfully.</div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger">The payment could not be processed. Please try again.</div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-secondary table-hover align-middle">
                    <thead class="table-info">
                        <tr>
                            <th>ID</th><th>PLAN</th><th>FIRST NAME</th><th>MIDDLE NAME</th>
                            <th>LAST NAME</th><th>PAYMENT METHOD</th><th>DUE DATE</th>
                            <th>AMOUNT</th><th>REMARKS</th><th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= paymentValue($row['id']) ?></td>
                                <td><?= paymentValue($row['plan']) ?></td>
                                <td><?= paymentValue($row['f_name']) ?></td>
                                <td><?= paymentValue($row['m_name']) ?></td>
                                <td><?= paymentValue($row['l_name']) ?></td>
                                <td><?= paymentValue($row['payment_method']) ?></td>
                                <td><?= paymentValue($row['due_date']) ?></td>
                                <td><?= paymentValue($row['amount']) ?></td>
                                <td><?= paymentValue($row['remarks']) ?></td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-payment-update btn-sm" data-bs-toggle="modal" data-bs-target="#updatePayment<?= (int) $row['id'] ?>">Update</button>
                                    <a href="../database/delete.php?id=<?= urlencode($row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this payment?')">Delete</a>

                            <div class="modal fade payment-modal" id="updatePayment<?= (int) $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <form action="../database/update_payment.php" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Payment #<?= paymentValue($row['id']) ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= paymentValue($row['id']) ?>">
                                                <div class="row g-3">
                                                    <div class="col-md-6"><label class="form-label">Plan</label><select name="plan" class="form-select" required><?php foreach (['50 mbps','100 mbps','150 mbps','200 mbps','250 mbps'] as $plan): ?><option value="<?= $plan ?>" <?= $row['plan'] === $plan ? 'selected' : '' ?>><?= $plan ?></option><?php endforeach; ?></select></div>
                                                    <div class="col-md-6"><label class="form-label">Payment Method</label><select name="payment_method" class="form-select" required><?php foreach (['Cash','Gcash'] as $method): ?><option value="<?= $method ?>" <?= $row['payment_method'] === $method ? 'selected' : '' ?>><?= $method ?></option><?php endforeach; ?></select></div>
                                                    <div class="col-md-4"><label class="form-label">First Name</label><input type="text" name="f_name" class="form-control" value="<?= paymentValue($row['f_name']) ?>" required></div>
                                                    <div class="col-md-4"><label class="form-label">Middle Name</label><input type="text" name="m_name" class="form-control" value="<?= paymentValue($row['m_name']) ?>"></div>
                                                    <div class="col-md-4"><label class="form-label">Last Name</label><input type="text" name="l_name" class="form-control" value="<?= paymentValue($row['l_name']) ?>" required></div>
                                                    <div class="col-md-4"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control" value="<?= paymentValue($row['due_date']) ?>" required></div>
                                                    <div class="col-md-4"><label class="form-label">Amount</label><input type="number" name="amount" class="form-control" value="<?= paymentValue($row['amount']) ?>" min="0" step="0.01" required></div>
                                                    <div class="col-md-4"><label class="form-label">Remarks</label><select name="remarks" class="form-select" required><?php foreach (['Paid','Not Paid'] as $remark): ?><option value="<?= $remark ?>" <?= $row['remarks'] === $remark ? 'selected' : '' ?>><?= $remark ?></option><?php endforeach; ?></select></div>
                                                </div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-payment-update">Save Changes</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="10" class="text-center py-4">No payment records found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
