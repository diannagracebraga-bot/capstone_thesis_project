<?php
include '../database/database_connection.php';

if(isset($_GET['updated'])){
    echo "<script>alert('Customer details updated successfully!');</script>";
}

if(isset($_GET['deleted'])){
    echo "<script>alert('Customer deleted successfully!');</script>";
}

if(isset($_GET['delete_error'])){
    echo "<script>alert('Unable to delete this customer. Please try again.');</script>";
}

$sql = "
SELECT
    c.customer_id,
    c.user_id,
    c.account_number,
    c.email_address AS email,
    c.password,
    c.first_name AS f_name,
    c.middle_name AS m_name,
    c.last_name AS l_name,
    c.contact_number,
    c.age,
    c.sex,
    c.civil_status,
    c.birth_date,
    c.barangay,
    c.subdivision,
    c.street,
    c.house_number AS house_name,
    c.internet_plan_id AS internet_plan,
    c.status AS connection_status,
    p.plan_id,
    p.plan_name
FROM customer_tbl c
LEFT JOIN internet_plan_tbl p
    ON c.internet_plan_id = p.plan_id
";
$result = mysqli_query($conn, $sql);
$plan_query = mysqli_query($conn, "SELECT * FROM internet_plan_tbl");

if (!$result || !$plan_query) {
    die('Unable to load customer records: ' . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <link rel="stylesheet" href="../css/admin_customer.css">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>

<?php include 'admin_sidebar_header_profile.php'; ?>

<h1>USER MANAGEMENT TRACKING</h1>

<div class="card w-75">
    <div class="card-body">

        <div class="table-container1">

            <div class="aligned">

                <div class="searchbar-container">
                    <input type="text" placeholder="Search.." name="search">
                    <button type="submit">Search</button>
                </div>

                <div class="add_customer">
                    <form action="admin_user_management.php" method="GET">
                        <input type="hidden" name="page" value="add_customer">
                        <button class="btn btn-primary" type="submit">
                            Add Customer
                        </button>
                    </form>
                </div>

            </div>

            <br>

            <table class="table table-secondary table-hover">

                <thead class="table-info">
                    <tr>
                        <th>CUSTOMER ID</th>
                        <th>ACCOUNT NUMBER</th>
                        <th>FIRST NAME</th>
                        <th>MIDDLE NAME</th>
                        <th>LAST NAME</th>
                        <th>CONTACT NUMBER</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>

                <tbody>

                <?php

                if(mysqli_num_rows($result) > 0){

                    while($row = mysqli_fetch_assoc($result)){
                ?>
                    <tr>
                        <td><?php echo $row['customer_id']; ?></td>
                        <td><?php echo $row['account_number']; ?></td>
                        <td><?php echo $row['f_name']; ?></td>
                        <td><?php echo $row['m_name']; ?></td>
                        <td><?php echo $row['l_name']; ?></td>
                        <td><?php echo $row['contact_number']; ?></td>
                        <td><?php echo $row['connection_status']; ?></td>
                        <td>

                            <button 
                                 class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                     data-bs-target="#editModal<?php echo $row['customer_id']; ?>">
                                             Edit
                            </button>

                            <a href="../database/delete_customer.php?id=<?php echo urlencode($row['customer_id']); ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this customer?')">
                                Delete
                             </a>

                        </td>

                    </tr>
            <div class="modal fade" id="editModal<?php echo $row['customer_id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit me-2"></i>Edit Customer Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="../database/admin_update_customer.php" method="POST">

                <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">

                <div class="modal-body">

                
                    <h6 class="text-black border-bottom pb-2 mb-3">
                        Account Information
                    </h6>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="<?php echo $row['account_number']; ?>"
                                   readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email"
                                   class="form-control"
                                   name="email"
                                   value="<?php echo $row['email']; ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   class="form-control"
                                   name="password"
                                   placeholder="Leave blank to keep current password">
                        </div>
                    </div>
                    <h6 class="text-black border-bottom pb-2 mt-3 mb-3">
                        Personal Information
                    </h6>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="f_name" value="<?php echo $row['f_name']; ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="m_name" value="<?php echo $row['m_name']; ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="l_name" value="<?php echo $row['l_name']; ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" name="contact_number" value="<?php echo $row['contact_number']; ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" name="age" value="<?php echo $row['age']; ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sex</label>
                            <select class="form-select" name="sex">
                                <option <?php if($row['sex']=="Male") echo "selected"; ?>>Male</option>
                                <option <?php if($row['sex']=="Female") echo "selected"; ?>>Female</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Civil Status</label>
                            <select class="form-select" name="civil_status">
                                <option <?php if($row['civil_status']=="Single") echo "selected"; ?>>Single</option>
                                <option <?php if($row['civil_status']=="Married") echo "selected"; ?>>Married</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Birth Date</label>
                            <input type="date" class="form-control" name="birth_date" value="<?php echo $row['birth_date']; ?>">
                        </div>

                    </div>

                    <h6 class="text-black border-bottom pb-2 mt-3 mb-3">
                        Address Information
                    </h6>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Barangay</label>
                            <select class="form-select" name="barangay">
                                <option <?php if($row['barangay']=="Bagtas") echo "selected"; ?>>Bagtas</option>
                                <option <?php if($row['barangay']=="Punta I") echo "selected"; ?>>Punta I</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Subdivision</label>
                            <input type="text" class="form-control" name="subdivision" value="<?php echo $row['subdivision']; ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Street</label>
                            <input type="text" class="form-control" name="street" value="<?php echo $row['street']; ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">House Number</label>
                            <input type="text" class="form-control" name="house_name" value="<?php echo $row['house_name']; ?>">
                        </div>

                    </div>

                    <h6 class="text-black border-bottom pb-2 mt-3 mb-3">
                        Internet Service
                    </h6>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Internet Plan</label>
                            <select class="form-select" name="internet_plan">
                                <?php
                                mysqli_data_seek($plan_query,0);
                                while($plan=mysqli_fetch_assoc($plan_query)){
                                ?>
                                <option value="<?php echo $plan['plan_id']; ?>"
                                    <?php if($row['internet_plan']==$plan['plan_id']) echo "selected"; ?>>
                                    <?php
                                    echo $plan['plan_name']." - ".
                                         $plan['internet_mbps']." Mbps - ₱".
                                         number_format($plan['internet_price'],2);
                                    ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Connection Status</label>
                            <select class="form-select" name="connection_status">
                                <option <?php if($row['connection_status']=="Connected") echo "selected"; ?>>Connected</option>
                                <option <?php if($row['connection_status']=="Disconnected") echo "selected"; ?>>Disconnected</option>
                            </select>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" name="update" class="btn btn-success">
                        Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

</form>
</div>
</div>
</div>
                <?php
                    }

                } else {

                    echo "<tr>
                            <td colspan='7' class='text-center'>
                                No Customer Registered
                            </td>
                          </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
