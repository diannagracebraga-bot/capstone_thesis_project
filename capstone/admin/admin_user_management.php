```php
<?php
$page = $_GET['page'] ?? 'users';
include '../database/database_connection.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_user_management.css?v=mobile-layout-3">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css?v=mobile-layout-3">

    <title>User Management</title>
</head>

<body>

<?php include 'admin_sidebar_header_profile.php'; ?>

<div class="card w-75">
    <div class="card-body">

        <div class="table-container">

            <div class="aligned">

                <div class="searchbar-container">
                    <form method="GET" action="admin_user_management.php">

                        <input
                            type="text"
                            placeholder=" Search "
                            name="search"
                            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                        >

                        <input type="hidden" name="page" value="users">

                        <button type="submit">Search</button>

                        <?php if (!empty($_GET['search'])): ?>
                            <a href="admin_user_management.php" class="btn btn-secondary btn-sm">
                                Clear
                            </a>
                        <?php endif; ?>

                    </form>
                </div>

                <div class="dropdown add-user-dropdown">
                    <select
                        class="dropbtn"
                        onchange="if(this.value) window.location.href=this.value;"
                    >

                        <option value="" hidden>
                            Add User
                        </option>

                        <option value="../admin/admin_add_admin.php">
                            Admin
                        </option>

                        <option value="../admin/admin_add_super_admin.php">
                            Super Admin
                        </option>

                    </select>
                </div>

            </div>

            <?php
            if ($page == 'add_customer') {
                include 'admin_add_customer.php';
            } else {
            ?>

            <table class="table table-secondary table-hover">

                <thead class="table-info">
                    <tr>
                        <th>NAME</th>
                        <th>EMAIL ADDRESS</th>
                        <th>ROLE</th>
                        <th>ACCOUNT STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>

                <tbody>

                <?php

                $search = $_GET['search'] ?? '';
                $search = mysqli_real_escape_string($conn, $search);

                $sql = "
                    SELECT
                        CONCAT_WS(' ', a.f_name, a.m_name, a.l_name) AS name,
                        a.email AS email,
                        a.role AS role,
                        'Active' AS account_status,
                        a.account_id AS record_id
                    FROM admin_superadmin_accounts_tbl a
                    WHERE
                        CONCAT_WS(' ', a.f_name, a.m_name, a.l_name) LIKE '%$search%'
                        OR a.email LIKE '%$search%'
                        OR a.role LIKE '%$search%'
                        OR 'Active' LIKE '%$search%'
                ";

                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }

                if (mysqli_num_rows($result) > 0) {

                    while ($row = mysqli_fetch_assoc($result)) {

                ?>

                    <tr>

                        <td>
                            <?php echo $row['name']; ?>
                        </td>

                        <td>
                            <?php echo $row['email']; ?>
                        </td>

                        <td>
                            <?php echo $row['role']; ?>
                        </td>

                        <td>
                            <?php echo $row['account_status']; ?>
                        </td>

                        <td>

                            <a
                                href="../crud/update_user_accounts.php?id=<?php echo urlencode($row['record_id']); ?>&role=<?php echo urlencode($row['role']); ?>"
                                class="btn btn-primary btn-sm"
                            >
                                Edit
                            </a>

                            <a
                                href="../crud/delete_user_account.php?record_id=<?php echo urlencode($row['record_id']); ?>&role=<?php echo urlencode($row['role']); ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this account?');"
                            >
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td colspan="5" class="text-center">
                            No users found.
                        </td>

                    </tr>

                <?php
                }
                ?>

                </tbody>

            </table>

            <?php
            }
            ?>

        </div>

    </div>
</div>

</body>
</html>
