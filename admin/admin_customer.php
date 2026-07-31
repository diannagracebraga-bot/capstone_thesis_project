<?php
include '../database/database_connection.php';
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

                $sql = "SELECT * FROM customer_tbl";
                $result = mysqli_query($conn, $sql);

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

                            <a href="edit_customer.php?id=<?php echo $row['customer_id']; ?>" class="btn btn-primary btn-sm">
                                Edit
                            </a>

                            <a href="../database/delete_customer.php?id=<?php echo $row['customer_id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this customer?')">
                                Delete
                            </a>

                        </td>

                    </tr>

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

</body>
</html>