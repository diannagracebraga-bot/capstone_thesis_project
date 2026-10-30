<?php
require_once __DIR__ . '/require_super_admin.php';

if (isset($_POST['submit'])) {

       $email = trim($_POST['email'] ?? '');

    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|ph|net|org|edu|gov)$/i', $email)
    ) {
        echo "<script>
                alert('Invalid email! Please enter a valid email, such as name@gmail.com.');
                window.history.back();
              </script>";
        exit;
    }

    $password = $_POST['password'] ?? '';

    if (
        strlen($password) < 8 ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[^A-Za-z0-9]/', $password)
    ) {
        echo "<script>
                alert('Weak password! Use at least 8 characters, including an uppercase letter, a lowercase letter, a number, and a special character.');
                window.history.back();
              </script>";
        exit;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    $f_name = mysqli_real_escape_string($conn, $_POST['f_name']);
    $m_name = mysqli_real_escape_string($conn, $_POST['m_name']);
    $l_name = mysqli_real_escape_string($conn, $_POST['l_name']);
    $role = "Admin";
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
        $file_name = uniqid('admin_') . '.' . $allowed_types[$image['mime']];

        // Upload photo
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $folder . $file_name)) {
            $profile_photo = $file_name;
        } else {
            die("Photo upload failed.");
        }
    }

    $profile_photo = mysqli_real_escape_string($conn, $profile_photo);

    // Save Admin account
    $sql = "INSERT INTO admin_superadmin_accounts_tbl
            (email, password, f_name, m_name, l_name, role, contact_number, profile_photo)
            VALUES
            ('$email', '$password', '$f_name', '$m_name', '$l_name', '$role', '$contact_number', '$profile_photo')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Admin created successfully!');
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

    <title>Create Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
<link rel="stylesheet" href="../css/admin_add_admin.css?v=password-toggle-1">
</head>

<body>

<?php include 'admin_sidebar_header_profile.php'; ?>

<main class="create-admin-page">
    <section class="card create-admin-card">
    <div class="card-body create-admin-card-body">
        <div class="create-admin-form">

            <h2>Create Admin</h2>

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email"
                           class="form-control"
                           pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|ph|net|org|edu|gov)"
                           title="Please enter a valid email address."
                           required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-field">
                        <input type="password" name="password" id="password"
                               class="form-control"
                               minlength="8"
                               pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}"
                               title="Password must contain at least 8 characters, including an uppercase letter, a lowercase letter, a number, and a special character."
                               autocomplete="new-password"
                               required>
                        <button type="button" class="password-visibility-toggle" aria-label="Show password" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="f_name" class="form-label">First Name</label>
                    <input type="text" name="f_name" id="f_name"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="m_name" class="form-label">Middle Name</label>
                    <input type="text" name="m_name" id="m_name"
                           class="form-control">
                </div>

                <div class="mb-3">
                    <label for="l_name" class="form-label">Last Name</label>
                    <input type="text" name="l_name" id="l_name"
                           class="form-control" required>
                </div>
                 
                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="tel" name="contact_number" id="contact_number"  class="form-control"  maxlength="11" pattern="09[0-9]{9}"
                                 title="Please enter a valid 11-digit contact number."
                             required>
                        </div>      

                <div class="mb-3">
                    <label for="profile_photo" class="form-label">Profile Photo (Optional)</label>
                    <input type="file" name="profile_photo" id="profile_photo"
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
</section>
</main>

</body>
</html>
