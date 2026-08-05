<?php

$amount_cents = 1000; // ₱10 test

// 1. Create cURL session FIRST
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v2/batch_transfers");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "sk_live_mG8MseME3iF3nGGno7Zjbj2C:");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

// 2. Prepare data
$data = [
    "transfers" => [
        [
            "source_account" => [
                "number" => "705320540966",
                "name" => "DIANNA GRACE BRAGA",
                "bic" => "PAEYPHM2XXX"
            ],
            "destination_account" => [
                "number" => "09266468174",
                "name" => "DIANNA GRACE BRAGA",
                "bic" => "GXCHPHM2XXX"
            ],
            "amount" => $amount_cents,
            "currency" => "PHP",
            "provider" => "instapay",
            "description" => "Transfer to GCash"
        ]
    ]
];

// 3. Send JSON
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// 4. Get response
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo curl_error($ch);
} else {
    header("Content-Type: application/json");
    echo $response;
}

curl_close($ch);

?>