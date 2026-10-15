<?php
require_once __DIR__ . '/../admin/require_super_admin.php';

if (!isset($_GET['id']) || !isset($_GET['role'])) {
    die("Invalid account.");
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$role = mysqli_real_escape_string($conn, $_GET['role']);

$f_name = "";
$m_name = "";
$l_name = "";
$email = "";
$password = "";
$account_status = "";
$profile_picture = "default.png";

if ($role == "Customer") {

    $sql = "
        SELECT c.customer_id, c.f_name, c.m_name, c.l_name,
               c.connection_status, u.user_id, u.email,
               u.password, u.profile_picture
        FROM customer_tbl c
        INNER JOIN user_accounts_tbl u ON c.user_id = u.user_id
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
    $password = $user['password'];
    $account_status = $user['connection_status'];
    $profile_picture = $user['profile_picture'] ?: "default.png";

} elseif ($role == "Admin" || $role == "Super Admin") {

    $sql = "
        SELECT *
        FROM admin_superadmin_accounts_tbl
        WHERE account_id = '$id'
        AND role = '$role'
    ";

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
    $profile_picture = $user['profile_picture'] ?: "default.png";
    $account_status = "Active";

} else {
    die("Invalid role.");
}

if (isset($_POST['update'])) {

    $f_name = mysqli_real_escape_string($conn, $_POST['f_name']);
    $m_name = mysqli_real_escape_string($conn, $_POST['m_name']);
    $l_name = mysqli_real_escape_string($conn, $_POST['l_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = $_POST['password'];

    $profile_picture = $_POST['old_profile_picture'] ?? 'default.png';

    // Upload new profile picture
    if (
        isset($_FILES['profile_picture']) &&
        $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK
    ) {
        $tmp_name = $_FILES['profile_picture']['tmp_name'];
        $image_info = getimagesize($tmp_name);

        if ($image_info === false) {
            die("Please upload a valid image.");
        }

        $allowed_types = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_GIF  => 'gif',
            IMAGETYPE_WEBP => 'webp'
        ];

        $image_type = $image_info[2];

        if (!isset($allowed_types[$image_type])) {
            die("Only JPG, PNG, GIF, and WEBP images are allowed.");
        }

        if ($_FILES['profile_picture']['size'] > 5 * 1024 * 1024) {
            die("Image must not exceed 5 MB.");
        }

        $upload_dir = __DIR__ . '/../uploads/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $new_file_name = bin2hex(random_bytes(16)) . '.' . $allowed_types[$image_type];

        if (!move_uploaded_file($tmp_name, $upload_dir . $new_file_name)) {
            die("Failed to upload profile picture.");
        }

        $profile_picture = $new_file_name;
    }

    $profile_picture_sql = mysqli_real_escape_string($conn, $profile_picture);

    if ($role == "Customer") {

        $status = mysqli_real_escape_string(
            $conn,
            $_POST['account_status']
        );

        $update_customer = "
            UPDATE customer_tbl
            SET f_name = '$f_name',
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

        if (!empty($new_password)) {

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $hashed_password = mysqli_real_escape_string($conn, $hashed_password);

            $update_user = "
                UPDATE user_accounts_tbl
                SET email = '$email',
                    `password` = '$hashed_password',
                    profile_picture = '$profile_picture_sql'
                WHERE user_id = '$user_id'
            ";

        } else {

            $update_user = "
                UPDATE user_accounts_tbl
                SET email = '$email',
                    profile_picture = '$profile_picture_sql'
                WHERE user_id = '$user_id'
            ";
        }

        mysqli_query($conn, $update_user);

    } else {

        if (!empty($new_password)) {

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $hashed_password = mysqli_real_escape_string($conn, $hashed_password);

            $update_admin = "
                UPDATE admin_superadmin_accounts_tbl
                SET f_name = '$f_name',
                    m_name = '$m_name',
                    l_name = '$l_name',
                    email = '$email',
                    `password` = '$hashed_password',
                    profile_picture = '$profile_picture_sql'
                WHERE account_id = '$id'
                AND role = '$role'
            ";

        } else {

            $update_admin = "
                UPDATE admin_superadmin_accounts_tbl
                SET f_name = '$f_name',
                    m_name = '$m_name',
                    l_name = '$l_name',
                    email = '$email',
                    profile_picture = '$profile_picture_sql'
                WHERE account_id = '$id'
                AND role = '$role'
            ";
        }

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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <title>Edit User</title>
</head>

<body>

<?php include '../admin/admin_sidebar_header_profile.php'; ?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3>Edit <?php echo htmlspecialchars($role); ?> Account</h3>
        </div>

        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="f_name" class="form-control"
                           value="<?php echo htmlspecialchars($f_name); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="m_name" class="form-control"
                           value="<?php echo htmlspecialchars($m_name); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="l_name" class="form-control"
                           value="<?php echo htmlspecialchars($l_name); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control"
                           value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="text" name="password"class="form-control" value="<?php echo($password); ?>" >
                    </div>
                <div class="mb-3">
                    <label class="form-label">Profile Picture</label>
                    <br>
                    <img
                        src="../uploads/<?php echo rawurlencode(basename($profile_picture)); ?>"
                        alt="Profile Picture"
                        width="100"
                        height="100"
                        class="rounded-circle mb-2"
                        style="object-fit: cover;" >

                    <input type="hidden" name="old_profile_picture"
                           value="<?php echo htmlspecialchars($profile_picture); ?>">
                    <input type="file" name="profile_picture"
                           class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small class="text-muted">JPG, PNG, GIF, or WEBP. Maximum 5 MB.</small>
                </div>
                <?php if ($role == "Customer") { ?>
                    <div class="mb-3">
                        <label class="form-label">Account Status</label>
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

                <button type="submit" name="update" class="btn btn-success">
                    Update Account
                </button>

                <a href="delete_user_account.php?record_id=<?php echo urlencode($id); ?>&role=<?php echo urlencode($role); ?>"
                   class="btn btn-danger"
                   onclick="return confirm('Are you sure you want to delete this account?');">
                    Delete Account
                </a>

                <a href="../admin/admin_user_management.php" class="btn btn-secondary">
                    Cancel
                </a>

            </form>
        </div>
    </div>
</div>

</body>
</html>