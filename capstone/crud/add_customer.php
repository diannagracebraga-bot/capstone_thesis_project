<?php
require_once __DIR__ . '/../admin/require_admin_access.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {

$email = trim($_POST['email'] ?? '');


// Validate Gmail address
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

// Validate if Gmail domain exists
if (!checkdnsrr('gmail.com', 'MX')) {
    echo "<script>
            alert('The Gmail domain does not exist. Please try again later.');
            window.history.back();
          </script>";
    exit;
}

$email = mysqli_real_escape_string($conn, $email);

$password = $_POST['password'] ?? '';

if (
    strlen($password) < 8 ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[^A-Za-z0-9]/', $password)
) {
    echo "<script>
            alert('Password must contain at least 8 characters, including an uppercase letter, a lowercase letter, a number, and a special character.');
            window.history.back();
          </script>";
    exit;
}

$password = password_hash($password, PASSWORD_DEFAULT);

    $fname = $_POST['f_name'];
    $mname = $_POST['m_name'];
    $lname = $_POST['l_name'];
    $contact = $_POST['contact_number'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $barangay = $_POST['barangay'];
    $house_name = $_POST['house_name'];
    $plan = $_POST['internet_plan'];
    $status = $_POST['connection_status'];
    $due_date = $_POST['due_date'];

    /* Check if email already exists */
    $check = mysqli_query(
        $conn,
        "SELECT * FROM user_accounts_tbl WHERE email='$email'"
    );

    if (mysqli_num_rows($check) > 0) {

        echo "<script>
                alert('Email already exists.');
                window.location='../admin/admin_user_management.php?page=add_customer';
              </script>";

        exit();
    }

    /* Customer role */
    $role = "customer";

    /* Insert account */
    $sqlUser = "INSERT INTO user_accounts_tbl
                (email, password, role)
                VALUES
                ('$email', '$password', '$role')";

    if (mysqli_query($conn, $sqlUser)) {

        $id = mysqli_insert_id($conn);

        /* Get next customer ID */
        $result = mysqli_query(
            $conn,
            "SELECT MAX(customer_id) AS last_id FROM customer_tbl"
        );

        $row = mysqli_fetch_assoc($result);

        $next_id = ($row['last_id'] ?? 0) + 1;

        /* Generate Account Number */
        $account_number = "MPC-" .
                          str_pad($next_id, 5, "0", STR_PAD_LEFT);


        /* Insert Customer */
        $sqlCustomer = "INSERT INTO customer_tbl
            (user_id, f_name, m_name, l_name, contact_number, age, sex, barangay,house_name, internet_plan, connection_status, account_number, due_date)
            VALUES('$id','$fname','$mname','$lname','$contact','$age','$sex','$barangay','$house_name','$plan','$status','$account_number','$due_date' )";

        if (mysqli_query($conn, $sqlCustomer)) {

            echo "<script>
                    alert('Customer Registered Successfully');
                    window.location='../admin/admin_customer.php';
                  </script>";

        } else {

            echo mysqli_error($conn);

        }
    } else {

        echo mysqli_error($conn);

    }
}
?>
