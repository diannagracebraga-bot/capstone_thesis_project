<?php
include '../database/database_connection.php';

if (isset($_GET['updated'])) {
    echo "<script>alert('Customer details updated successfully!');</script>";
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

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

if ($search !== '') {
    $search_safe = mysqli_real_escape_string($conn, $search);

    $sql .= "
    WHERE c.account_number LIKE '%$search_safe%'
       OR c.f_name LIKE '%$search_safe%'
       OR c.m_name LIKE '%$search_safe%'
       OR c.l_name LIKE '%$search_safe%'
       OR c.barangay LIKE '%$search_safe%'
       OR c.connection_status LIKE '%$search_safe%'
    ";
}

$sql .= " ORDER BY c.customer_id ASC";

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css?v=announcement-theme-3">
    <link rel="stylesheet" href="../css/admin_customer.css?v=announcement-card-padding-20261029-5">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body class="admin-module-page">
<?php include 'admin_sidebar_header_profile.php'; ?>
<div class="card">
    <div class="card-body">
        <div class="table-container1">
            <div class="aligned">
<div class="searchbar-container">
    <form method="GET" action="">
        <div class="input-group">
         <input type="text" id="customerSearch" name="search" placeholder="Search..." class="form-control" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">

            <button type="submit" class="btn btn-primary">
                Search
            </button>

            <?php if ($search !== '') { ?>
                <a
                    href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>"
                    class="btn btn-secondary">
                    Clear
                </a>
            <?php } ?>
        </div>
    </form>
</div>


   <div class="status-filter">
    <select id="statusFilter" class="form-select">
        <option value="" selected>All Status</option>
        <option value="connected">Connected</option>
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
                        <td><button class="btn btn-primary"data-bs-toggle="modal"data-bs-target="#editModal<?php echo $row['customer_id']; ?>">  View </button>
                            <a href="../crud/delete_customer.php?id=<?php echo $row['customer_id']; ?>"
                               class="btn btn-danger"
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
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                            Cancel
                                        </button>
                                        <button type="submit"  name="update" class="btn btn-success">
                                            Save Changes
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
                        <td colspan='7'class='text-center'> No Customer Registered </td> </tr>";

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

</body>
</html>
