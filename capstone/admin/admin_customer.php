<?php
include '../database/database_connection.php';
require_once '../database/customer_balance.php';

if (isset($_GET['updated'])) {
    echo "<script>alert('Customer details updated successfully!');</script>";
} elseif (isset($_GET['error'])) {
    $messages = [
        'invalid_customer' => 'Invalid customer record.',
        'missing_fields' => 'May kulang na field. Pakisubukang i-save ulit.',
        'invalid_fields' => 'May maling value o petsa. Pakisuri ang mga inilagay.',
        'save_failed' => 'Hindi na-save ang customer. Suriin ang database settings at error log.',
    ];
    $message = $messages[$_GET['error']] ?? 'Hindi na-save ang pagbabago.';
    echo '<script>alert(' . json_encode($message) . ');</script>';
}

$sql = "
SELECT
    c.*,
    u.email,
    u.password,
    p.plan_id,
    p.plan_name,
    p.internet_mbps,
    p.internet_price
FROM customer_tbl c
INNER JOIN user_accounts_tbl u
    ON c.user_id = u.user_id
LEFT JOIN internet_plan_tbl p
    ON c.internet_plan = p.plan_id
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

$plan_query = mysqli_query($conn, "SELECT * FROM internet_plan_tbl");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <link rel="stylesheet" href="../css/admin_customer.css?v=admin-modal-balance-5">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>
<?php include 'admin_sidebar_header_profile.php'; ?>
<div class="card w-75">
    <div class="card-body">
        <div class="table-container1">
            <div class="aligned">
    <div class="searchbar-container">
        <form method="GET" action="">
            <input
                type="text"
                id="customerSearch"
                name="search"
                placeholder="Search.."
                value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                autocomplete="off" >
            <button type="submit" class="btn btn-primary"> Search </button>
            <?php if (!empty($_GET['search'])) { ?>
                <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>"
                   class="btn btn-secondary">
                    Clear
                </a>
            <?php } ?>
        </form>
    </div>
    <div class="status-filter">
        <select id="statusFilter" class="form-select">
            <option value="">All Statuses</option>
            <option value="connected">Connected    </option>
            <option value="inactive">Inactive</option>
            <option value="disconnected">Disconnected</option>
            <option value="frozen">Frozen</option>
        </select>
    </div>
    <div class="add_customer">
        <form action="admin_add_customer_page.php" method="GET">
            <button class="btn btn-primary" type="submit">
                Add Customer
            </button>
        </form>
    </div>
