<?php
include '../database/database_connection.php';

if (!isset($_GET['id']) || !isset($_GET['role'])) {
    die("Invalid account.");
}

$id = $_GET['id'];
$role = $_GET['role'];

$name = "";
$email = "";
$password = "";
$account_status = "";

if ($role == "Customer") {

    $sql = "
        SELECT 
            c.customer_id, c.f_name, c.m_name, c.l_name,
            c.connection_status, u.email, u.password
        FROM customer_tbl c
        INNER JOIN user_accounts_tbl u 
            ON c.user_id = u.user_id
        WHERE c.customer_id = '$id'
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        die("Customer not found.");
    }

    $user = mysqli_fetch_assoc($result);

    $f_name = $user['f_name'];
    $m_name = $user['m_name'];
    $l_name = $user['l_name'];
    $email = $user['email'];
    $new_password = $_POST['password'];

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $account_status = $user['connection_status'];
}

elseif ($role == "Admin" || $role == "Super Admin") {

    $sql = "
         SELECT *
        FROM admin_superadmin_accounts_tbl
        WHERE account_id = '$id'
        AND role = '$role'  ";

    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        die("$role account not found.");
    }
    $user = mysqli_fetch_assoc($result);

    $f_name = $user['f_name'];
    $m_name = $user['m_name'];
    $l_name = $user['l_name'];
    $email = $user['email'];
    $password = $user['password'];
    $account_status = "Active";
}

else {
    die("Invalid role.");
}


if (isset($_POST['update'])) {

   $f_name = $_POST['f_name'];
   $m_name = $_POST['m_name'];
   $l_name = $_POST['l_name'];
   $email = $_POST['email'];

$new_password = $_POST['password'];

    if ($role == "Customer") {

        $status = mysqli_real_escape_string(
            $conn,
            $_POST['account_status']
        );

        $update_customer = "
            UPDATE customer_tbl
            SET 
                f_name = '$f_name',
                m_name = '$m_name',
                l_name = '$l_name',
                connection_status = '$status'
            WHERE customer_id = '$id'
        ";

        mysqli_query($conn, $update_customer);

        $get_user = mysqli_query(
            $conn,
            "SELECT user_id FROM customer_tbl WHERE customer_id = '$id'"
        );

        $customer_data = mysqli_fetch_assoc($get_user);
        $user_id = $customer_data['user_id'];

       $update_email = "
             UPDATE user_accounts_tbl
              SET  email = '$email',
                  `password` = '$hashed_password'
                   WHERE user_id = '$user_id'";

mysqli_query($conn, $update_email);
    }
    else {

      $update_admin = "
    UPDATE admin_superadmin_accounts_tbl
    SET 
        f_name = '$f_name',
        m_name = '$m_name',
        l_name = '$l_name',
        email = '$email',
        `password` = '$hashed_password'
    WHERE account_id = '$id'
    AND role = '$role'
";

mysqli_query($conn, $update_admin);
    }

    echo "<script>
            alert('Account updated successfully!');
           window.location.href='../admin/admin_user_management.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Edit User</title>

</head>

<body>
<?php include '../admin/admin_sidebar_header_profile.php'; ?>
<div class="container mt-5">

    <div class="card">

        <div class="card-header">
            <h3>Edit <?php echo($role); ?> Account</h3>
        </div>

        <div class="card-body">

            <form method="POST">
                <div class="mb-3">

                    <label class="form-label"> First Name</label>

                    <input  type="text" name="f_name" class="form-control"value="<?php echo($f_name); ?>"
                        required>
                </div>
                <div class="mb-3">

                    <label class="form-label"> Middle Name</label>
                    <input type="text" name="m_name" class="form-control"value="<?php echo ($m_name); ?>" >
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text"name="l_name" class="form-control"value="<?php echo($l_name); ?>" required>
                </div>

                <div class="mb-3">
                     <label class="form-label">Email Address </label>
                    <input type="email" name="email" class="form-control" value="<?php echo($email); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="text" name="password"class="form-control" value="<?php echo($password); ?>" required >
                </div>

                <?php if ($role == "Customer") { ?>

                    <div class="mb-3">

                        <label class="form-label"> Account Status</label>

                        <select name="account_status" class="form-select" required>

                            <option value="Connected"
                                <?php if ($account_status == "Connected") echo "selected"; ?>>
                                Connected
                            </option>

                            <option value="Disconnected"
                                <?php if ($account_status == "Disconnected") echo "selected"; ?>>
                                Disconnected
                            </option>

                        </select>
                    </div>
                <?php } ?>

                <button type="submit" name="update" class="btn btn-success"> Update Account
                </button>
                <a href="../admin/admin_user_management.php" class="btn btn-secondary">
                    Cancel
                </a>
         </form>

        </div>

    </div>

</div>

</body>
</html>