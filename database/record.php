<?php
if(isset($_POST['add_payment'])){
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $payment_method = $_POST['payment_method'];
    $date = $_POST['date'];
    $remarks = $_POST['remarks'];
    $amount = $_POST['amount'];
    if($first_name == "" || empty($first_name)){
        header('location:admin_add_payment.php?message=You need to fill in the first Name!');
    }
}
?>