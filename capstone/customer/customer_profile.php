<?php
include '../admin/check_customer_access.php';

include '../database/database_connection.php';


$user_id = (int) ($_SESSION['user_id'] ?? 0);
$message = "";
$message_type = 'success';
$editAction = in_array($_GET['edit'] ?? '', ['photo', 'contact', 'password'], true) ? $_GET['edit'] : '';
$editPhoto = $editAction === 'photo';
$editContact = $editAction === 'contact';
$editPassword = $editAction === 'password';

$photoColumnResult = mysqli_query($conn, "SHOW COLUMNS FROM user_accounts_tbl LIKE 'profile_photo'");
$profilePhotoAvailable = $photoColumnResult && mysqli_num_rows($photoColumnResult) > 0;
if (!$profilePhotoAvailable) {
    $message = 'Photo upload is unavailable until the customer profile SQL migration is imported.';
    $message_type = 'warning';
}

$existingPhotoQuery = $profilePhotoAvailable
    ? mysqli_query($conn, "SELECT profile_photo FROM user_accounts_tbl WHERE user_id = $user_id LIMIT 1")
    : false;
$existingPhotoRow = $existingPhotoQuery ? mysqli_fetch_assoc($existingPhotoQuery) : null;
$oldProfilePhoto = basename((string) ($existingPhotoRow['profile_photo'] ?? ''));

