<?php
include '../database/database_connection.php';

$result = mysqli_query($conn, "SELECT MAX(customer_id) AS last_id FROM customer_tbl");
$row = mysqli_fetch_assoc($result);

$number = $row['last_id'] + 1;

if ($number < 10) {
    $account_number = "MPC-0000" . $number;
} elseif ($number < 100) {
    $account_number = "MPC-000" . $number;
} elseif ($number < 1000) {
    $account_number = "MPC-00" . $number;
} else {
    $account_number = "MPC-0" . $number;
}

$plan_query = mysqli_query($conn, "SELECT * FROM internet_plan_tbl ORDER BY plan_id ASC");

?>
<link rel="stylesheet" href="../css/admin_add_customer.css">

<div class="customer_registration">
    <h3>Customer Registration</h3>
    <form action="../database/insert_customer.php" method="POST">
        <div class="form_grid">
            
        <div class="form_group">
              <label>Account Number</label>
              <input type="text" class = "account_number" value="<?php echo $account_number; ?>"  readonly>
                <input type="hidden" name="account_number" value="<?php echo $account_number; ?>">
            </div>
            <div class="form_group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form_group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form_group">
                <label>First Name</label>
                <input type="text" name="f_name" required>
            </div>
            <div class="form_group">
                <label>Middle Name</label>
                <input type="text" name="m_name">
            </div>
            <div class="form_group">
                <label>Last Name</label>
                <input type="text" name="l_name" required>
            </div>
            <div class="form_group">
                <label>Contact Number</label>
                <input type="number" name="contact_number" required>
            </div>
            <div class="form_group">
                <label>Age</label>
                <input type="number" name="age" required>
            </div>
            <div class="form_group">
                <label>Sex</label>
                <select name="sex">
                    <option>Male</option>
                    <option>Female</option>
                </select>
            </div>
            <div class="form_group">
                <label>Civil Status</label>
                <select name="civil_status">
                    <option>Single</option>
                    <option>Married</option>
                </select>
            </div>
            <div class="form_group">
                <label>Birth Date</label>
                <input type="date" name="birth_date">
            </div>
            <div class="form_group">
            <label>Barangay</label>
                <select name="barangay">
                    <option>Bagtas</option>
                    <option>Punta I</option>
            
                </select>
            </div>
            <div class="form_group">
                <label>Subdivision</label>
                <input type="text" name="subdivision">
            </div>
            <div class="form_group">
                <label>Street</label>
                <input type="text" name="street">
            </div>
            <div class="form_group">
                <label>House Number</label>
                <input type="text" name="house_number">
            </div>
            <div class="form_group">
                <label>Internet Plan</label>
        <?php
                ?> <select name="internet_plan">
                        <?php while($plan = mysqli_fetch_assoc($plan_query)){ ?>
                        <option value="<?php echo $plan['plan_id']; ?>">
                        <?php echo $plan['plan_name']; ?> -
                        <?php echo $plan['internet_mbps']; ?> Mbps -
                       ₱<?php echo $plan['internet_price']; ?></option>
                        <?php } ?>
                  </select>
            </div>
            <div class="form_group">
            <label>Connection Status</label>
                <select name="connection_status">
                    <option>Connected</option>
                    <option>Disconnected</option>
                </select>
            </div>
   <div class="form_group">
            <label for="due_date">Due Date</label>
<input type="date" name="due_date" id="due_date" required>

  </div>        

<div class="form_group full_width">
           <input type="submit" class = "btn btn-success"name="register" value="Register">
           </div>
        </div>
    </form>
</div>