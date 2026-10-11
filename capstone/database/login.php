
<?php
session_start();
include 'database_connection.php';

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check customer accounts first
    $sql = "SELECT * FROM user_accounts_tbl WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    // If not a customer, check Admin and Super Admin accounts
    if ($result && mysqli_num_rows($result) == 0) {

        $sql = "SELECT * FROM admin_superadmin_accounts_tbl
                WHERE email='$email'";

        $result = mysqli_query($conn, $sql);
    }

    if ($result && mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        // Check password
        if (password_verify($password, $user['password'])) {

            // Check Admin and Super Admin account status
            if (
                ($user['role'] == "Admin" || $user['role'] == "Super Admin")
                && $user['account_status'] != "Active"
            ) {

                echo "<script>
                    alert('Your account has been deactivated. Please contact the Super Admin.');
                    window.location='../index.php';
                </script>";

                exit();
            }

            // Set session according to account type
            if ($user['role'] == "customer") {

                $_SESSION['user_id'] = $user['user_id'];

            } else {

                $_SESSION['account_id'] = $user['account_id'];

            }

            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect according to role
            if ($user['role'] == "customer") {

                echo "<script>
                    alert('Welcome, Customer!');
                    window.location='../customer/customer-dashboard.php';
                </script>";

                exit();

            } elseif ($user['role'] == "Admin") {

                echo "<script>
                    alert('Welcome, Admin!');
                    window.location='../admin/admin_dashboard.php';
                </script>";

                exit();

            } elseif ($user['role'] == "Super Admin") {

                echo "<script>
                    alert('Welcome, Super Admin!');
                    window.location='../admin/admin_dashboard.php';
                </script>";

                exit();

            } else {

                echo "<script>
                    alert('Invalid user role.');
                    window.location='../index.php';
                </script>";

                exit();
            }

        } else {

            echo "<script>
                alert('Incorrect password.');
                window.location='../index.php';
            </script>";

            exit();
        }

    } else {

        echo "<script>
            alert('Email not found.');
            window.location='../index.php';
        </script>";

        exit();
    }
}
?>
