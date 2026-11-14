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
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet" href="../css/customer_sidebar_header.css?v=customer-shell-14">
    <link rel="stylesheet" href="../css/customer_payment.css?v=qr-modal-fit-2">
</head>
<body class="customer-payment-page">
    <div class="content">
        <div class="payment-container">
            <div class="qr-section" id="payment-status" aria-live="polite">
                <p id="payment-message" class="mb-2">Inihahanda ang QR Ph payment...</p>
                <div class="qr-box">
                    <img id="qr-image" alt="QR PH Payment">
                </div>
 </div>
        </div>
    </div>
    <script>
async function loadQRCode() {
    const message = document.getElementById('payment-message');
    const qrBox = document.querySelector('.qr-box');
    try {
        const intentResponse = await fetch("../database/payment_intent.php");
        const paymentIntent = await intentResponse.json();
        if (!intentResponse.ok || !paymentIntent.data?.id) {
            throw new Error(paymentIntent.error || 'Hindi makagawa ng payment QR.');
        }

        const paymentIntentId = paymentIntent.data.id;
        const clientKey = paymentIntent.data.attributes.client_key;
        const headers = {
            "Content-Type": "application/json",
            "Authorization": "Basic " + btoa("pk_live_WgQj8QFMeQmzhxYHDvZJ2uHZ:")
        };
        const methodResponse = await fetch("https://api.paymongo.com/v1/payment_methods", {
            method: "POST",
            headers,
            body: JSON.stringify({data: {attributes: {type: "qrph"}}})
        });
        const paymentMethod = await methodResponse.json();
        if (!methodResponse.ok || !paymentMethod.data?.id) {
            throw new Error('Hindi makagawa ng QR Ph payment method.');
        }

        const attachResponse = await fetch(
            `https://api.paymongo.com/v1/payment_intents/${paymentIntentId}/attach`,
            {
                method: "POST",
                headers,
                body: JSON.stringify({data: {attributes: {payment_method: paymentMethod.data.id, client_key: clientKey}}})
            }
        );
        const intent = await attachResponse.json();
        const imageUrl = intent?.data?.attributes?.next_action?.code?.image_url;
        if (!attachResponse.ok || !imageUrl) {
            throw new Error('Hindi maipakita ang payment QR. Pakisubukan ulit.');
        }

        document.getElementById("qr-image").src = imageUrl;
        message.textContent = 'I-scan ang QR Ph code para magbayad.';

        let checking = false;
        const paymentChecker = setInterval(async function() {
            if (checking) return;
            checking = true;
            try {
                const checkResponse = await fetch(
                    "../database/check_payment.php?payment_intent_id=" + encodeURIComponent(paymentIntentId),
                    {cache: 'no-store'}
                );
                const result = await checkResponse.json();
                if (checkResponse.ok && result.status === "Paid") {
                    clearInterval(paymentChecker);
                    qrBox.hidden = true;
                    message.textContent = 'Matagumpay ang bayad. Ina-update ang iyong SOA...';
                    window.parent.postMessage({type: 'customer-payment-success'}, window.location.origin);
                }
            } catch (error) {
                console.error('Payment status check failed:', error);
            } finally {
                checking = false;
            }
        }, 5000);
    } catch (error) {
        message.textContent = error.message || 'May problema sa pag-load ng payment QR. Pakisubukan ulit.';
        if (qrBox) qrBox.hidden = true;
        console.error('QR payment setup failed:', error);
    }
}
loadQRCode();
</script>
</body>
</html>
