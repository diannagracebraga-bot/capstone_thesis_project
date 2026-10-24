<?php
require_once __DIR__ . '/require_super_admin.php';

if (isset($_POST['submit'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $f_name = mysqli_real_escape_string($conn, $_POST['f_name']);
    $m_name = mysqli_real_escape_string($conn, $_POST['m_name']);
    $l_name = mysqli_real_escape_string($conn, $_POST['l_name']);
    $role = "Super Admin";
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);

    // Default profile photo
    $profile_photo = "";
    // Profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['name'] != "") {

        $folder = __DIR__ . '/../uploads/admin_profiles/';

        // Check file size
        if ($_FILES['profile_photo']['size'] > 2000000) {
            die("Photo must not exceed 2 MB.");
        }

        // Check image type
        $image = getimagesize($_FILES['profile_photo']['tmp_name']);
        $allowed_types = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if ($image === false || !isset($allowed_types[$image['mime']])) {
            die("Only valid JPG, PNG, and WebP images are allowed.");
        }

        // Create folder if it does not exist
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        // Generate unique filename
        $file_name = uniqid('superadmin_') . '.' . $allowed_types[$image['mime']];

        // Upload photo
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $folder . $file_name)) {
            $profile_photo = $file_name;
        } else {
            die("Photo upload failed.");
        }
    }

    $profile_photo = mysqli_real_escape_string($conn, $profile_photo);

    // Save Super Admin account
    $sql = "INSERT INTO admin_superadmin_accounts_tbl
            (email, password, f_name, m_name, l_name, role, contact_number, profile_photo)
            VALUES
            ('$email', '$password', '$f_name', '$m_name', '$l_name', '$role', '$contact_number', '$profile_photo')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Super Admin created successfully!');
                window.location.href='admin_user_management.php';
              </script>";
        exit();
    } else {
        die("Error: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Create Super Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet" href="../css/admin_add_admin.css?v=layout-fix-2">
</head>

<body>

<?php include 'admin_sidebar_header_profile.php'; ?>

<div class="card">
    <div class="card-body">
        <div class="container mt-4">

            <h2>Create Super Admin</h2>

            <form method="POST" enctype="multipart/form-data">

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

                <div class="mb-3">
                    <label>Profile Photo (Optional)</label>
                    <input type="file" name="profile_photo"
                           class="form-control"
                           accept=".jpg,.jpeg,.png,.webp">

                    <small class="text-muted">
                        Accepted formats: JPG, PNG, WebP. Maximum size: 2 MB.
                    </small>
                </div>

                 <div class="button-container">
                    <button type="submit" name="submit" class="btn btn-success">
                         Create
                     </button>
            </div>

            </form>
        </div>
    </div>
</div>
</body>
</html>