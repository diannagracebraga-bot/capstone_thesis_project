<?php
require_once __DIR__ . '/../admin/require_super_admin.php';
if (!isset($_GET['id'], $_GET['role'])) {
    die("Invalid account.");
}
$id = mysqli_real_escape_string($conn, $_GET['id']);
$role = trim($_GET['role']);

if (!in_array($role, ['Customer', 'Admin', 'Super Admin'], true)) {
    die("Invalid role.");
}

$f_name = "";
$m_name = "";
$l_name = "";
$email = "";
$account_status = "";
$profile_photo = "default.png";
$upload_dir = __DIR__ . '/../uploads/admin_profiles/';
$upload_url = '../uploads/admin_profiles/';
if ($role === "Customer") {
    $sql = "
        SELECT c.customer_id, c.f_name, c.m_name, c.l_name,
               c.connection_status, u.user_id, u.email,
               u.profile_photo
        FROM customer_tbl c
        INNER JOIN user_accounts_tbl u ON c.user_id = u.user_id
        WHERE c.customer_id = '$id'
    ";
} else {
    $sql = "
        SELECT *
        FROM admin_superadmin_accounts_tbl
        WHERE account_id = '$id'
          AND role = '" . mysqli_real_escape_string($conn, $role) . "'
    ";
}
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Account query failed: " . mysqli_error($conn));
}
if (mysqli_num_rows($result) === 0) {
    die("Account not found.");
}
$user = mysqli_fetch_assoc($result);
$f_name = $user['f_name'] ?? '';
$m_name = $user['m_name'] ?? '';
$l_name = $user['l_name'] ?? '';
$email = $user['email'] ?? '';
$profile_photo = !empty($user['profile_photo'])
    ? basename($user['profile_photo'])
    : 'default.png';
