<?php

header("Content-Type: application/json");

$secretKey = "sk_live_mG8MseME3iF3nGGno7Zjbj2C"; // Replace with your actual secret key

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
                "amount" => 500, // ₱100.00 (centavos)
                "currency" => "PHP",
                "payment_method_allowed" => ["qrph"],
                "description" => "Order #1234"
            ]
        ]
    ])
]);

$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo json_encode([
        "error" => curl_error($curl)
    ]);
    exit;
}

curl_close($curl);

// Return the JSON response to your JavaScript
echo $response;
?>