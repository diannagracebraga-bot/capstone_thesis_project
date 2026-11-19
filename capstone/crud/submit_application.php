
<?php

require_once __DIR__ . '/../database/database_connection.php';
require_once __DIR__ . '/../database/email_availability.php';
date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["privacy_notice_acknowledged"])) {
        echo "<script>alert('Please acknowledge the Privacy Notice before submitting.'); window.history.back();</script>";
        exit;
    }

    $first_name_value = trim($_POST['first_name'] ?? '');
    $middle_name = mysqli_real_escape_string($conn, trim($_POST['middle_name'] ?? ''));
    $last_name_value = trim($_POST['last_name'] ?? '');
    $sex_value = strtolower(trim($_POST['sex'] ?? ''));

    if ($first_name_value === '' || $last_name_value === '') {
        echo "<script>alert('First name and last name are required.'); window.history.back();</script>";
        exit;
    }

    if (!in_array($sex_value, ['male', 'female'], true)) {
        echo "<script>alert('Please select Male or Female for sex.'); window.history.back();</script>";
        exit;
    }

    $first_name = mysqli_real_escape_string($conn, $first_name_value);
    $last_name = mysqli_real_escape_string($conn, $last_name_value);
    $sex = mysqli_real_escape_string($conn, $sex_value);
    $contact_number_value = trim($_POST['contact_number'] ?? '');
    if (!preg_match('/^09[0-9]{9}$/', $contact_number_value)) {
        echo "<script>alert('Enter a valid 11-digit contact number starting with 09.'); window.history.back();</script>";
        exit;
    }
    $contact_number = mysqli_real_escape_string($conn, $contact_number_value);
    $email = trim($_POST['email'] ?? '');
    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strtolower(substr($email, -10)) !== '@gmail.com'
    ) {
        echo "<script>
                alert('Please enter a valid Gmail address.');
                window.history.back();
              </script>";
        exit;
    }
    if (!checkdnsrr('gmail.com', 'MX')) {
        echo "<script>
                alert('The Gmail domain does not exist. Please try again later.');
                window.history.back();
              </script>";
        exit;
    }

    $emailAvailability = emailIsAlreadyUsed($conn, $email);
    if ($emailAvailability === true) {
        echo "<script>alert('This email is already in use. Please use a different email address.'); window.history.back();</script>";
        exit;
    }
    if ($emailAvailability === null) {
        echo "<script>alert('We could not verify this email right now. Please try again.'); window.history.back();</script>";
        exit;
    }

    $email = mysqli_real_escape_string($conn, strtolower($email));
    $facebook_account = mysqli_real_escape_string($conn, trim($_POST['facebook_account'] ?? ''));
    $barangay = mysqli_real_escape_string($conn, $_POST['barangay'] ?? '');
    $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $internet_plan = (int)($_POST['internet_plan'] ?? 0);
    $installationDateInput = trim($_POST['desired_installation_date'] ?? '');
    $installationDate = DateTime::createFromFormat('!Y-m-d', $installationDateInput);
    $today = new DateTime('today', new DateTimeZone('Asia/Manila'));
    if (!$installationDate || $installationDate->format('Y-m-d') !== $installationDateInput || $installationDate <= $today) {
        echo "<script>alert('Choose an installation date in the future.'); window.history.back();</script>";
        exit;
    }
    $desired_installation_date = mysqli_real_escape_string($conn, $installationDateInput);
    $phase_7_carissa = mysqli_real_escape_string($conn, $_POST['phase_7_carissa'] ?? '');
    $filled_up_by = mysqli_real_escape_string($conn, trim($_POST['filled_up_by'] ?? ''));
    $date_received = date("Y-m-d H:i:s");
    $status = "Pending";
    $filled_by_column = mysqli_query($conn, "SHOW COLUMNS FROM internet_application_tbl LIKE 'filled_up_by'");

    if ($filled_by_column && mysqli_num_rows($filled_by_column) === 0) {
        mysqli_query($conn, "ALTER TABLE internet_application_tbl ADD filled_up_by VARCHAR(150) NOT NULL DEFAULT ''");
    }

    $hoa_certificate = "";
    if (isset($_FILES['hoa_certificate'])) {
        if ($_FILES['hoa_certificate']['error'] != 4) {
            if ($_FILES['hoa_certificate']['error'] != 0) {
                echo "<script>alert('There was a problem uploading your HOA Certificate. Please try again.'); window.history.back();</script>";
                exit;
            }

            $file_name = $_FILES['hoa_certificate']['name'];
            $tmp_name = $_FILES['hoa_certificate']['tmp_name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
            $allowed_types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'pdf' => 'application/pdf'
            ];

            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($file_info, $tmp_name);
            finfo_close($file_info);

            if (
                !in_array($file_extension, $allowed_extensions) ||
                !isset($allowed_types[$file_extension]) ||
                $file_type != $allowed_types[$file_extension]
            ) {
                echo "<script>alert('Invalid file! Please upload JPG, JPEG, PNG, or PDF only.'); window.history.back();</script>";
                exit;
            }

            $upload_folder = __DIR__ . '/uploads/hoa_certificates/';

            if (!is_dir($upload_folder)) {
                mkdir($upload_folder, 0755, true);
            }

            $new_file_name = uniqid() . "." . $file_extension;

            if (move_uploaded_file($tmp_name, $upload_folder . $new_file_name)) {

                $hoa_certificate = $new_file_name;

            } else {
                echo "<script>alert('Failed to upload HOA Certificate. Please try again.'); window.history.back();</script>";
                exit;
            }
        }
    }

    if ($phase_7_carissa == "Yes" && empty($hoa_certificate)) {
        echo "<script>alert('HOA Certificate is required for customers from Phase 7 Carissa.'); window.history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO internet_application_tbl
    (first_name, middle_name, last_name, sex, contact_number, email, facebook_account, barangay, address,
    internet_plan, desired_installation_date, phase_7_carissa, hoa_certificate, date_received, status, filled_up_by)
    VALUES
    ('$first_name', '$middle_name', '$last_name', '$sex', '$contact_number', '$email', '$facebook_account', '$barangay', '$address',
    '$internet_plan', '$desired_installation_date', '$phase_7_carissa', '$hoa_certificate', '$date_received', '$status', '$filled_up_by')";

    if (mysqli_query($conn, $sql)) {

        $applicant_id = mysqli_insert_id($conn);

        echo "<script>
                alert('Your Internet Application has been submitted successfully!\\n\\nYour reference number is: " . $applicant_id . "');
                window.location.href = '../index.php';
              </script>";

    } else {
        echo "Failed to Submit Application: " . mysqli_error($conn);
    }

} else {
    header("Location: index.php");
    exit;
}

?>