/* Validate and save profile changes and an optional photo upload. */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $profileAction = $_POST['profile_action'] ?? '';
    $contact_number = trim($_POST['contact_number'] ?? '');
    $newPhotoName = null;
    $uploadError = '';
    $photoUpload = $profileAction === 'photo' ? ($_FILES['profile_photo'] ?? null) : null;
    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
    $passwordChangeRequested = $profileAction === 'password';
    $passwordHash = null;

    if (!in_array($profileAction, ['photo', 'contact', 'password'], true)) {
        $uploadError = 'Invalid profile update request.';
    } elseif ($profileAction === 'contact' && !array_key_exists('contact_number', $_POST)) {
        $uploadError = 'The submitted profile data is incomplete. Please try again.';
    } elseif ($profileAction === 'contact' && !preg_match('/^09[0-9]{9}$/', $contact_number)) {
        $uploadError = 'Contact number must contain 11 digits and start with 09.';
    } elseif ($passwordChangeRequested && ($currentPassword === '' || $newPassword === '' || $confirmPassword === '')) {
        $uploadError = 'Enter your current password, new password, and password confirmation to change your password.';
    } elseif ($passwordChangeRequested && strlen($newPassword) < 12) {
        $uploadError = 'The new password must be at least 12 characters long.';
    } elseif ($passwordChangeRequested && strlen($newPassword) > 72) {
        $uploadError = 'The new password must not exceed 72 characters.';
    } elseif ($passwordChangeRequested && (
        !preg_match('/[a-z]/', $newPassword)
        || !preg_match('/[A-Z]/', $newPassword)
        || !preg_match('/[0-9]/', $newPassword)
        || !preg_match('/[^A-Za-z0-9]/', $newPassword)
    )) {
        $uploadError = 'Use uppercase and lowercase letters, a number, and a special character in your new password.';
    } elseif ($passwordChangeRequested && !hash_equals($newPassword, $confirmPassword)) {
        $uploadError = 'The new password and confirmation do not match.';
    } elseif ($passwordChangeRequested) {
        $storedPasswordStatement = mysqli_prepare($conn, "SELECT `password` FROM user_accounts_tbl WHERE user_id = ? LIMIT 1");
        if ($storedPasswordStatement) {
            mysqli_stmt_bind_param($storedPasswordStatement, 'i', $user_id);
            mysqli_stmt_execute($storedPasswordStatement);
            $storedPasswordResult = mysqli_stmt_get_result($storedPasswordStatement);
            $storedPasswordRow = $storedPasswordResult ? mysqli_fetch_assoc($storedPasswordResult) : null;
        } else {
            $storedPasswordRow = null;
        }

        if (!$storedPasswordRow || !password_verify($currentPassword, (string) $storedPasswordRow['password'])) {
            $uploadError = 'The current password is incorrect.';
        } elseif (password_verify($newPassword, (string) $storedPasswordRow['password'])) {
            $uploadError = 'Choose a new password that is different from your current password.';
        } else {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        }
    }

    if ($uploadError === '' && $profileAction === 'photo' && (!$photoUpload || ($photoUpload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
        $uploadError = 'Choose a profile photo before saving.';
    }

    if ($uploadError === '' && $profileAction === 'photo' && !$profilePhotoAvailable) {
        $uploadError = 'Import the customer profile SQL migration before uploading a photo.';
    }

    if ($uploadError === '' && $photoUpload && ($photoUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        if (($photoUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $uploadError = 'The photo could not be uploaded. Please select it again.';
        } elseif (($photoUpload['size'] ?? 0) < 1 || $photoUpload['size'] > 5 * 1024 * 1024) {
            $uploadError = 'Please choose an image smaller than 5 MB.';
        } elseif (!is_uploaded_file($photoUpload['tmp_name'])) {
            $uploadError = 'The selected photo upload is invalid.';
        } else {
            $imageInfo = @getimagesize($photoUpload['tmp_name']);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedMime = $finfo ? finfo_file($finfo, $photoUpload['tmp_name']) : false;
            if ($finfo) {
                finfo_close($finfo);
            }

            $allowedPhotoTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];
            $validDimensions = $imageInfo !== false
                && $imageInfo[0] <= 5000
                && $imageInfo[1] <= 5000
                && ($imageInfo[0] * $imageInfo[1]) <= 25000000;

            if (!$imageInfo || !$detectedMime || !isset($allowedPhotoTypes[$detectedMime])
                || ($imageInfo['mime'] ?? '') !== $detectedMime || !$validDimensions) {
                $uploadError = 'Please upload a valid JPG, PNG, or WEBP photo (maximum 5 MB).';
            }
        }
    }

    if ($uploadError !== '') {
        $message = $uploadError;
        $message_type = 'danger';
    } else {
        if ($photoUpload && ($photoUpload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $uploadDirectory = __DIR__ . '/../uploads/admin_profiles/';
            if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
                $message = 'Unable to prepare the photo folder. Please try again later.';
                $message_type = 'danger';
            } else {
                $newPhotoName = bin2hex(random_bytes(16)) . '.' . $allowedPhotoTypes[$detectedMime];
                if (!move_uploaded_file($photoUpload['tmp_name'], $uploadDirectory . $newPhotoName)) {
                    $message = 'Unable to save the photo. Please try again.';
                    $message_type = 'danger';
                    $newPhotoName = null;
                }
            }
        }

        if ($message_type !== 'danger') {
            mysqli_begin_transaction($conn);
            $customerSaved = true;
            $accountSaved = true;

            if ($profileAction === 'contact') {
                $customerUpdate = mysqli_prepare($conn, "UPDATE customer_tbl SET contact_number = ? WHERE user_id = ?");
                $customerSaved = false;
            }
            if ($profileAction === 'contact' && $customerUpdate) {
                mysqli_stmt_bind_param($customerUpdate, 'si', $contact_number, $user_id);
                $customerSaved = mysqli_stmt_execute($customerUpdate);
            }

            if ($profileAction === 'photo' || $profileAction === 'password') {
                $accountSaved = false;
                if ($newPhotoName !== null && $passwordHash !== null) {
                    $accountUpdate = mysqli_prepare($conn, "UPDATE user_accounts_tbl SET profile_photo = ?, `password` = ? WHERE user_id = ?");
                    if ($accountUpdate) {
                        mysqli_stmt_bind_param($accountUpdate, 'ssi', $newPhotoName, $passwordHash, $user_id);
                    }
                } elseif ($newPhotoName !== null) {
                    $accountUpdate = mysqli_prepare($conn, "UPDATE user_accounts_tbl SET profile_photo = ? WHERE user_id = ?");
                    if ($accountUpdate) {
                        mysqli_stmt_bind_param($accountUpdate, 'si', $newPhotoName, $user_id);
                    }
                } else {
                    $accountUpdate = mysqli_prepare($conn, "UPDATE user_accounts_tbl SET `password` = ? WHERE user_id = ?");
                    if ($accountUpdate) {
                        mysqli_stmt_bind_param($accountUpdate, 'si', $passwordHash, $user_id);
                    }
                }
                $accountSaved = isset($accountUpdate) && $accountUpdate && mysqli_stmt_execute($accountUpdate);
            }

            if ($customerSaved && $accountSaved) {
                mysqli_commit($conn);
                if ($newPhotoName !== null && $oldProfilePhoto !== '' && $oldProfilePhoto !== $newPhotoName) {
                    $oldPhotoPath = __DIR__ . '/../uploads/admin_profiles/' . $oldProfilePhoto;
                    if (is_file($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                header("Location: customer_profile.php?updated=1");
                exit();
            }

            mysqli_rollback($conn);

            if ($newPhotoName !== null) {
                $newPhotoPath = __DIR__ . '/../uploads/admin_profiles/' . $newPhotoName;
                if (is_file($newPhotoPath)) {
                    unlink($newPhotoPath);
                }
            }
            $message = 'Failed to update profile. Please try again.';
            $message_type = 'danger';
        }
    }
}

/* Success Message */
if (isset($_GET['updated'])) {
    $message = "Profile updated successfully.";
}

/* Get Customer Information */
$query = mysqli_query($conn, "
SELECT
    c.*,
    u.email,
    u.role,
    " . ($profilePhotoAvailable ? "u.profile_photo" : "NULL AS profile_photo") . ",
    p.plan_name,
    p.internet_mbps,
    p.internet_price
FROM customer_tbl c
INNER JOIN user_accounts_tbl u
    ON c.user_id = u.user_id
LEFT JOIN internet_plan_tbl p
    ON c.internet_plan = p.plan_id
        WHERE c.user_id = '$user_id'
");

if (!$query) {
    die("Query Failed: " . mysqli_error($conn));
}

$customer = mysqli_fetch_assoc($query);

if (!$customer) {
    die("Customer record not found.");
}

$formatCustomerName = static function ($value) {
    $value = trim((string) $value);
    return function_exists('mb_convert_case')
        ? mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8')
        : ucwords(strtolower($value));
};
$displayFirstName = $formatCustomerName($customer['f_name'] ?? '');
$displayMiddleName = $formatCustomerName($customer['m_name'] ?? '');
$displayLastName = $formatCustomerName($customer['l_name'] ?? '');

$email_address = $customer['email'];
$role = $customer['role'];
$profilePhotoName = basename((string) ($customer['profile_photo'] ?? ''));
$profilePhotoPath = __DIR__ . '/../uploads/admin_profiles/' . $profilePhotoName;
$hasProfilePhoto = $profilePhotoName !== '' && is_file($profilePhotoPath);
$profilePhotoUrl = $hasProfilePhoto ? '../uploads/admin_profiles/' . rawurlencode($profilePhotoName) : '';

$internet_plan = $customer['plan_name'] . " (" .
    $customer['internet_mbps'] . " Mbps) - ₱" .
    $customer['internet_price'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet"
          href="../css/customer_sidebar_header.css?v=customer-shell-14">

    <link rel="stylesheet"
          href="../css/customer_profile.css?v=17">

    <link rel="stylesheet" href="../css/system-font.css?v=1">
</head>

<body>

<?php include 'customer_sidebar_header.php'; ?>

<main class="profile-content">

    <div class="card">

        <div class="card-body">

            <section class="profile-card">

                <div class="profile-title-row">

                    <h2>Customer Profile</h2>
                    <p>View your account information and update your contact number or password.</p>

                </div>


                <?php if ($message != ""): ?>

                    <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>">

                        <?php echo htmlspecialchars($message); ?>

                    </div>

                <?php endif; ?>


                <div class="profile-layout">
    <div class="profile-bento-grid">
        <section class="profile-bento-card profile-identity-card">
            <div class="profile-avatar-wrap">
                <?php if ($hasProfilePhoto): ?>
                    <img src="<?php echo htmlspecialchars($profilePhotoUrl); ?>" alt="Customer profile photo" id="profilePhotoPreview">
                <?php else: ?>
                    <div class="profile-avatar-placeholder" id="profilePhotoPlaceholder" aria-label="No profile photo selected"><i class="bi bi-person-fill" aria-hidden="true"></i></div>
                <?php endif; ?>
            </div>
            <div class="profile-identity-copy">
                <h3><?php echo htmlspecialchars(trim($displayFirstName . ' ' . $displayLastName)); ?></h3>
                <span class="profile-role-badge"><?php echo htmlspecialchars(ucfirst($role)); ?></span>
                <p><?php echo htmlspecialchars($email_address); ?></p>
                <?php if ($editPhoto): ?>
                    <?php if ($profilePhotoAvailable): ?>
                        <form method="POST" enctype="multipart/form-data" class="profile-photo-form">
                            <input type="hidden" name="profile_action" value="photo">
                            <label class="profile-photo-choose" for="profile_photo"><i class="bi bi-camera-fill" aria-hidden="true"></i> Choose photo</label>
                            <input type="file" name="profile_photo" id="profile_photo" class="profile-photo-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                            <small class="profile-photo-hint">JPG, PNG, or WEBP · up to 5 MB</small>
                            <div class="profile-card-actions"><a href="customer_profile.php" class="profile-link-button">Cancel</a><button type="submit" class="profile-action-button">Save photo</button></div>
                        </form>
                    <?php else: ?>
                        <small class="profile-photo-hint">Import the profile SQL file to enable photo uploads.</small>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="customer_profile.php?edit=photo" class="profile-link-button"><i class="bi bi-camera" aria-hidden="true"></i> Change photo</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="profile-bento-card profile-account-card">
            <div class="profile-bento-heading"><span class="profile-bento-icon"><i class="bi bi-person-vcard" aria-hidden="true"></i></span><div><h3>Account details</h3><p>Your login and customer account information.</p></div></div>
            <div class="profile-bento-fields profile-account-fields">
                <div class="profile-bento-field"><label for="profile_account_number">Account number</label><input id="profile_account_number" class="form-control" value="<?php echo htmlspecialchars((string) $customer['account_number']); ?>" readonly></div>
                <div class="profile-bento-field"><label for="profile_email">Email address</label><input id="profile_email" type="email" class="form-control" value="<?php echo htmlspecialchars((string) $email_address); ?>" readonly></div>
            </div>
        </section>

        <section class="profile-bento-card profile-personal-card">
            <div class="profile-bento-heading"><span class="profile-bento-icon profile-icon-pink"><i class="bi bi-person" aria-hidden="true"></i></span><div><h3>Personal information</h3><p>Your registered name and personal details.</p></div></div>
            <div class="profile-bento-fields profile-personal-fields">
                <div class="profile-bento-field"><label for="profile_first_name">First name</label><input id="profile_first_name" class="form-control" value="<?php echo htmlspecialchars($displayFirstName); ?>" readonly></div>
                <div class="profile-bento-field"><label for="profile_middle_name">Middle name</label><input id="profile_middle_name" class="form-control" value="<?php echo htmlspecialchars($displayMiddleName); ?>" readonly></div>
                <div class="profile-bento-field"><label for="profile_last_name">Last name</label><input id="profile_last_name" class="form-control" value="<?php echo htmlspecialchars($displayLastName); ?>" readonly></div>
                <div class="profile-bento-field"><label for="profile_age">Age</label><input id="profile_age" class="form-control" value="<?php echo htmlspecialchars((string) $customer['age']); ?>" readonly></div>
                <div class="profile-bento-field"><label for="profile_sex">Sex</label><input id="profile_sex" class="form-control" value="<?php echo htmlspecialchars((string) $customer['sex']); ?>" readonly></div>
            </div>
        </section>

        <section class="profile-bento-card profile-service-card">
            <div class="profile-bento-heading"><span class="profile-bento-icon profile-icon-green"><i class="bi bi-wifi" aria-hidden="true"></i></span><div><h3>Internet service</h3><p>Your active plan and connection details.</p></div></div>
            <div class="profile-bento-fields">
                <div class="profile-bento-field"><label for="profile_plan">Internet plan</label><input id="profile_plan" class="form-control" value="<?php echo htmlspecialchars($internet_plan); ?>" readonly></div>
                <div class="profile-bento-field"><label for="profile_connection_status">Connection status</label><input id="profile_connection_status" class="form-control" value="<?php echo htmlspecialchars((string) $customer['connection_status']); ?>" readonly></div>
                <div class="profile-bento-field"><label for="profile_due_date">Due date</label><input id="profile_due_date" class="form-control" value="<?php echo htmlspecialchars((string) $customer['due_date']); ?>" readonly></div>
            </div>
        </section>

        <section class="profile-bento-card profile-address-card">
            <div class="profile-bento-heading"><span class="profile-bento-icon profile-icon-purple"><i class="bi bi-geo-alt" aria-hidden="true"></i></span><div><h3>Service address</h3><p>Location registered to this account.</p></div></div>
            <div class="profile-bento-fields">
                <div class="profile-bento-field"><label for="profile_barangay">Barangay</label><input id="profile_barangay" class="form-control" value="<?php echo htmlspecialchars((string) $customer['barangay']); ?>" readonly></div>
                <div class="profile-bento-field"><label for="profile_house_address">House address</label><input id="profile_house_address" class="form-control" value="<?php echo htmlspecialchars(ucfirst((string) $customer['house_name'])); ?>" readonly></div>
            </div>
        </section>

        <section class="profile-bento-card profile-contact-card">
            <div class="profile-bento-heading"><span class="profile-bento-icon profile-icon-blue"><i class="bi bi-telephone" aria-hidden="true"></i></span><div><h3>Contact information</h3><p>Keep your contact number up to date.</p></div></div>
            <form method="POST" class="profile-independent-form">
                <input type="hidden" name="profile_action" value="contact">
                <div class="profile-bento-field">
                    <label for="profile_contact_number">Contact number</label>
                    <input id="profile_contact_number" type="tel" class="form-control" name="contact_number" value="<?php echo htmlspecialchars((string) $customer['contact_number']); ?>" <?php if ($editContact): ?>maxlength="11" pattern="09[0-9]{9}" title="Enter 11 digits starting with 09." required autocomplete="tel"<?php else: ?>readonly<?php endif; ?>>
                </div>
                <div class="profile-card-actions">
                    <?php if ($editContact): ?>
                        <a href="customer_profile.php" class="profile-link-button">Cancel</a><button type="submit" class="profile-action-button">Save number</button>
                    <?php else: ?>
                        <a href="customer_profile.php?edit=contact" class="profile-link-button"><i class="bi bi-pencil" aria-hidden="true"></i> Edit contact</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="profile-bento-card profile-password-card">
            <div class="profile-bento-heading"><span class="profile-bento-icon profile-icon-amber"><i class="bi bi-lock" aria-hidden="true"></i></span><div><h3>Password &amp; security</h3><p>Protect your account with a strong password.</p></div></div>
            <?php if ($editPassword): ?>
                <form method="POST" class="profile-independent-form">
                    <input type="hidden" name="profile_action" value="password">
                    <div class="profile-bento-fields profile-password-fields">
                        <div class="profile-bento-field profile-current-password"><label for="current_password">Current password</label><input id="current_password" type="password" class="form-control" name="current_password" autocomplete="current-password" required></div>
                        <div class="profile-bento-field"><label for="new_password">New password</label><input id="new_password" type="password" class="form-control" name="new_password" minlength="12" maxlength="72" autocomplete="new-password" required></div>
                        <div class="profile-bento-field"><label for="confirm_password">Confirm new password</label><input id="confirm_password" type="password" class="form-control" name="confirm_password" minlength="12" maxlength="72" autocomplete="new-password" required></div>
                    </div>
                    <p class="profile-password-hint">Use at least 12 characters with uppercase and lowercase letters, a number, and a special character.</p>
                    <div class="profile-card-actions"><a href="customer_profile.php" class="profile-link-button">Cancel</a><button type="submit" class="profile-action-button">Update password</button></div>
                </form>
            <?php else: ?>
                <div class="profile-password-protected"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i><span>••••••••••••</span><small>Your password is securely stored and cannot be viewed.</small></div>
                <div class="profile-card-actions"><a href="customer_profile.php?edit=password" class="profile-link-button"><i class="bi bi-pencil" aria-hidden="true"></i> Change password</a></div>
            <?php endif; ?>
        </section>

        <aside class="profile-bento-card profile-checklist-card">
            <div class="profile-bento-heading"><span class="profile-bento-icon profile-icon-green"><i class="bi bi-check2-circle" aria-hidden="true"></i></span><div><h3>Password requirements</h3><p>Your new password must include:</p></div></div>
            <ul class="profile-password-checklist">
                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> At least 12 characters</li>
                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Uppercase and lowercase letters</li>
                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> At least one number</li>
                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> At least one special character</li>
            </ul>
        </aside>
    </div>
</div>
            </section>

        </div>

    </div>

</main>
<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});

const profilePhotoInput = document.getElementById('profile_photo');
if (profilePhotoInput) {
    profilePhotoInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;
        let preview = document.getElementById('profilePhotoPreview');
        const placeholder = document.getElementById('profilePhotoPlaceholder');
        if (!preview) {
            preview = document.createElement('img');
            preview.id = 'profilePhotoPreview';
            preview.alt = 'Selected customer profile photo';
            document.querySelector('.profile-avatar-wrap').prepend(preview);
        }
        if (placeholder) placeholder.hidden = true;
        preview.src = URL.createObjectURL(file);
    });
}
</script>
</body>

</html>


