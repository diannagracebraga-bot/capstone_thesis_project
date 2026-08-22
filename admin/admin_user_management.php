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

<table class= "table table-secondary table-hover">
    <thead class = "table-info">
        <tr>
            <th>NAME</th>
            <th>EMAIL ADDRESS</th>
            <th>ROLE</th>
            <th>ACCOUNT STATUS</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
        <div class="dropdown">
    <select class="dropbtn" onchange="if(this.value) window.location.href=this.value;">
        <option value="">Add User</option>
        <option value="../admin/admin_add_admin.php">Admin</option>
        <option value="../admin/admin_add_super_admin.php">Super Admin</option>
        <option value="admin_user_management.php?page=add_customer">Customer</option>
    </select>
</div>

<?php

$sql = "
    SELECT 
        CONCAT_WS(' ', c.f_name, c.m_name, c.l_name) AS name,
        u.email AS email,
        'Customer' AS role,
        c.connection_status AS account_status,
        c.customer_id AS record_id
    FROM customer_tbl c
    INNER JOIN user_accounts_tbl u
        ON c.user_id = u.user_id

    UNION ALL

    SELECT 
        CONCAT_WS(' ', a.f_name, a.m_name, a.l_name) AS name,
        a.email AS email,
        a.role AS role,
        'Active' AS account_status,
        a.account_id AS record_id
    FROM admin_superadmin_accounts_tbl a
";

$result = mysqli_query($conn, $sql);

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){

while($row = mysqli_fetch_assoc($result)){
?>
<tr>


    <td><?php echo $row['name']; ?></td>

    <td><?php echo $row['email']; ?></td>

    <td><?php echo $row['role']; ?></td>

    <td><?php echo $row['account_status']; ?></td>

    <td>
        <a href="edit_user.php?id=<?php echo $row['record_id']; ?>" 
           class="btn btn-primary btn-sm">
            Edit
        </a>

        <a href="../crud/delete_user_account.php?record_id=<?php echo $row['record_id']; ?>&role=<?php echo $row['role']; ?>"
   class="btn btn-danger btn-sm"
   onclick="return confirm('Are you sure you want to delete this account?');">
    Delete
</a>
    </td>

</tr>
<?php
}
}else{
?>

<tr>
    <td colspan="6" style="text-align:center;">
        No Customer Registered
    </td>
</tr>

<?php
}
?>
    </tbody>
</table>
<?php } ?>

    
</div>

        </div>
    </div>

</div>

</body>
</html>