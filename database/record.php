<?php
if(isset($_POST['add_payment'])){
    $fname = $_POST['f_name']
    $mname = $_POST['m_name']
    $lname = $_POST['l_name']
    $Payment_Method = $_POST['Payment_Method']
    $ddate = $_POST['d_date']
    $remarks = $_POST['remarks']
    $amount = $_POST['amount']

    if($fname == "" || empty($fname)){
        header('location:admin_add_payment.php?message=You need to fill in the first Name!');
    }
}
?>