<?php
include 'database_connection.php';

if(isset($_POST['update'])){

    $customer_id = $_POST['customer_id'];

    $email = $_POST['email'];
    $password = $_POST['password'];

    $f_name = $_POST['f_name'];
    $m_name = $_POST['m_name'];
    $l_name = $_POST['l_name'];

    $contact_number = $_POST['contact_number'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $civil_status = $_POST['civil_status'];

    $birth_date = $_POST['birth_date'];

    $barangay = $_POST['barangay'];
    $subdivision = $_POST['subdivision'];
    $street = $_POST['street'];
    $house_name = $_POST['house_name'];

    $internet_plan = $_POST['internet_plan'];
    $connection_status = $_POST['connection_status'];

    // Get the user_id of this customer
    $getUser = mysqli_query($conn, "SELECT user_id
                                    FROM customer_tbl
                                    WHERE customer_id='$customer_id'");

    $user = mysqli_fetch_assoc($getUser);

    $user_id = $user['user_id'];

    // Update customer table
    $updateCustomer = "UPDATE customer_tbl SET

        f_name='$f_name',
        m_name='$m_name',
        l_name='$l_name',
        contact_number='$contact_number',
        age='$age',
        sex='$sex',
        civil_status='$civil_status',
        birth_date='$birth_date',
        barangay='$barangay',
        subdivision='$subdivision',
        street='$street',
        house_name='$house_name',
        internet_plan = '$internet_plan',
        connection_status='$connection_status'

        WHERE customer_id='$customer_id'";

    mysqli_query($conn, $updateCustomer);

    if(!empty($password)){

        $password = password_hash($password, PASSWORD_DEFAULT);

        mysqli_query($conn, "UPDATE user_accounts_tbl
                             SET
                                email='$email',
                                password='$password'
                             WHERE id='$user_id'");

    }else{

        mysqli_query($conn, "UPDATE user_accounts_tbl
                             SET email='$email'
                             WHERE id='$user_id'");
    }

    header("Location: ../admin/admin_customer.php?updated=1");
    exit();
}
?>