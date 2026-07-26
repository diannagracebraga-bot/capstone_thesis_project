<?php
include '../database/database_connection.php';

$sql = "
SELECT 
    c.*,
    u.email,
    u.password

FROM customer_tbl c

INNER JOIN user_accounts_tbl u

ON c.user_id = u.id
";

$result = mysqli_query($conn, $sql);
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

<h1>CUSTOMER MANAGEMENT</h1>

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

                            <a href="../database/delete_customer.php?id=<?php echo $row['customer_id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this customer?')">
                                Delete
                             </a>

                        </td>

                    </tr>
                    <div class="modal fade" id="editModal<?php echo $row['customer_id']; ?>" tabindex="-1">

                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header bg-primary text-white">
                             <h5 class="modal-title">Edit Customer</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                <form action="../database/update_customer.php" method="POST">

                    <div class="modal-body">
                        <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Email Address</label>
                        <input type="email" class="form-control"name="email" value="<?php echo $row['email']; ?>">
                        </div>

                    <div class="col-md-6 mb-3">
                        <label>Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="f_name" value="<?php echo $row['f_name']; ?>">
                        </div>

                    <div class="col-md-4 mb-3">
                        <label>Middle Name</label>
                            <input type="text" class="form-control"name="m_name" value="<?php echo $row['m_name']; ?>">
                    </div>

                <div class="col-md-4 mb-3">
                    <label>Last Name</label>
                        <input type="text" class="form-control" name="l_name" value="<?php echo $row['l_name']; ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Contact Number</label>
                        <input type="number" class="form-control" name="contact_number" value="<?php echo $row['contact_number']; ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Age</label>
                        <input type="number" class="form-control" name="age" value="<?php echo $row['age']; ?>">
               </div>

                <div class="col-md-6 mb-3">
                    <label>Sex</label>
                        <select class="form-control" name="sex">
                            <option <?php if($row['sex']=="Male") echo "selected"; ?>>Male</option>
                            <option <?php if($row['sex']=="Female") echo "selected"; ?>>Female</option>

                        </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Civil Status</label>
                    <select class="form-control" name="civil_status">
                        <option <?php if($row['civil_status']=="Single") echo "selected"; ?>>Single</option>
                        <option <?php if($row['civil_status']=="Married") echo "selected"; ?>>Married</option>

                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Birth Date</label>
                        <input type="date"class="form-control" name="birth_date "value="<?php echo $row['birth_date']; ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Barangay</label>
                         <select class="form-control" name="barangay">
                            <option <?php if($row['barangay']=="Bagtas") echo "selected"; ?>>Bagtas
                            </option>

                            <option <?php if($row['barangay']=="Punta I") echo "selected"; ?>>Punta I
                            </option>

            </select>
                    </div>

                <div class="col-md-6 mb-3">
                    <label>Subdivision</label>
                        <input type="text"class="form-control"name="subdivision"value="<?php echo $row['subdivision']; ?>">
</div>

                <div class="col-md-6 mb-3">
                    <label>Street</label>
                        <input type="text" class="form-control" name="street" value="<?php echo $row['street']; ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>House Number</label>
                        <input type="text" class="form-control" name="house_name" value="<?php echo $row['house_name']; ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Connection Status</label>
                        <select class="form-control" name="connection_status">
                            <option <?php if($row['connection_status']=="Connected") echo "selected"; ?>>Connected
                            </option>

                            <option <?php if($row['connection_status']=="Disconnected") echo "selected"; ?>>Disconnected
                            </option>

            </select>

        </div>
</div>
</div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
            </button>

            <button type="submit" name="update"class="btn btn-primary">Save Changes
            </button>


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