<?php
include '../database/database_connection.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>

<!DOCTYPE html>

<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_payment.css">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <title>Payments</title>
</head>

<body>

<?php include 'admin_sidebar_header_profile.php'; ?>

<div class="card w-75">
    <div class="card-body">

    <div class="table-container">

        <div class="aligned">

            <div class="searchbar-container">
                <form method="GET" action="">

                    <input 
                        type="text"
                        placeholder="Search.."
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                    >

                    <button type="submit" class="btn btn-primary">
                        Search
                    </button>

                    <?php if (!empty($search)) { ?>

                        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                           class="btn btn-secondary">
                            Clear
                        </a>

                    <?php } ?>

                </form>
            </div>

            <div class="payment-plus">
                <form action="admin_add_payment.php" method="get">
                    <button type="submit" class="btn btn-success">
                        Add Payment
                    </button>
                </form>
            </div>

        </div>

        <br>

        <?php

       if (empty($search)) {

    $query = "SELECT payment_tbl.*, 
                     customer_tbl.account_number,
                     admin_superadmin_accounts_tbl.f_name AS admin_f_name,
                     admin_superadmin_accounts_tbl.m_name AS admin_m_name,
                     admin_superadmin_accounts_tbl.l_name AS admin_l_name
              FROM payment_tbl
              INNER JOIN customer_tbl
                  ON payment_tbl.user_id = customer_tbl.user_id
              LEFT JOIN admin_superadmin_accounts_tbl
                  ON payment_tbl.cash_added_by = admin_superadmin_accounts_tbl.account_id
              ORDER BY payment_tbl.id ASC";

} else {

    $searchTerm = mysqli_real_escape_string($conn, $search);

    $query = "SELECT payment_tbl.*, 
                     customer_tbl.account_number,
                     admin_superadmin_accounts_tbl.f_name AS admin_f_name,
                     admin_superadmin_accounts_tbl.m_name AS admin_m_name,
                     admin_superadmin_accounts_tbl.l_name AS admin_l_name
              FROM payment_tbl
              INNER JOIN customer_tbl
                  ON payment_tbl.user_id = customer_tbl.user_id
              LEFT JOIN admin_superadmin_accounts_tbl
                  ON payment_tbl.cash_added_by = admin_superadmin_accounts_tbl.account_id
              WHERE payment_tbl.id LIKE '%$searchTerm%'
                 OR customer_tbl.account_number LIKE '%$searchTerm%'
                 OR payment_tbl.f_name LIKE '%$searchTerm%'
                 OR payment_tbl.m_name LIKE '%$searchTerm%'
                 OR payment_tbl.l_name LIKE '%$searchTerm%'
                 OR payment_tbl.payment_method LIKE '%$searchTerm%'
                 OR payment_tbl.amount LIKE '%$searchTerm%'
                 OR payment_tbl.reference_number LIKE '%$searchTerm%'
                 OR payment_tbl.paymongo_payment_id LIKE '%$searchTerm%'
              ORDER BY payment_tbl.id ASC";
}

        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        ?>

        <table class="table table-secondary table-hover">

            <thead class="table-info">

                <tr>
                    <th>ACCOUNT NUMBER</th>
                    <th>FIRST NAME</th>
                    <th>MIDDLE NAME</th>
                    <th>LAST NAME</th>
                    <th>PAYMENT METHOD</th>
                    <th>AMOUNT</th>
                    <th>REFERENCE NUMBER</th>
                    <th>CASH ADDED BY</th>
                    <th>ACTION</th>
                </tr>

            </thead>

            <tbody>

            <?php

            if (mysqli_num_rows($result) > 0) {

                while ($row = mysqli_fetch_assoc($result)) {

            ?>

                <tr>
                    <td><?php echo $row['account_number']; ?></td>
                    <td><?php echo $row['f_name']; ?></td>
                    <td> <?php echo $row['m_name']; ?></td>
                    <td><?php echo $row['l_name']; ?></td>
                    <td><?php echo $row['payment_method']; ?></td>
                    <td> <?php echo $row['amount']; ?></td>
                    
                    
                   <td>
            <?php
                     if ($row['payment_method'] == 'Cash') {
                        echo $row['reference_number'];
                   } else {
                        echo $row['paymongo_payment_id'];
                    }
                ?>
                  </td>
                  <td>
    <?php
    if ($row['payment_method'] == 'Cash') {
        echo $row['admin_f_name'] . ' ' .
             $row['admin_m_name'] . ' ' .
             $row['admin_l_name'];
    } else {
        echo '-';
    }
    ?>
</td>
                    <td>
                        <a href="../crud/delete_payment.php?id=<?php echo $row['id']; ?>"
                           class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to delete this payment?');">
                            Delete
                        </a>

                    </td>

                </tr>

            <?php

                }

            } else {

            ?>

                <tr>
                    <td colspan="8" class="text-center">
                        No payments found.
                    </td>
                </tr>

            <?php

            }

            ?>

            </tbody>

        </table>

    </div>

</div>

</div>

</body>
</html>
