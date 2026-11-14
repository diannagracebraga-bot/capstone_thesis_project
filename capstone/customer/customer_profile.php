<?php
include '../admin/check_customer_access.php';

include '../database/database_connection.php';


$user_id = (int) ($_SESSION['user_id'] ?? 0);
$message = "";
$message_type = 'success';
$edit_mode = ($_SERVER['REQUEST_METHOD'] === 'POST') || (isset($_GET['edit']) && $_GET['edit'] === '1');

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
    $contact_number = trim($_POST['contact_number'] ?? '');
    $newPhotoName = null;
    $uploadError = '';
    $photoUpload = $_FILES['profile_photo'] ?? null;
    $newPassword = (string) ($_POST['password'] ?? '');
    $passwordHash = null;

    if (!array_key_exists('contact_number', $_POST)) {
        $uploadError = 'The submitted profile data is incomplete. Please try again.';
    } elseif (!preg_match('/^09[0-9]{9}$/', $contact_number)) {
        $uploadError = 'Contact number must contain 11 digits and start with 09.';
    } elseif ($newPassword !== '' && (
        strlen($newPassword) < 8
        || !preg_match('/[a-z]/', $newPassword)
        || !preg_match('/[A-Z]/', $newPassword)
        || !preg_match('/[0-9]/', $newPassword)
        || !preg_match('/[^A-Za-z0-9]/', $newPassword)
    )) {
        $uploadError = 'Password must be at least 8 characters and include uppercase, lowercase, number, and special character.';
    } elseif ($photoUpload && ($photoUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE && !$profilePhotoAvailable) {
        $uploadError = 'Import the customer profile SQL migration before uploading a photo.';
    } elseif ($newPassword !== '') {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
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
            $customerUpdate = mysqli_prepare($conn, "UPDATE customer_tbl SET contact_number = ? WHERE user_id = ?");
            $customerSaved = false;
            $accountSaved = $newPhotoName === null && $passwordHash === null;

            if ($customerUpdate) {
                mysqli_stmt_bind_param($customerUpdate, 'si', $contact_number, $user_id);
                $customerSaved = mysqli_stmt_execute($customerUpdate);
            }

            if ($customerSaved && ($newPhotoName !== null || $passwordHash !== null)) {
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
    $message = '';
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
          href="../css/customer_sidebar_header.css?v=customer-shell-11">

    <link rel="stylesheet"
          href="../css/customer_profile.css?v=12">

</head>

<body>

<?php if (isset($_GET['updated'])): ?>
    <script>alert('Profile changes successfully');</script>
<?php endif; ?>

<?php include 'customer_sidebar_header.php'; ?>

<main class="profile-content">

    <div class="card">

        <div class="card-body">

            <section class="profile-card">

                <div class="profile-title-row">

                    <h2>Account Information</h2>

                </div>


                <?php if ($message != ""): ?>

                    <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>">

                        <?php echo htmlspecialchars($message); ?>

                    </div>

                <?php endif; ?>


                <form method="POST" id="profileForm" class="profile-layout" enctype="multipart/form-data">

                    <aside class="profile-identity-panel">
                        <div class="profile-avatar-wrap">
                            <?php if ($hasProfilePhoto): ?>
                                <img src="<?php echo htmlspecialchars($profilePhotoUrl); ?>" alt="Customer profile photo" id="profilePhotoPreview">
                            <?php else: ?>
                                <div class="profile-avatar-placeholder" id="profilePhotoPlaceholder" aria-label="No profile photo selected"><i class="bi bi-person-fill" aria-hidden="true"></i></div>
                            <?php endif; ?>
                            <?php if ($edit_mode && $profilePhotoAvailable): ?>
                                <label class="profile-photo-picker" for="profile_photo" aria-label="Choose a profile photo"><i class="bi bi-camera-fill" aria-hidden="true"></i></label>
                                <input type="file" name="profile_photo" id="profile_photo" class="profile-photo-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            <?php endif; ?>
                        </div>
                        <div class="profile-identity-name"><?php echo htmlspecialchars(trim($displayFirstName . ' ' . $displayLastName)); ?></div>
                        <span class="profile-role-badge"><?php echo htmlspecialchars(ucfirst($role)); ?></span>
                        <?php if ($edit_mode && $profilePhotoAvailable): ?>
                            <small class="profile-photo-hint">JPG, PNG, or WEBP · up to 5 MB</small>
                        <?php elseif ($edit_mode): ?>
                            <small class="profile-photo-hint">Import the profile SQL file to enable photo uploads.</small>
                        <?php endif; ?>
                    </aside>

                    <div class="profile-details">

                    <div class="row g-3 profile-fields-grid">


                        <!-- Account Number -->

                        <div class="col-md-2">

                            <label class="form-label">
                                Account Number:
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['account_number']; ?>"
                                   readonly>

                        </div>


                        <!-- Email -->

                        <div class="col-md-7">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input type="email"
                                   class="form-control"
                                   value="<?php echo $customer['email']; ?>"
                                   readonly>

                        </div>


                        <!-- Password -->

                        <div class="col-md-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input type="password"
                                   class="form-control"
                                   name="password"
                                   <?php if ($edit_mode): ?>
                                       placeholder="Leave blank to keep your password"
                                       minlength="8"
                                       pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}"
                                       title="Use at least 8 characters with uppercase, lowercase, number, and special character."
                                       autocomplete="new-password"
                                   <?php else: ?>
                                       value="password"
                                       readonly
                                   <?php endif; ?>>
                            <?php if ($edit_mode): ?>
                                <small class="profile-field-hint">Leave blank if you do not want to change your password.</small>
                            <?php endif; ?>

                        </div>


                        <!-- First Name -->

                        <div class="col-md-4">

                            <label class="form-label">
                                First Name
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($displayFirstName); ?>"
                                   readonly>

                        </div>


                        <!-- Middle Name -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Middle Name
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($displayMiddleName); ?>"
                                   readonly>

                        </div>


                        <!-- Last Name -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Last Name
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($displayLastName); ?>"
                                   readonly>

                        </div>


                        <!-- Contact Number -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Contact Number
                            </label>

                            <input type="text"
                                   class="form-control"
                                   name="contact_number"
                                   value="<?php echo htmlspecialchars((string) $customer['contact_number']); ?>"
                                   <?php if ($edit_mode): ?>
                                       maxlength="11" pattern="09[0-9]{9}" title="Enter 11 digits starting with 09." required autocomplete="tel"
                                   <?php else: ?>
                                       readonly
                                   <?php endif; ?>>

                        </div>


                        <!-- Age -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Age
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['age']; ?>"
                                   readonly>

                        </div>


                        <!-- Sex -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Sex
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['sex']; ?>"
                                   readonly>

                        </div>


                        <!-- Barangay -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Barangay
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars((string) $customer['barangay']); ?>"
                                   readonly>

                        </div>

                                  <div class="col-md-4">

                            <label class="form-label">
                                House Address
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo ucfirst($customer['house_name']); ?>"
                                   readonly>

                        </div>
                        <!-- Role -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Role
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo ucfirst($customer['role']); ?>"
                                   readonly>

                        </div>


                        <!-- Internet Plan -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Internet Plan
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $internet_plan; ?>"
                                   readonly>

                        </div>


                        <!-- Connection Status -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Connection Status
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['connection_status']; ?>"
                                   readonly>

                        </div>


                        <!-- Due Date -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Due Date
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['due_date']; ?>"
                                   readonly>

                        </div>


                    </div>

                    </div>

                </form>

                <div class="profile-button-row">
                    <?php if ($edit_mode) { ?>
                        <a href="customer_profile.php" class="btn btn-secondary profile-cancel-btn">Cancel</a>
                        <button type="submit" form="profileForm" class="btn profile-save-btn">Save Changes</button>
                    <?php } else { ?>
                        <a href="customer_profile.php?edit=1" class="btn profile-edit-btn">Edit</a>
                    <?php } ?>
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
