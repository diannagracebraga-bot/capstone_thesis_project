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
        <link rel="stylesheet" href="../css/admin_user_management.css">
        <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
        <title>User Management</title>
    </head>
    <body>

    <?php include 'admin_sidebar_header_profile.php'; ?>

    <h1>USER MANAGEMENT TRACKING</h1>


    <div class="card w-75">
        <div class="card-body">
            <div class="table-container">

    <?php
    if ($page == 'add_customer') {
        include 'admin_add_customer.php';
    } else {
    ?>
        <div class="dropdown">
            <a class="dropdown-item" href="../admin/admin_add_admin.php">Admin</a>
            <a class="dropdown-item" href="../admin/admin_user_management.php?page=add_customer">Customer</a>
        </div>
    </div>

</div>
    <table class="table_applicants">
        <thead>
            <tr>
                <th>ACCOUNT NUMBER</th>
                <th>NAME</th>
                <th>EMAIL ADDRESS</th>
                <th>ROLE</th>
                <th>ACCOUNT STATUS</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
    <?php
    $sql = "SELECT customer_id, account_number, first_name AS f_name, middle_name AS m_name, last_name AS l_name, email_address AS email, status AS connection_status FROM customer_tbl ORDER BY customer_id DESC";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){

        while($row = mysqli_fetch_assoc($result)){
    ?>
    <tr>
        <td><?php echo $row['account_number']; ?></td>
        <td>
            <?php echo $row['f_name']." ".$row['m_name']." ".$row['l_name']; ?>
        </td>
        <td><?php echo $row['email']; ?></td>
        <td>Customer</td>
        <td><?php echo $row['connection_status']; ?></td>
        <td>
            <a href="edit_user.php?id=<?php echo $row['customer_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
            <a href="../database/delete_customer.php?id=<?php echo $row['customer_id']; ?>"
            class="btn btn-danger btn-sm"
            onclick="return confirm('Delete this customer?')">
            Delete
            </a>
        </td>
    </tr>
    <?php
        }

    }else{
    ?>

    <tr>
        <td colspan="6" class="text-center">No Customer Registered</td>
    </tr>

    <?php } ?>

        </tbody>
    </table>

    <?php } ?>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    </body>
    </html>
