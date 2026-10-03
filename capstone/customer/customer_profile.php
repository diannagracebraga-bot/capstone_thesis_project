```php
<?php
session_start();

include '../database/database_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$edit_mode = false;

/* Update Profile */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $contact_number = $_POST['contact_number'];
    $barangay = $_POST['barangay'];

    $update = mysqli_query($conn, "
        UPDATE customer_tbl
        SET
            contact_number = '$contact_number',
            barangay = '$barangay'
        WHERE user_id = '$user_id'
    ");

    if ($update) {
        header("Location: customer_profile.php?updated=1");
        exit();
    } else {
        $message = "Failed to update profile.";
    }
}

/* Success Message */
if (isset($_GET['updated'])) {
    $message = "Profile updated successfully.";
}

/* Get Customer Information */
$query = mysqli_query($conn, "
SELECT
    c.*,
    u.email,
    u.role,
    p.plan_name,
    p.internet_mbps,
    p.internet_price
FROM customer_tbl c
INNER JOIN user_accounts_tbl u
    ON c.user_id = u.user_id
LEFT JOIN internet_plan_tbl p
    ON c.internet_plan = p.plan_id
WHERE c.user_id = '$user_id'
");

if (!$query) {
    die("Query Failed: " . mysqli_error($conn));
}

$customer = mysqli_fetch_assoc($query);

if (!$customer) {
    die("Customer record not found.");
}

$email_address = $customer['email'];
$role = $customer['role'];

$internet_plan = $customer['plan_name'] . " (" .
    $customer['internet_mbps'] . " Mbps) - ₱" .
    $customer['internet_price'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="../css/customer_sidebar_header.css?v=customer-shell-11">

    <link rel="stylesheet"
          href="../css/customer_profile.css?v=8">

</head>

<body>

<?php include 'customer_sidebar_header.php'; ?>

<main class="profile-content">

    <div class="card">

        <div class="card-body">

            <section class="profile-card">

                <div class="profile-title-row">

                    <h2>Account Information</h2>

                    <?php if ($edit_mode) { ?>

                        <div class="profile-button-row">

                            <a href="customer_profile.php"
                               class="btn btn-secondary">
                                Cancel
                            </a>

                            <button type="submit"
                                    form="profileForm"
                                    class="btn btn-success">
                                Save Changes
                            </button>

                        </div>

                    <?php } ?>

                </div>


                <?php if ($message != ""): ?>

                    <div class="alert alert-success">

                        <?php echo $message; ?>

                    </div>

                <?php endif; ?>


                <form method="POST" id="profileForm">

                    <div class="row g-3">


                        <!-- Account Number -->

                        <div class="col-md-2">

                            <label class="form-label">
                                Account Number:
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['account_number']; ?>"
                                   readonly>

                        </div>


                        <!-- Email -->

                        <div class="col-md-7">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input type="email"
                                   class="form-control"
                                   value="<?php echo $customer['email']; ?>"
                                   readonly>

                        </div>


                        <!-- Password -->

                        <div class="col-md-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input type="password"
                                   class="form-control"
                                   value="password"
                                   readonly>

                        </div>


                        <!-- First Name -->

                        <div class="col-md-4">

                            <label class="form-label">
                                First Name
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['f_name']; ?>"
                                   readonly>

                        </div>


                        <!-- Middle Name -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Middle Name
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['m_name']; ?>"
                                   readonly>

                        </div>


                        <!-- Last Name -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Last Name
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['l_name']; ?>"
                                   readonly>

                        </div>


                        <!-- Contact Number -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Contact Number
                            </label>

                            <input type="text"
                                   class="form-control"
                                   name="contact_number"
                                   value="<?php echo $customer['contact_number']; ?>"
                                   <?php echo $edit_mode ? "" : "readonly"; ?>>

                        </div>


                        <!-- Age -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Age
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['age']; ?>"
                                   readonly>

                        </div>


                        <!-- Sex -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Sex
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['sex']; ?>"
                                   readonly>

                        </div>


                        <!-- Barangay -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Barangay
                            </label>

                            <?php if ($edit_mode) { ?>

                                <select class="form-select"
                                        name="barangay">

                                    <option value="Bagtas"
                                        <?php
                                        if ($customer['barangay'] == "Bagtas")
                                            echo "selected";
                                        ?>>
                                        Bagtas
                                    </option>

                                    <option value="Punta I"
                                        <?php
                                        if ($customer['barangay'] == "Punta I")
                                            echo "selected";
                                        ?>>
                                        Punta I
                                    </option>

                                </select>

                            <?php } else { ?>

                                <input type="text"
                                       class="form-control"
                                       value="<?php echo $customer['barangay']; ?>"
                                       readonly>

                            <?php } ?>

                        </div>

                                  <div class="col-md-4">

                            <label class="form-label">
                                House Address
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo ucfirst($customer['house_name']); ?>"
                                   readonly>

                        </div>
                        <!-- Role -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Role
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo ucfirst($customer['role']); ?>"
                                   readonly>

                        </div>


                        <!-- Internet Plan -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Internet Plan
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $internet_plan; ?>"
                                   readonly>

                        </div>


                        <!-- Connection Status -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Connection Status
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['connection_status']; ?>"
                                   readonly>

                        </div>


                        <!-- Due Date -->

                        <div class="col-md-4">

                            <label class="form-label">
                                Due Date
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="<?php echo $customer['due_date']; ?>"
                                   readonly>

                        </div>


                    </div>

                </form>

            </section>

        </div>

    </div>

</main>

</body>

</html>
