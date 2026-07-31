<?php
include '../database/database_connection.php';

if (isset($_POST['submit'])) {

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = "Admin";

    $sql = "INSERT INTO admin_superadmin_accounts_tbl (id, email, password, f_name, m_name, l_name, role, contact_number)
            VALUES ('$id', $email', '$password','$f_name', '$m_name', '$l_name', '$role', '$contact_number')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Admin created successfully!');</script>";
    } else {
        echo "<script>alert('Failed to create admin.');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_add_admin.css">
</head>
<body>

<div class="container mt-5">
    <h2>Create Admin</h2>

    <form method="POST">

        <div class="mb-3">
            <label>id</label>
            <input type="number" name="id" class="form-control" required>
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
            <input type="number" name="contact_number" class="form-control" required>
        </div>
        <button type="submit" name="submit" class="btn btn-success">
            Create
        </button>
    </form>
</div>

</body>
</html>
