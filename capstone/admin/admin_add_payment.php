```php
<?php
include '../database/database_connection.php';

/* Get customers */
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
    $remarks = $_POST['remarks'];


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


    /*
        Insert payment into payment_tbl
    */

    $query = "INSERT INTO payment_tbl
              (
                  plan_id,
                  f_name,
                  m_name,
                  l_name,
                  payment_method,
                  amount,
                  remarks,
                  user_id,
                  payment_status
              )
              VALUES
              (
                  '$plan_id',
                  '$f_name',
                  '$m_name',
                  '$l_name',
                  '$payment_method',
                  '$amount',
                  '$remarks',
                  '$user_id',
                  '$payment_status'
              )";

    $result = mysqli_query($conn, $query);


    if (!$result) {
        die("Payment insert failed: " . mysqli_error($conn));
    }


    /*
        Update customer's due date
        to next month
    */

    $update_due_date = "UPDATE customer_tbl
                        SET due_date = DATE_ADD(due_date, INTERVAL 1 MONTH)
                        WHERE user_id = '$user_id'";

    $update_result = mysqli_query($conn, $update_due_date);


    if (!$update_result) {
        die("Payment was saved but due date update failed: " . mysqli_error($conn));
    }


    /*
        Payment successfully recorded
    */

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

    <link rel="stylesheet" href="../css/admin_add_payment.css">

    <title>MITZTIANPC WIRED INTERNET SERVICES</title>

</head>

<body>

<?php include 'admin_sidebar_header_profile.php'; ?>


<div class="card w-75">

    <div class="card-body">

        <div class="add_payment">

            <h3>Add Cash Payment</h3>


            <form action="" method="post">


                <!-- CUSTOMER -->

                <div class="form_group">

                    <label>Customer</label>

                    <select name="user_id" required>

                        <option value="">
                            -- Select Customer --
                        </option>


                        <?php

                        while ($customer = mysqli_fetch_assoc($customer_result)) {

                        ?>

                            <option value="<?php echo $customer['user_id']; ?>">

                                <?php

                                echo $customer['f_name'] . ' ' .
                                     $customer['m_name'] . ' ' .
                                     $customer['l_name'] .
                                     ' - ' .
                                     $customer['account_number'];

                                ?>

                            </option>

                        <?php

                        }

                        ?>

                    </select>

                </div>


                <!-- PAYMENT METHOD -->

                <div class="form_group">

                    <label>Payment Method</label>

                    <select name="payment_method" required>

                        <option value="">
                            -- Select --
                        </option>

                        <option value="Cash">
                            Cash
                        </option>

                    </select>

                </div>


                <!-- AMOUNT -->

                <div class="form_group">

                    <label>Amount</label>

                    <input
                        type="number"
                        name="amount"
                        step="0.01"
                        required
                    >

                </div>


                <!-- REMARKS -->

                <div class="form_group">

                    <label>Remarks</label>

                    <input
                        type="text"
                        name="remarks"
                        value="Cash payment"
                        required
                    >

                </div>


                <!-- BUTTON -->

                <div class="form_group full_width">

                    <button
                        type="submit"
                        name="add"
                        class="payment-plus"
                    >
                        Add Payment
                    </button>

                </div>


            </form>

        </div>

    </div>

</div>


</body>

</html>
```