$account_status = $role === 'Customer'
    ? ($user['connection_status'] ?? 'Disconnected')
    : 'Active';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $f_name = mysqli_real_escape_string($conn, trim($_POST['f_name'] ?? ''));
    $m_name = mysqli_real_escape_string($conn, trim($_POST['m_name'] ?? ''));
    $l_name = mysqli_real_escape_string($conn, trim($_POST['l_name'] ?? ''));
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['password'] ?? '';

    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|ph|net|org|edu|gov)$/i', $email)
    ) {
        echo "<script>
                alert('Invalid email! Please enter a valid email ending in .com, .ph, .net, .org, .edu, or .gov.');
                window.history.back();
              </script>";
        exit;
    }

    $email = mysqli_real_escape_string($conn, $email);
    $profile_photo = basename($_POST['old_profile_photo'] ?? 'default.png');
   
    if (isset($_FILES['profile_photo']) &&
        $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
            die("Photo upload failed. Error code: " .
                (int) $_FILES['profile_photo']['error']);
        }
        if ($_FILES['profile_photo']['size'] > 5 * 1024 * 1024) {
            die("Image must not exceed 5 MB.");
        }
        $tmp_name = $_FILES['profile_photo']['tmp_name'];
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
        if (!is_dir($upload_dir) &&
            !mkdir($upload_dir, 0755, true) &&
            !is_dir($upload_dir)) {
            die("Unable to create the profile photo folder.");
        }
        $new_file_name = bin2hex(random_bytes(16)) .
            '.' . $allowed_types[$image_type];
        if (!move_uploaded_file($tmp_name, $upload_dir . $new_file_name)) {
            die("Failed to save the profile photo.");
        }
        $profile_photo = $new_file_name;
    }
    $profile_photo_sql = mysqli_real_escape_string($conn, $profile_photo);
    if ($role === "Customer") {
        $status = $_POST['account_status'] ?? '';
        if (!in_array($status, ['Connected', 'Disconnected'], true)) {
            die("Invalid customer account status.");
        }
        $status_sql = mysqli_real_escape_string($conn, $status);
        $update_customer = "
            UPDATE customer_tbl
            SET f_name = '$f_name',
                m_name = '$m_name',
                l_name = '$l_name',
                connection_status = '$status_sql'
            WHERE customer_id = '$id'
        ";
        if (!mysqli_query($conn, $update_customer)) {
            die("Customer update failed: " . mysqli_error($conn));
        }
        $user_id = mysqli_real_escape_string(
            $conn,
            $user['user_id']
        );
        if ($new_password !== '') {
            $hashed_password = mysqli_real_escape_string(
                $conn,
                password_hash($new_password, PASSWORD_DEFAULT)
            );
            $update_user = "
                UPDATE user_accounts_tbl
                SET email = '$email',
                    `password` = '$hashed_password',
                    profile_photo = '$profile_photo_sql'
                WHERE user_id = '$user_id'
            ";
        } else {
            $update_user = "
                UPDATE user_accounts_tbl
                SET email = '$email',
                    profile_photo = '$profile_photo_sql'
                WHERE user_id = '$user_id'
            ";
        }
        if (!mysqli_query($conn, $update_user)) {
            die("Customer login account update failed: " .
                mysqli_error($conn));
        }
    } else {
        $role_sql = mysqli_real_escape_string($conn, $role);
        if ($new_password !== '') {
            $hashed_password = mysqli_real_escape_string(
                $conn,
                password_hash($new_password, PASSWORD_DEFAULT)
            );
            $update_admin = "
                UPDATE admin_superadmin_accounts_tbl
                SET f_name = '$f_name',
                    m_name = '$m_name',
                    l_name = '$l_name',
                    email = '$email',
                    `password` = '$hashed_password',
                    profile_photo = '$profile_photo_sql'
                WHERE account_id = '$id'
                  AND role = '$role_sql'
            ";
        } else {
            $update_admin = "
                UPDATE admin_superadmin_accounts_tbl
                SET f_name = '$f_name',
                    m_name = '$m_name',
                    l_name = '$l_name',
                    email = '$email',
                    profile_photo = '$profile_photo_sql'
                WHERE account_id = '$id'
                  AND role = '$role_sql'
            ";
        }
        if (!mysqli_query($conn, $update_admin)) {
            die("Admin account update failed: " . mysqli_error($conn));
        }
    }
    echo "<script>
        alert('Account updated successfully!');
        window.location.href='../admin/admin_user_management.php';
    </script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <title>Edit User Account</title>
    <style>
        body {
            background: #FFFFFF;
        }
        body > h1 { color: #000000 !important; }
        .container.edit-container {
            box-sizing: border-box;
            width: calc(100% - 330px) !important;
            max-width: none !important;
            margin: 24px 26px 40px 304px !important;
            padding: 0 !important;
        }
        .edit-container > .card {
            overflow: hidden;
            border: 1px solid #dce6f1;
            border-radius: 14px;
            background: #FFFFFF;
            box-shadow: 0 5px 16px rgba(30, 74, 121, .09) !important;
        }
        .edit-container .card-header.bg-primary {
            padding: 20px 28px;
            border: 0;
            border-bottom: 1px solid #dce6f1;
            background: #FFFFFF !important;
            color: #123d70 !important;
        }
        .edit-container .card-header h3 {
            margin: 0;
            color: #123d70;
            font-size: 24px;
            font-weight: 700;
        }
        .edit-container .card-body { padding: 28px; }
        .edit-container .form-label {
            margin-bottom: 6px;
            color: #23496f;
            font-weight: 700;
        }
        .edit-container .form-control,
        .edit-container .form-select {
            min-height: 42px;
            border-color: #ccd9e7;
            border-radius: 8px;
            color: #000000;
        }
        .edit-container .form-control:focus,
        .edit-container .form-select:focus {
            border-color: #267bd0;
            box-shadow: 0 0 0 3px rgba(28, 117, 205, .14);
        }
        .edit-user-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .edit-user-actions .btn { min-width: 130px; padding: 10px 17px; border-radius: 8px; font-weight: 700; }
        .edit-user-actions .btn-primary { border-color: #315a7d; background: #315a7d; }
        .edit-user-actions .btn-primary:hover { border-color: #264762; background: #264762; }
        .edit-user-actions .btn-secondary { border-color: #ccd9e7; background: #FFFFFF; color: #315a7d; }
        .edit-user-actions .btn-secondary:hover { background: #f1f6fc; color: #264762; }
        @media (max-width: 1024px) {
            .container.edit-container {
                width: auto !important;
                margin: 15px 16px 30px !important;
            }
            .edit-container .card-body { padding: 20px; }
            .edit-container .card-header.bg-primary { padding: 18px 20px; }
        }
        @media (max-width: 520px) {
            .container.edit-container { margin: 12px 10px 24px !important; }
            .edit-container .card-body { padding: 16px; }
            .edit-user-actions .btn { flex: 1 1 130px; }
        }
        .profile-preview {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #dee2e6;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
<?php include '../admin/admin_sidebar_header_profile.php'; ?>
<main class="container edit-container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">
                Edit <?php echo htmlspecialchars($role); ?> Account
            </h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input
                        type="text"
                        name="f_name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($f_name); ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Middle Name</label>
                    <input
                        type="text"
                        name="m_name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($m_name); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input
                        type="text"
                        name="l_name"
                        class="form-control"
                        value="<?php echo htmlspecialchars($l_name); ?>"
                        required>
                </div>
            
<div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input
        type="email"
        name="email"
        id="email"
        class="form-control"
        value="<?php echo htmlspecialchars($email); ?>"
        pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|ph|net|org|edu|gov)"
        title="Please enter a valid email address."
        required>
</div>

               
<div class="mb-3">
    <label class="form-label">New Password</label>
    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password" minlength="8"
        pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}"
        title="Password must contain at least 8 characters, including an uppercase letter, a lowercase letter, a number, and a special character."
        autocomplete="new-password">
    <small class="text-muted">
        Leave blank if you do not want to change the password.
    </small>
</div>

                <div class="mb-3">
                    <label class="form-label">Profile Photo</label>
                    <br>
                    <?php
                    $photo_file = basename($profile_photo);
                    $photo_disk_path = $upload_dir . $photo_file;
                    $photo_web_path = $upload_url . rawurlencode($photo_file);
                    if ($photo_file !== 'default.png' &&
                        file_exists($photo_disk_path)) {
                    ?>
                        <img
                            src="<?php echo htmlspecialchars($photo_web_path); ?>"
                            alt="Current Profile Photo"
                            class="profile-preview">
                    <?php
                    } else {
                    ?>
                        <div class="mb-2">
                            <span class="text-muted">
                                No uploaded profile photo found.
                            </span>
                        </div>
                    <?php
                    }
                    ?>
                    <input
                        type="hidden"
                        name="old_profile_photo"
                        value="<?php echo htmlspecialchars($profile_photo); ?>">
                    <input
                        type="file"
                        name="profile_photo"
                        class="form-control"
                        accept="image/jpeg,image/png,image/gif,image/webp">

                    <small class="text-muted">
                        JPG, PNG, GIF, or WEBP. Maximum 5 MB.
                    </small>
                </div>
                <?php if ($role === "Customer") { ?>
                    <div class="mb-3">
                        <label class="form-label">Account Status</label>
                        <select
                            name="account_status"
                            class="form-select"
                            required>
                            <option value="Connected"
                                <?php echo $account_status === "Connected"
                                    ? "selected" : ""; ?>>
                                Connected
                            </option>
                            <option value="Disconnected"
                                <?php echo $account_status === "Disconnected"
                                    ? "selected" : ""; ?>>
                                Disconnected
                            </option>
                        </select>
                    </div>
                <?php } ?>
                <div class="edit-user-actions">
                <button type="submit" name="update" class="btn btn-primary"> Update Account </button>
                <a href="delete_user_account.php?record_id=<?php echo urlencode($id); ?>&role=<?php echo urlencode($role); ?>" class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to delete this account?');"> Delete Account </a>
                    <a href="../admin/admin_user_management.php" class="btn btn-secondary"> Cancel </a>
                </div>
            </form>
        </div>
    </div>
</main>

</body>
</html>
