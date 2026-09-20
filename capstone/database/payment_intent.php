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

/* Get customer and internet plan */
$sql = "SELECT 
            c.internet_plan,
            c.f_name,
            c.m_name,
            c.l_name,
            p.plan_name,
            p.internet_mbps,
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
        "error" => "Customer or internet plan not found"
    ]);
    exit;
}


/* Get plan information */
$plan_id = $customer['internet_plan'];

$f_name = $customer['f_name'];
$m_name = $customer['m_name'];
$l_name = $customer['l_name'];

$plan_name = $customer['plan_name'];
$internet_mbps = $customer['internet_mbps'];
$price = $customer['internet_price'];

/* Convert peso to centavos */
$amount = $price * 100;


/* PayMongo secret key */
$secretKey = "sk_live_mG8MseME3iF3nGGno7Zjbj2C";


/* Create Payment Intent */
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paymongo.com/v1/payment_intents",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "content-type: application/json",
        "Authorization: Basic " . base64_encode($secretKey . ":")
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "data" => [
            "attributes" => [
                "amount" => $amount,
                "currency" => "PHP",
                "payment_method_allowed" => ["qrph"],
                "description" => $plan_name . " - " . $internet_mbps . " Mbps"
            ]
        ]
    ])
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


/* Convert PayMongo response to PHP array */
$paymentIntent = json_decode($response, true);


/* Check if PayMongo returned an error */
if (isset($paymentIntent['errors'])) {

    echo json_encode([
        "error" => "PayMongo error",
        "details" => $paymentIntent['errors']
    ]);

    exit;
}


/* Get Payment Intent ID */
$paymongo_payment_id = $paymentIntent['data']['id'];


/* Return PayMongo response to JavaScript */
echo $response;

?>