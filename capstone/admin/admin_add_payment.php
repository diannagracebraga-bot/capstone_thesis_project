<?php
include '../database/database_connection.php';

$customer_query = "SELECT 
                        c.user_id,
                        c.f_name,
                        c.m_name,
                        c.l_name,
                        c.account_number,
                        c.internet_plan,
                        p.plan_name,
                        p.internet_mbps,
                        p.internet_price
                   FROM customer_tbl c
                   INNER JOIN internet_plan_tbl p
                       ON c.internet_plan = p.plan_id
                   ORDER BY c.f_name ASC";

$customer_result = mysqli_query($conn, $customer_query);

if (!$customer_result) {
    die("Customer query failed: " . mysqli_error($conn));
}


/* Add payment */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_id = $_POST['user_id'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];


    /* Get customer information */

    $customer_sql = "SELECT
                        c.internet_plan,
                        c.f_name,
                        c.m_name,
                        c.l_name
                     FROM customer_tbl c
                     WHERE c.user_id = '$user_id'";

    $customer_result2 = mysqli_query($conn, $customer_sql);

    if (!$customer_result2) {
        die("Customer query failed: " . mysqli_error($conn));
    }

    $customer = mysqli_fetch_assoc($customer_result2);

    if (!$customer) {
        die("Customer not found.");
    }


    $plan_id = $customer['internet_plan'];

    $f_name = $customer['f_name'];
    $m_name = $customer['m_name'];
    $l_name = $customer['l_name'];

    $payment_status = "Paid";

    $query = "INSERT INTO payment_tbl
              (plan_id, f_name, m_name, l_name, payment_method, amount, user_id, payment_status)
              VALUES
              ('$plan_id', '$f_name', '$m_name', '$l_name', '$payment_method', '$amount', '$user_id', '$payment_status')";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Payment insert failed: " . mysqli_error($conn));
    }

    $update_due_date = "UPDATE customer_tbl
                        SET due_date = DATE_ADD(due_date, INTERVAL 1 MONTH)
                        WHERE user_id = '$user_id'";

    $update_result = mysqli_query($conn, $update_due_date);

    if (!$update_result) {
        die("Payment was saved but due date update failed: " . mysqli_error($conn));
    }


    echo "<script>
        alert('Cash payment added successfully!');
        window.location.href = 'admin_payment.php';
    </script>";

    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <link rel="stylesheet" href="../css/admin_add_payment.css?v=999">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>

<body>

<?php include 'admin_sidebar_header_profile.php'; ?>

<div class="card w-75">
    <div class="card-body">
        <div class="add_payment">
            <h3>Add Cash Payment</h3>


            <form action="" method="post">

                <div class="form_group">

                    <label>Search Customer</label>

                    <input type="text" id="customer_search" class="form-control" placeholder="Search account number or customer name..." list="customer_list" autocomplete="off" required>

                    <datalist id="customer_list">

                        <?php while ($customer = mysqli_fetch_assoc($customer_result)) { ?>

                            <option
                                value="<?php
                                    echo $customer['account_number'] . ' - ' .
                                         $customer['f_name'] . ' ' .
                                         $customer['m_name'] . ' ' .
                                         $customer['l_name'];
                                ?>"
                                data-user-id="<?php echo $customer['user_id']; ?>"
                                data-account-number="<?php echo $customer['account_number']; ?>"
                                data-name="<?php
                                    echo $customer['f_name'] . ' ' .
                                         $customer['m_name'] . ' ' .
                                         $customer['l_name'];
                                ?>"
                                data-plan="<?php
                                    echo $customer['plan_name'] . ' ' .
                                         $customer['internet_mbps'] . ' Mbps = ₱' .
                                         number_format($customer['internet_price'], 2);
                                ?>"
                                data-price="<?php echo $customer['internet_price']; ?>"
                            >

                            </option>

                        <?php } ?>

                    </datalist>
                    <input type="hidden"name="user_id" id="user_id">
                </div>
      
                <div class="form_group">

                    <label>Customer Name</label>

                    <input type="text" id="customer_name" class="form-control" readonly>
                </div>

                <div class="form_group">

                    <label>Available Plan Details</label>

                    <input type="text" id="plan_details" class="form-control" readonly>
                </div>

                <div class="form_group">
                    <label>Payment Method</label>

                    <input type="text" value="Cash" class="form-control" readonly>
                    <input type="hidden" name="payment_method" value="Cash">
                </div>

                <div class="form_group">

                    <label>Amount</label>

                    <input type="number" name="amount" id="amount" class="form-control" readonly required >
                </div>

                <div class="form_group full_width">

                    <button type="submit" name="add" class="payment-plus"> Add Payment </button>
                </div>
            </form>

        </div>

    </div>

</div>

<script>

/* Customer search */

document.getElementById("customer_search").addEventListener("input", function() {

    var searchValue = this.value.toLowerCase();
    var options = document.querySelectorAll("#customer_list option");
    var found = false;

    for (var i = 0; i < options.length; i++) {
        var optionValue = options[i].value.toLowerCase();
        var accountNumber = options[i].getAttribute("data-account-number").toLowerCase();
        var customerName = options[i].getAttribute("data-name").toLowerCase();

        if (
            optionValue === searchValue ||
            accountNumber === searchValue ||
            customerName === searchValue
        ) {

            var userId = options[i].getAttribute("data-user-id");
            var name = options[i].getAttribute("data-name");
            var planDetails = options[i].getAttribute("data-plan");
            var planPrice = options[i].getAttribute("data-price");

            /* Automatically fill customer information */

            document.getElementById("user_id").value = userId;
            document.getElementById("customer_name").value = name;
            document.getElementById("plan_details").value = planDetails;
            document.getElementById("amount").value = planPrice;
            found = true;
            break;
        }
    }
    /* Clear fields if customer is not found */

    if (!found) {

        document.getElementById("user_id").value = "";
        document.getElementById("customer_name").value = "";
        document.getElementById("plan_details").value = "";
        document.getElementById("amount").value = "";
    }

});

/* Prevent invalid submission */

document.querySelector("form").addEventListener("submit", function(event) {
    var userId = document.getElementById("user_id").value;

    if (userId === "") {
        alert("Please select a valid customer.");
        event.preventDefault();
    }

});

</script>
</body>
</html>