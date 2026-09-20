<?php
include '../database/database_connection.php';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_payment.css">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <title>Payments</title>
</head>
<body>
<?php include 'admin_sidebar_header_profile.php'; ?>
<div class="card w-75">
    <div class="card-body">
        <div class="table-container">
            <div class="aligned">
                <div class="searchbar-container">
                    <form method="GET" action="">
                        <input 
                            type="text" 
                            placeholder="Search.." 
                            name="search"
                            value="<?php echo htmlspecialchars($search); ?>" >
                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>
                        <?php if (!empty($search)) { ?>
                            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" 
                               class="btn btn-secondary">
                                Clear
                            </a>
                        <?php } ?>
                    </form>
                </div>
                <div class="payment-plus">
                    <form action="admin_add_payment.php" method="get">
<<<<<<< HEAD:admin/admin_payment.php
                        <button type="submit" class="btn btn-success">
                            Add Payment
                        </button>
                    </form>
                </div>
            </div>
            <br>
            <table class="table table-secondary table-hover">
                <thead class="table-info">
                    <tr>
                        <th>ID</th>
                        <th>PLAN</th>
                        <th>FIRST NAME</th>
                        <th>MIDDLE NAME</th>
                        <th>LAST NAME</th>
                        <th>PAYMENT METHOD</th>
                        <th>DUE DATE</th>
                        <th>AMOUNT</th>
                        <th>REMARKS</th>
                        <th>ACTION</th>
=======
						<button type="submit" class="btn btn-success">
							Add Payment
						</button>
					</form>
               
</div> 
        </div>
		<br>
				<table class = "table table-secondary table-hover">
					<thead class = "table-info">
					<tr>
						<th> ID </th>
						<th> FIRST NAME </th>
						<th> MIDDLE NAME </th>
						<th> LAST NAME </th>
						<th> PAYMENT METHOD </th>
						<th> AMOUNT </th>
						<th> REMARKS </th>
						<th> ACTION </th>
					</tr>
					
					</thead>
					<?php
					$query = "SELECT * FROM payment_tbl";
					$result = mysqli_query($conn, $query);
					if (!$result) {
						die("Query failed: " . mysqli_error($conn));
					} 
					else {
					while($row = mysqli_fetch_assoc($result)){
						?>
					<tr>
						<td> <?php echo $row['id'];?> </td>
						<td> <?php echo $row['f_name'];?> </td>
						<td> <?php echo $row['m_name'];?> </td>
						<td> <?php echo $row['l_name'];?> </td>
						<td> <?php echo $row['payment_method'];?> </td>
						<td> <?php echo $row['amount'];?> </td>
						<td> <?php echo $row['remarks'];?> </td>
						<td> <a href="update.php?id=<?php echo $row['id']; ?>"> 
							<button class = "btn btn-primary">update</button>
						     </a>
							 <a href="../crud/delete_payment.php?id=<?php echo $row['id']; ?>">
								<button class = "btn btn-primary">delete</button>
							 </a>
						</td>
>>>>>>> 772f60352770c40bb033bc27aa84fd33bd1af6a2:capstone/admin/admin_payment.php
                    </tr>
                </thead>
                <tbody>
                <?php
                if (empty($search)) {
                    $query = "SELECT * FROM payment_tbl";
                    $result = mysqli_query($conn, $query);
                } else {
                    $searchTerm = mysqli_real_escape_string($conn, $search);
                    $query = "SELECT * FROM payment_tbl
                              WHERE id LIKE '%$searchTerm%'
                              OR plan LIKE '%$searchTerm%'
                              OR f_name LIKE '%$searchTerm%'
                              OR m_name LIKE '%$searchTerm%'
                              OR l_name LIKE '%$searchTerm%'
                              OR payment_method LIKE '%$searchTerm%'
                              OR due_date LIKE '%$searchTerm%'
                              OR amount LIKE '%$searchTerm%'
                              OR remarks LIKE '%$searchTerm%'";
                    $result = mysqli_query($conn, $query);
                }
                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($row['id']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['plan']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['f_name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['m_name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['l_name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['payment_method']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['due_date']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['amount']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['remarks']); ?>
                        </td>
                        <td>
                            <a href="update.php?id=<?php echo $row['id']; ?>"
                               class="btn btn-primary">
                                Update
                            </a>
                            <a href="../crud/delete_payment.php?id=<?php echo $row['id']; ?>"
                               class="btn btn-danger"
                               onclick="return confirm('Are you sure you want to delete this payment?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="10" class="text-center">
                            No payments found.
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
