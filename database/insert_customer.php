<?php
include '../database/database_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $fname = $_POST['f_name'];
    $mname = $_POST['m_name'];
    $lname = $_POST['l_name'];
    $contact = $_POST['contact_number'];
    $birth = $_POST['birth_date'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $civil = $_POST['civil_status'];
    $barangay = $_POST['barangay'];
    $subdivision = $_POST['subdivision'];
    $street = $_POST['street'];
    $house = $_POST['house_number'];
    $plan = $_POST['internet_plan'];
    $status = $_POST['connection_status'];

    $check = mysqli_query($conn, "SELECT * FROM user_accounts_tbl WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){
        echo "<script>
                alert('Email already exists.');
                window.location='../admin/admin_user_management.php?page=add_customer';
              </script>";
        exit();
    }
    $role = "customer";

$sqlUser = "INSERT INTO user_accounts_tbl (email, password, role)
            VALUES ('$email', '$password', '$role')";

    if(mysqli_query($conn, $sqlUser)){
        $id = mysqli_insert_id($conn);

        $sqlCustomer = "INSERT INTO customer_tbl
        (user_id, f_name, m_name, l_name, contact_number, age, sex, civil_status, birth_date, barangay, subdivision, street,
            house_name, internet_plan,connection_status)
        VALUES
        ('$id','$fname','$mname','$lname','$contact','$age','$sex','$civil','$birth','$barangay','$subdivision',
            '$street','$house','$plan','$status')";

        if(mysqli_query($conn, $sqlCustomer)){
            echo "<script>
                    alert('Customer Registered Successfully');
                    window.location='../admin/admin_customer.php';
                  </script>";
        }else{
            echo mysqli_error($conn);
        }
    }else{
        echo mysqli_error($conn);
    }
}
?>