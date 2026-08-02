<?php
include '../database/database_connection.php';
?>

<!DOCTYPE html>
<html>
<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/admin_payment.css">
	<link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
	<title></title>
</head>
<body>
	<?php include 'admin_sidebar_header_profile.php'; ?>
	
		<h1> USER MANAGEMENT TRACKING </h1>
		 <div class="card w-75">
  				<div class="card-body">
		<div class = "table-container">
			<div class = "aligned">
        <div class="searchbar-container">
                <input type="text" placeholder="Search.." name="search">
                <button type="submit">Search</button>
			</div>        
				<div class = "payment-plus">
                    <form action="admin_add_payment.php" method="get">
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
						<th> PLAN </th>
						<th> FIRST NAME </th>
						<th> MIDDLE NAME </th>
						<th> LAST NAME </th>
						<th> PAYMENT METHOD </th>
						<th> DUE DATE </th>
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
						<td> <?php echo $row['plan'];?> </td>
						<td> <?php echo $row['f_name'];?> </td>
						<td> <?php echo $row['m_name'];?> </td>
						<td> <?php echo $row['l_name'];?> </td>
						<td> <?php echo $row['payment_method'];?> </td>
						<td> <?php echo $row['due_date'];?> </td>
						<td> <?php echo $row['amount'];?> </td>
						<td> <?php echo $row['remarks'];?> </td>
						<td> <a href="../database/update.php?id=<?php echo $row['id']; ?>"> 
							<button class = "btn btn-primary">update</button>
						     </a>
							 <a href="../database/delete.php?id=<?php echo $row['id']; ?>">
								<button class = "btn btn-primary">delete</button>
							 </a>
						</td>
                    </tr>
						<?php
					}
					}
 ?>
 
</table>
</div>
</div>
</div>
</body>
</html>