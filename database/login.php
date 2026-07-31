<?php
session_start();
include 'database_connection.php';

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user_accounts_tbl WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1){

        $user = mysqli_fetch_assoc($result);

        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

           if($user['role'] == "customer"){
              echo "<script>
            alert('Welcome, Customer!');
            window.location='../customer/customer-dashboard.php';
          </script>";
    exit();

            }elseif($user['role'] == "admin"){
                 echo "<script>
            alert('Welcome, Admin!');
            window.location='../admin/admin_dashboard.php';
          </script>";
            }elseif($user['role'] == "super_admin"){
                header("Location: ../admin/admin_dashboard.php");
            }else{
                echo "<script> alert('Invalid user role.'); window.location='../index.php';
                      </script>";
            }
            exit();
        }else{
            echo "<script> alert('Incorrect password.');  window.location='../index.php';
                  </script>";
        }
    }else{
        echo "<script>alert('Email not found.'); window.location='../index.php';
              </script>";
    }
}
?>