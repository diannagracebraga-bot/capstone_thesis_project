<?php
session_start();

include '../database/database_connection.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$edit_mode = isset($_GET['edit']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $contact_number = $_POST['contact_number'];
    $civil_status = $_POST['civil_status'];
    $barangay = $_POST['barangay'];
    $subdivision = $_POST['subdivision'];
    $street = $_POST['street'];
    $house_name = $_POST['house_name'];

    $update = mysqli_query($conn, "
        UPDATE customer_tbl
        SET
            contact_number = '$contact_number',
            civil_status = '$civil_status',
            barangay = '$barangay',
            subdivision = '$subdivision',
            street = '$street',
            house_name = '$house_name'
        WHERE user_id = '$user_id'
    ");

    if ($update) {
        header("Location: customer_profile.php?updated=1");
        exit();
    } else {
        $message = "Failed to update profile.";
    }
}

if (isset($_GET['updated'])) {
    $message = "Profile updated successfully.";
}

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
    ON c.user_id = u.id
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
    $customer['internet_mbps'] . " Mbps) - ₱" .$customer['internet_price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/customer_sidebar_header.css?v=9">
    <link rel="stylesheet" href="../css/customer_profile.css?v=8">
</head>
<body>

<?php include 'customer_sidebar_header.php'; ?>

<main class="profile-content">
      <div class="card">
  				<div class="card-body">
    <section class="profile-card">

        <div class="profile-title-row">
            <h2>Customer Information</h2>
                  <?php if($edit_mode){ ?>

                <div class="profile-button-row">
                    <a href="customer_profile.php" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        Save Changes
                    </button>
                </div>

                <?php } ?>

            <?php if (!$edit_mode): ?>
                <a href="customer_profile.php?edit=1" class="btn btn-primary profile-action-btn">
                    Edit Customer Details
                </a>
            <?php endif; ?>
        </div>

        <?php if ($message != ""): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="row g-3">

                <div class="col-md-2">
                    <label class="form-label">Account Number:</label>
                    <input type="text" class="form-control"
                        value="<?php echo $customer['account_number']; ?>" readonly>
                </div>

                <div class="col-md-7">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control"
                        value="<?php echo $customer['email']; ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control"
                        value="password" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control"
                        value="<?php echo $customer['f_name']; ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" class="form-control"
                        value="<?php echo $customer['m_name']; ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control"
                        value="<?php echo $customer['l_name']; ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Contact Number</label>
                    <input type="text"
                        class="form-control"
                        name="contact_number"
                        value="<?php echo $customer['contact_number']; ?>"
                        <?php echo $edit_mode ? "" : "readonly"; ?>>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Age</label>
                    <input type="text"
                        class="form-control"
                        value="<?php echo $customer['age']; ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sex</label>
                    <input type="text"
                        class="form-control"
                        value="<?php echo $customer['sex']; ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Civil Status</label>

                    <?php if($edit_mode){ ?>

                        <select class="form-select" name="civil_status">
                            <option value="Single" <?php if($customer['civil_status']=="Single") echo "selected"; ?>>Single</option>
                            <option value="Married" <?php if($customer['civil_status']=="Married") echo "selected"; ?>>Married</option>
                            <option value="Widowed" <?php if($customer['civil_status']=="Widowed") echo "selected"; ?>>Widowed</option>
                            <option value="Separated" <?php if($customer['civil_status']=="Separated") echo "selected"; ?>>Separated</option>
                        </select>

                    <?php }else{ ?>

                        <input type="text"
                            class="form-control"
                            value="<?php echo $customer['civil_status']; ?>" readonly>

                    <?php } ?>

                </div>

                <div class="col-md-4">
                    <label class="form-label">Birth Date</label>
                    <input type="text"
                        class="form-control"
                        value="<?php echo $customer['birth_date']; ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Barangay</label>

                    <?php if($edit_mode){ ?>

                        <select class="form-select" name="barangay">
                            <option value="Bagtas " <?php if($customer['barangay']=="Bagtas") echo "selected"; ?>>Bagtas</option>
                            <option value="Punta I" <?php if($customer['barangay']=="Punta I") echo "selected"; ?>>Punta I</option>
                           
                        </select>

                    <?php }else{ ?>

                        <input type="text" class="form-control"
                            value="<?php echo $customer['barangay']; ?>" readonly>
                    <?php } ?>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Subdivision</label>
                    <input type="text" class="form-control"  name="subdivision"
                        value="<?php echo $customer['subdivision']; ?>"
                        <?php echo $edit_mode ? "" : "readonly"; ?>>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Street</label>
                    <input type="text" class="form-control" name="street"
                        value="<?php echo $customer['street']; ?>"
                        <?php echo $edit_mode ? "" : "readonly"; ?>>
                </div>

                <div class="col-md-4">
                    <label class="form-label">House Number</label>
                    <input type="text" class="form-control" name="house_name"
                        value="<?php echo $customer['house_name']; ?>"
                        <?php echo $edit_mode ? "" : "readonly"; ?>>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control"
                         value="<?php echo ucfirst($customer['role']); ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Internet Plan</label>
                    <input type="text" class="form-control"
                        value="<?php echo $internet_plan; ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Connection Status</label>
                    <input type="text" class="form-control"
                        value="<?php echo $customer['connection_status']; ?>" readonly>
                </div>

          
            </div>
        </form>
    </section>
        </div>
        </div>
</main>
</body>
</html>