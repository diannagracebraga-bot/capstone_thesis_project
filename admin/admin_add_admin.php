<?php
include '../database/database_connection.php';

if (isset($_POST['submit'])) {

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $f_name = $_POST['f_name'];
    $m_name = $_POST['m_name'];
    $l_name = $_POST['l_name'];
    $role = "Admin";
    $contact_number = $_POST['contact_number'];

    
    $sql = "INSERT INTO admin_superadmin_accounts_tbl (email, password, f_name, m_name, l_name, role, contact_number)
            VALUES ('$email', '$password','$f_name', '$m_name', '$l_name', '$role', '$contact_number')";
    if (mysqli_query($conn, $sql)) {
       echo "<script>
                alert('Admin created successfully!');
                window.location.href='admin_user_management.php';
            </script>";
        } else {
            die("Error: " . mysqli_error($conn));
}
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_add_admin.css">

    <style>
        .card{
    width: 75%;
    padding: 20px;
    margin: 0 0 0 305px;
    box-shadow: 0 6px 15px rgba(0,74,173,0.3);
}
        </style>
</head>
<body>
    <?php include 'admin_sidebar_header_profile.php'; ?>

<h1>USER MANAGEMENT TRACKING</h1>
<div class="card">
    <div class="card-body">
<div class="container mt-4">
    <h2>Create Admin</h2>

    <form method="POST">
        <div class="mb-3">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="f_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Middle Name</label>
            <input type="text" name="m_name" class="form-control">
        </div>
        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="l_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contact Number</label>
            <input type="text" name="contact_number" class="form-control" required>
        </div>
        <button type="submit" name="submit" class="btn btn-success">Create</button>
    </form>
</div>
</div>
</div>
</body>
</html>