</div>
            <br>
            <table class="table table-secondary table-hover" id="customerTable">
                <thead class="table-info">
                    <tr>
                        <th>ACCOUNT NUMBER</th>
                        <th>FIRST NAME</th>
                        <th>MIDDLE NAME</th>
                        <th>LAST NAME</th>
                        <th>BARANGAY</th>
                        <th>STATUS</th>
                        <th>UNPAID BALANCE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td> <?php echo $row['account_number']; ?> </td>
                        <td> <?php echo $row['f_name']; ?></td>
                        <td> <?php echo $row['m_name']; ?> </td>
                        <td> <?php echo $row['l_name']; ?>  </td>
                        <td> <?php echo $row['barangay']; ?></td>
                        <td> <?php echo $row['connection_status']; ?></td>
                        <?php $rowBalance = calculateCustomerBalance($row['internet_price'] ?? 0, $row['due_date'] ?? ''); ?>
                        <td>
                            <?php if ($rowBalance['has_due_date']): ?>
                                <strong>₱<?php echo number_format($rowBalance['balance'], 2); ?></strong>
                                <small class="customer-balance-cycles d-block">
                                    <?php echo (int) $rowBalance['cycle_count']; ?> unpaid month<?php echo $rowBalance['cycle_count'] === 1 ? '' : 's'; ?>
                                </small>
                            <?php else: ?>
                                <span class="text-muted">Not set</span>
                            <?php endif; ?>
                        </td>
                        <td><button class="btn btn-primary btn-sm"data-bs-toggle="modal"data-bs-target="#editModal<?php echo $row['customer_id']; ?>">  View </button>
                            <a href="../crud/delete_customer.php?id=<?php echo $row['customer_id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this customer?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <div class="modal fade" id="editModal<?php echo $row['customer_id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header customer-modal-header">
                                    <h5 class="modal-title">Edit Customer Details </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="../crud/admin_update_customer.php" method="POST">
                                    <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">
                                    <div class="modal-body">
                                        <h6 class="text-black border-bottom pb-2 mb-3"> Account Information</h6>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"> Account Number</label>
                                                <input type="text" class="form-control bg-light" value="<?php echo $row['account_number']; ?>" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" class="form-control bg-light" name="email" value="<?php echo $row['email']; ?>"  readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"> Due Date</label>
                                                <input type="date" class="form-control" name="due_date" value="<?php echo $row['due_date']; ?>">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Unpaid Balance</label>
                                                <?php $modalBalance = calculateCustomerBalance($row['internet_price'] ?? 0, $row['due_date'] ?? ''); ?>
                                                <input type="text" class="form-control bg-light" value="<?php echo $modalBalance['has_due_date'] ? '₱' . number_format($modalBalance['balance'], 2) . ' (' . $modalBalance['cycle_count'] . ' unpaid month' . ($modalBalance['cycle_count'] === 1 ? '' : 's') . ')' : 'Due date not set'; ?>" readonly>
                                            </div>
                                             <div class="col-md-3 mb-3">
                                                 <label class="form-label">Installation Date </label>
                                                 <input type="date"class="form-control"name="installation_date" value="<?php echo $row['installation_date']; ?>">
                                                </div>
                                        </div>
                                        <h6 class="text-black border-bottom pb-2 mt-3 mb-3">Personal Information</h6>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"> First Name</label>
                                                <input type="text" class="form-control" name="f_name" value="<?php echo $row['f_name']; ?>">
                                            </div>
                                            <div class="col-md-4 mb-3">

                                                <label class="form-label"> Middle Name </label>
                                                <input type="text" class="form-control" name="m_name" value="<?php echo $row['m_name']; ?>">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"> Last Name</label>
                                                <input type="text" class="form-control" name="l_name" value="<?php echo $row['l_name']; ?>">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"> Contact Number </label>
                                                <input type="tel" class="form-control" name="contact_number" value="<?php echo $row['contact_number']; ?>">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">  Age </label>
                                                <input type="number" class="form-control" name="age" value="<?php echo $row['age']; ?>">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"> Sex</label>
                                                <select class="form-select"
                                                        name="sex">
                                                    <option value="Male"<?php
                                                        if ($row['sex'] == "Male")
                                                            echo "selected";
                                                        ?>>  Male
                                                    </option>
                                                    <option value="Female"
                                                        <?php
                                                        if ($row['sex'] == "Female")
                                                            echo "selected";
                                                        ?>>
                                                        Female
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <h6 class="text-black border-bottom pb-2 mt-3 mb-3">Address Information </h6>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label"> Barangay</label>
                                                <select class="form-select"  name="barangay">
                                                    <option value="Bagtas"
                                                        <?php
                                                        if ($row['barangay'] == "Bagtas")
                                                            echo "selected";
                                                        ?>>
                                                        Bagtas
                                                    </option>
                                                    <option value="Punta I"
                                                        <?php
                                                        if ($row['barangay'] == "Punta I")
                                                            echo "selected";
                                                        ?>>
                                                        Punta I
                                                    </option>
                                                </select>
                                            </div>
                                               <div class="col-md-4 mb-3">
                                                 <label class="form-label">House Address</label>
                                                 <input type="text" class="form-control"  name="house_name"  value="<?php echo $row['house_name']; ?>"></div>
                                        </div>
                                        <h6 class="text-black border-bottom pb-2 mt-3 mb-3"> Internet Service</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Internet Plan </label>
                                                <select class="form-select" name="internet_plan">
                                                    <?php
                                                    mysqli_data_seek($plan_query, 0);
                                                    while ($plan = mysqli_fetch_assoc($plan_query)) {
                                                    ?>
                                                        <option
                                                            value="<?php echo $plan['plan_id']; ?>"
                                                            <?php
                                                            if ($row['internet_plan'] == $plan['plan_id']) {
                                                                echo "selected";
                                                            }
                                                            ?>>
                                                            <?php
                                                            echo $plan['plan_name'] .
                                                                 " - " .
                                                                 $plan['internet_mbps'] .
                                                                 " Mbps - ₱" .
                                                                 number_format(
                                                                     $plan['internet_price'],
                                                                     2
                                                                 );
                                                            ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Connection Status</label>
                                                <select class="form-select" name="connection_status">
                                                    <?php
                                                    $statuses = [
                                                         'Connected',
                                                          'Disconnected',
                                                           'Frozen',
                                                            'Inactive'
                                                             ];
                                                             foreach ($statuses as $status) {
                                                                  $selected = (
                                                                  $row['connection_status'] === $status
                                                                   ) ? 'selected' : '';
                                                                   echo '<option value="' . $status . '" '
                                                                   . $selected . '>'
                                                                   . $status
                                                                   . '</option>';
                                                                    }
                                                                    ?>
                                                                    </select>
                                                                </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit"  name="update" class="btn btn-success">
                                            Save Changes
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                            Cancel
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                    }

                } else {
                    echo "<tr>
                        <td colspan='8'class='text-center'> No Customer Registered </td> </tr>";

                }
                ?>
                </tbody>
            </table>
        </div>

    </div>

</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("customerSearch");
    const statusFilter = document.getElementById("statusFilter");
    const table = document.getElementById("customerTable");
    if (!searchInput || !statusFilter || !table) {
        console.error("Search input, status filter, or table not found.");
        return;
    }
    const rows = table.querySelectorAll("tbody tr");
    function filterCustomers() {
        const searchValue = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value.toLowerCase().trim();
        rows.forEach(function (row) {
            if (row.cells.length < 7) {
                return;
            }
            const accountNumber = row.cells[0].textContent.toLowerCase().trim();
            const firstName = row.cells[1].textContent.toLowerCase().trim();
            const middleName = row.cells[2].textContent.toLowerCase().trim();
            const lastName = row.cells[3].textContent.toLowerCase().trim();
            const barangay = row.cells[4].textContent.toLowerCase().trim();
            const status = row.cells[5].textContent.toLowerCase().trim();
            const customerData = [
                accountNumber,
                firstName,
                middleName,
                lastName,
                barangay,
                status
            ].join(" ");
            const matchesSearch = customerData.includes(searchValue);
            const matchesStatus =
                selectedStatus === "" || status === selectedStatus;
            row.style.display =
                matchesSearch && matchesStatus ? "" : "none";
        });
    }

    searchInput.addEventListener("input", filterCustomers);
    statusFilter.addEventListener("change", filterCustomers);
    filterCustomers();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
