
<?php

require_once __DIR__ . '/../database/database_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["privacy_notice_acknowledged"])) {
        echo "<script>alert('Please acknowledge the Privacy Notice before submitting.'); window.history.back();</script>";
        exit;
    }

    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name'] ?? ''));
    $middle_name = mysqli_real_escape_string($conn, trim($_POST['middle_name'] ?? ''));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name'] ?? ''));
    $sex = mysqli_real_escape_string($conn, $_POST['sex'] ?? '');
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number'] ?? '');
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

    $facebook_account = mysqli_real_escape_string($conn, trim($_POST['facebook_account'] ?? ''));
    $barangay = mysqli_real_escape_string($conn, $_POST['barangay'] ?? '');
    $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $internet_plan = (int)($_POST['internet_plan'] ?? 0);
    $desired_installation_date = mysqli_real_escape_string($conn, $_POST['desired_installation_date'] ?? '');
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
