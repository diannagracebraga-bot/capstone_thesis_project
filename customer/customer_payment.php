<?php
session_start();
include '../database/database_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM customer_tbl WHERE user_id='$user_id'";
$result = mysqli_query($conn, $sql);
$customer = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/customer_sidebar_header.css?v=4">
    <link rel="stylesheet" href="../css/customer_payment.css?v=3">
</head>

<body>
    <?php include 'customer_sidebar_header.php'; ?>

    <div class="content">
        <div class="payment-container">

        
            <div class="qr-section">

                <h2>Scan QR Payment</h2>

                <div class="qr-box">
    <img id="qr-image" alt="QR PH Payment">
</div>
                <p>Scan using GCash / Maya / Banking App</p>

            </div>

            

       
      
        </div>
    </div>


    <script>
async function loadQRCode() {

    // Get Payment Intent from PHP backend
    const intentResponse = await fetch("../database/payment_intent.php");
    const paymentIntent = await intentResponse.json();

    const paymentIntentId = paymentIntent.data.id;
    const clientKey = paymentIntent.data.attributes.client_key;

    // 1) Create QR PH Payment Method
    const response = await fetch("https://api.paymongo.com/v1/payment_methods", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Basic " + btoa("pk_live_WgQj8QFMeQmzhxYHDvZJ2uHZ:")
        },
        body: JSON.stringify({
            data: {
                attributes: {
                    type: "qrph"
                }
            }
        })
    });

    const paymentMethod = await response.json();

    // 2) Attach Payment Method to Payment Intent
    const attachResponse = await fetch(
        `https://api.paymongo.com/v1/payment_intents/${paymentIntentId}/attach`,
        {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Basic " + btoa("pk_live_WgQj8QFMeQmzhxYHDvZJ2uHZ:")
            },
            body: JSON.stringify({
                data: {
                    attributes: {
                        payment_method: paymentMethod.data.id,
                        client_key: clientKey
                    }
                }
            })
        }
    );

    const intent = await attachResponse.json();

    console.log(intent);

    // 3) Show QR Code
    const imageUrl = intent.data.attributes.next_action.code.image_url;

    document.getElementById("qr-image").src = imageUrl;
}

loadQRCode();
</script>
</body>
</html>
