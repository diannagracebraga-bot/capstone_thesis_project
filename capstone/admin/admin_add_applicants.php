<?php

include '../admin/check_admin_access.php';
include '../database/database_connection.php';

date_default_timezone_set('Asia/Manila');

$message = '';

$filled_up_by = '';

$admin_id = $_SESSION['account_id'] ?? 0;

$admin_query = mysqli_query($conn, "
    SELECT f_name, m_name, l_name
    FROM admin_superadmin_accounts_tbl
    WHERE account_id = '$admin_id'
");

if ($admin_query && mysqli_num_rows($admin_query) > 0) {

    $admin = mysqli_fetch_assoc($admin_query);

    $filled_up_by = trim(
        $admin['f_name'] . ' ' .
        $admin['m_name'] . ' ' .
        $admin['l_name']
    );

} else {

    $filled_up_by = $_SESSION['email'] ?? 'Admin';

}

// Ensure filled_up_by column exists
$filled_by_column = mysqli_query(
    $conn,
    "SHOW COLUMNS FROM internet_application_tbl LIKE 'filled_up_by'"
);

if ($filled_by_column && mysqli_num_rows($filled_by_column) === 0) {

    mysqli_query(
        $conn,
        "ALTER TABLE internet_application_tbl
         ADD filled_up_by VARCHAR(150) NOT NULL DEFAULT ''"
    );
}

// Get Internet Plans
$plan_query = mysqli_query(
    $conn,
    "SELECT * FROM internet_plan_tbl ORDER BY plan_id ASC"
);

// Submit Applicant
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["privacy_notice_acknowledged"])) {

        echo "<script>
                alert('Please acknowledge the Privacy Notice before submitting.');
                window.history.back();
              </script>";
        exit;
    }

    $first_name = mysqli_real_escape_string(
        $conn,
        trim($_POST['first_name'] ?? '')
    );

    $middle_name = mysqli_real_escape_string(
        $conn,
        trim($_POST['middle_name'] ?? '')
    );

    $last_name = mysqli_real_escape_string(
        $conn,
        trim($_POST['last_name'] ?? '')
    );

    $sex = mysqli_real_escape_string(
        $conn,
        trim($_POST['sex'] ?? '')
    );

    $contact_number = mysqli_real_escape_string(
        $conn,
        trim($_POST['contact_number'] ?? '')
    );

    $email = trim($_POST['email'] ?? '');

    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|ph|net|org|edu|gov)$/i', $email)
    ) {

        echo "<script>
                alert('Please enter a valid email address.');
                window.history.back();
              </script>";
        exit;
    }

    $email = mysqli_real_escape_string($conn, $email);

    $facebook_account = mysqli_real_escape_string(
        $conn,
        trim($_POST['facebook_account'] ?? '')
    );

    $barangay = mysqli_real_escape_string(
        $conn,
        trim($_POST['barangay'] ?? '')
    );

    $address = mysqli_real_escape_string(
        $conn,
        trim($_POST['address'] ?? '')
    );

    $internet_plan = intval($_POST['internet_plan'] ?? 0);

    $desired_installation_date = mysqli_real_escape_string(
        $conn,
        trim($_POST['desired_installation_date'] ?? '')
    );

    $phase_7_carissa = mysqli_real_escape_string(
        $conn,
        trim($_POST['phase_7_carissa'] ?? '')
    );

    $date_received = date("Y-m-d H:i:s");

    $status = "Pending";

    $filled_up_by = mysqli_real_escape_string(
        $conn,
        $filled_up_by
    );

    $hoa_certificate = "";

    // Validate required fields
    if (
        empty($first_name) ||
        empty($last_name) ||
        empty($sex) ||
        empty($contact_number) ||
        empty($email) ||
        empty($barangay) ||
        empty($address) ||
        $internet_plan <= 0 ||
        empty($desired_installation_date) ||
        !in_array($phase_7_carissa, ['Yes', 'No'])
    ) {

        echo "<script>
                alert('Please complete all required fields.');
                window.history.back();
              </script>";
        exit;
    }

    // Validate selected Internet Plan
    $check_plan = mysqli_query(
        $conn,
        "SELECT plan_id FROM internet_plan_tbl
         WHERE plan_id = '$internet_plan'"
    );

    if (!$check_plan || mysqli_num_rows($check_plan) == 0) {

        echo "<script>
                alert('Please select a valid Internet Plan.');
                window.history.back();
              </script>";
        exit;
    }

    // HOA Certificate Upload
    if (isset($_FILES['hoa_certificate'])) {

        if ($_FILES['hoa_certificate']['error'] != 4) {

            if ($_FILES['hoa_certificate']['error'] != 0) {

                echo "<script>
                        alert('There was a problem uploading your HOA Certificate. Please try again.');
                        window.history.back();
                      </script>";
                exit;
            }

            $file_name = $_FILES['hoa_certificate']['name'];

            $tmp_name = $_FILES['hoa_certificate']['tmp_name'];

            $file_extension = strtolower(
                pathinfo($file_name, PATHINFO_EXTENSION)
            );

            $allowed_extensions = ['jpg', 'jpeg', 'png'];

            $allowed_types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png'
            ];

            $file_info = finfo_open(FILEINFO_MIME_TYPE);

            $file_type = finfo_file($file_info, $tmp_name);

            finfo_close($file_info);

            if (
                !in_array($file_extension, $allowed_extensions) ||
                !isset($allowed_types[$file_extension]) ||
                $file_type != $allowed_types[$file_extension]
            ) {

                echo "<script>
                        alert('Invalid file! Please upload JPG, JPEG, or PNG only.');
                        window.history.back();
                      </script>";
                exit;
            }

            $upload_folder = "../uploads/hoa_certificates/";

            if (!is_dir($upload_folder)) {

                mkdir($upload_folder, 0777, true);
            }

            $new_file_name = uniqid() . "." . $file_extension;

            if (
                move_uploaded_file(
                    $tmp_name,
                    $upload_folder . $new_file_name
                )
            ) {

                $hoa_certificate = $new_file_name;

            } else {

                echo "<script>
                        alert('Failed to upload HOA Certificate. Please try again.');
                        window.history.back();
                      </script>";
                exit;
            }
        }
    }

    // Require HOA Certificate for Phase 7 Carissa
    if ($phase_7_carissa == "Yes" && empty($hoa_certificate)) {

        echo "<script>
                alert('HOA Certificate is required for customers from Phase 7 Carissa.');
                window.history.back();
              </script>";
        exit;
    }

    // Insert Applicant
    $sql = "INSERT INTO internet_application_tbl

    ( first_name,middle_name, last_name, sex, contact_number, email, facebook_account, barangay, address, internet_plan, desired_installation_date,
        phase_7_carissa,hoa_certificate, date_received, status, filled_up_by)
    VALUES
    ('$first_name', '$middle_name', '$last_name', '$sex', '$contact_number', '$email', '$facebook_account', '$barangay', '$address', '$internet_plan',
        '$desired_installation_date', '$phase_7_carissa', '$hoa_certificate', '$date_received', '$status', '$filled_up_by')";

    if (mysqli_query($conn, $sql)) {

        $applicant_id = mysqli_insert_id($conn);

        echo "<script>
                alert('Applicant successfully added!\\n\\nApplicant ID: " . $applicant_id . "');
                window.location='admin_applicants.php';
              </script>";

        exit;

    } else {

        echo "<script>
                alert('Failed to add applicant: " .
                addslashes(mysqli_error($conn)) .
                "');
                window.history.back();
              </script>";

        exit;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css?v=announcement-theme-3">

<link rel="stylesheet" href="../css/admin_add_applicants.css?v=2">

    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>

<body class="admin-module-page">
<?php include '../admin/admin_sidebar_header_profile.php'; ?>
<div class="admin-add-applicant-page">
    <div class="card add-applicant-card">
        <div class="card-header">
            <h4 class="mb-1">Add New Applicant</h4>

        </div>

        <div class="card-body">

            <form method="POST" action="" enctype="multipart/form-data">

                <!-- Personal Information -->

                <h6 class="applicant-section-title">
                    Personal Information
                </h6>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            First Name <span class="required">*</span>
                        </label>

                        <input type="text" class="form-control" name="first_name" placeholder="Enter first name" required >
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Middle Name
                        </label>

                        <input type="text" class="form-control" name="middle_name" placeholder="Enter middle name" >

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Last Name <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="last_name" placeholder="Enter last name" required >
                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Sex <span class="required">*</span>
                        </label>

                        <select class="form-select" name="sex" required>

                            <option value="" selected disabled>
                                Select Sex
                            </option>

                            <option value="Male">Male</option>
                            <option value="Female">Female</option>

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Contact Number <span class="required">*</span>
                        </label>

                        <input type="tel" class="form-control" name="contact_number" placeholder="Enter contact number" pattern="[0-9]{11}" maxlength="11" minlength="11" required>

                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Email Address <span class="required">*</span>
                        </label>
                        <input type="email" class="form-control"  name="email" placeholder="example@gmail.com" required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Facebook Account
                        </label>

                        <input type="text" class="form-control" name="facebook_account" placeholder="Enter Facebook account">
                    </div>
                </div>

                <!-- Address Information -->

                <h6 class="applicant-section-title">
                    Address Information
                </h6>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Barangay <span class="required">*</span>
                        </label>

                        <select class="form-select" name="barangay" required >

                            <option value="" selected disabled>
                                Select Barangay
                            </option>

                            <option value="Bagtas">Bagtas</option>
                            <option value="Punta I">Punta I</option>

                        </select>

                    </div>

                    <div class="col-md-8 mb-3">

                        <label class="form-label">
                            House Address <span class="required">*</span>
                        </label>

                        <input type="text" class="form-control" name="address" placeholder="Enter house number, street, and other address details" required >

                    </div>

                </div>

                <!-- Internet Service -->
                <h6 class="applicant-section-title">
                    Internet Service
                </h6>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Internet Plan <span class="required">*</span>
                        </label>

                        <select class="form-select" name="internet_plan" required>
                            <option value="" selected disabled>
                               Select Internet Plan
                            </option>

                            <?php

                            if ($plan_query && mysqli_num_rows($plan_query) > 0) {

                                while ($plan = mysqli_fetch_assoc($plan_query)) {

                            ?>
                                <option value="<?php echo $plan['plan_id']; ?>">

                                    <?php

                                    echo htmlspecialchars(
                                        $plan['plan_name']
                                    ) .
                                    " - " .
                                    $plan['internet_mbps'] .
                                    " Mbps - ₱" .
                                    number_format(
                                        $plan['internet_price'],
                                        2
                                    );

                                    ?>

                                </option>
                            <?php
                                }

                            }

                            ?>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Desired Installation Date <span class="required">*</span>
                        </label>

                        <input type="date" class="form-control" name="desired_installation_date" min="<?php echo date('Y-m-d'); ?>" required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Are you from Phase 7 Carissa?
                            <span class="required">*</span>
                        </label>

                        <select  class="form-select"  name="phase_7_carissa"  id="phase_7_carissa"  required >
                            <option value="" selected disabled>
                                Select Answer
                            </option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>

                        </select>
                    </div>
                </div>

                <!-- HOA Requirement -->

                <h6 class="applicant-section-title">
                    HOA Requirement
                </h6>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            HOA Certificate
                            <span class="required" id="hoaRequired">*</span>
                        </label>

                        <input type="file" class="form-control" name="hoa_certificate" id="hoa_certificate" accept=".jpg,.jpeg,.png,image/jpeg,image/png" >
                        <small class="text-muted">
                            Upload JPG, JPEG, or PNG only.
                            Required for Phase 7 Carissa.
                        </small>
                    </div>
                </div>

                <!-- Filled Up By -->
                <h6 class="applicant-section-title">
                    Application Information
                </h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Filled Up By
                        </label>

                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($filled_up_by, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>
                </div>

                <!-- Privacy Notice -->

                <div class="border rounded p-3 mt-3">

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="privacy_notice_acknowledged" id="privacy_notice_acknowledged" value="1"
                            required>

                        <label class="form-check-label" for="privacy_notice_acknowledged">

                            I acknowledge that the applicant has been informed
                            about the Privacy Notice and the collection and
                            processing of their personal information.

                            <span class="required">*</span>

                        </label>
                    </div>
                </div>

                <!-- Buttons -->

                <div class="form-actions">

                    <a href="admin_applicants.php" class="btn btn-danger"> Cancel </a>

                    <button type="reset" class="btn btn-secondary" >
                        Clear
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Add Applicant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

document.addEventListener("DOMContentLoaded", function () {

    const phaseSelect = document.getElementById("phase_7_carissa");

    const hoaInput = document.getElementById("hoa_certificate");

    const hoaRequired = document.getElementById("hoaRequired");

    function checkHOARequirement() {

        if (phaseSelect.value === "Yes") {

            hoaInput.required = true;

            hoaRequired.style.display = "inline";

        } else {

            hoaInput.required = false;

            hoaRequired.style.display = "none";

        }

    }

    phaseSelect.addEventListener("change", checkHOARequirement);

    checkHOARequirement();

});

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.addEventListener('pageshow', function(event) {

    if (event.persisted) {

        window.location.reload();

    }

});

</script>
</body>
</html>
