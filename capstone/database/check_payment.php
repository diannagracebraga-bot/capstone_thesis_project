<?php

session_start();
header("Content-Type: application/json");

include "database_connection.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "error" => "User not logged in"
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

/*
    We will receive the Payment Intent ID
    from the customer payment page.
*/

if (!isset($_GET['payment_intent_id'])) {
    echo json_encode([
        "error" => "Payment Intent ID is missing"
    ]);
    exit;
}

$payment_intent_id = $_GET['payment_intent_id'];


/* PayMongo secret key */

$secretKey = "sk_live_mG8MseME3iF3nGGno7Zjbj2C";


/* Get Payment Intent from PayMongo */

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paymongo.com/v1/payment_intents/" . $payment_intent_id,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "Authorization: Basic " . base64_encode($secretKey . ":")
    ]
]);

$response = curl_exec($curl);

if (curl_errno($curl)) {

    echo json_encode([
        "error" => curl_error($curl)
    ]);

    curl_close($curl);
    exit;
}

curl_close($curl);


$data = json_decode($response, true);


/* Check PayMongo response */

if (isset($data['errors'])) {

    echo json_encode([
        "error" => "PayMongo error",
        "details" => $data['errors']
    ]);

    exit;
}


/* Get payment status */

$status = $data['data']['attributes']['status'];


/*
    Only save payment when PayMongo says
    the payment was successful.
*/

if ($status === "succeeded") {

    /* Get customer information */

    $sql = "SELECT
                c.internet_plan,
                c.f_name,
                c.m_name,
                c.l_name,
                p.internet_price
            FROM customer_tbl c
            JOIN internet_plan_tbl p
                ON c.internet_plan = p.plan_id
            WHERE c.user_id = '$user_id'";

    $result = mysqli_query($conn, $sql);

    if (!$result) {

        echo json_encode([
            "error" => mysqli_error($conn)
        ]);

        exit;
    }

    $customer = mysqli_fetch_assoc($result);

    if (!$customer) {

        echo json_encode([
            "error" => "Customer not found"
        ]);

        exit;
    }


    $plan_id = $customer['internet_plan'];

    $f_name = $customer['f_name'];
    $m_name = $customer['m_name'];
    $l_name = $customer['l_name'];

    $amount = $customer['internet_price'];

    $payment_method = "QR Ph";
    $payment_status = "Paid";
    $remarks = "QR Ph payment";



    /*
        Check if this Payment Intent
        was already saved.
    */

    $check_sql = "SELECT id
                  FROM payment_tbl
                  WHERE paymongo_payment_id = '$payment_intent_id'
                  LIMIT 1";

    $check_result = mysqli_query($conn, $check_sql);


    if (mysqli_num_rows($check_result) == 0) {

        $insert_sql = "INSERT INTO payment_tbl
        ( plan_id,f_name, m_name, l_name, payment_method, amount, remarks, user_id,paymongo_payment_id, payment_status )
        VALUES
        ('$plan_id', '$f_name', '$m_name', '$l_name', '$payment_method', '$amount', '$remarks', '$user_id', '$payment_intent_id','$payment_status' )";

       $insert_result = mysqli_query($conn, $insert_sql);

if (!$insert_result) {

    echo json_encode([
        "error" => "Payment was successful but database save failed",
        "details" => mysqli_error($conn)
    ]);

    exit;
}


/* Update due date to next month */

$update_due_date = "UPDATE customer_tbl
                    SET due_date = DATE_ADD(due_date, INTERVAL 1 MONTH)
                    WHERE user_id = '$user_id'";

$update_result = mysqli_query($conn, $update_due_date);

if (!$update_result) {

    echo json_encode([
        "error" => "Payment was saved but due date update failed",
        "details" => mysqli_error($conn)
    ]);

    exit;
}

echo json_encode([
    "status" => "Paid",
    "message" => "Payment saved and due date updated successfully"
]);

exit;
    }
    echo json_encode([
        "status" => "Paid",
        "message" => "Payment already saved"
    ]);

    exit;
}
/* Payment is not successful yet */

echo json_encode([
    "status" => $status
]);

?>