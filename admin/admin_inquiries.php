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
	<link rel="stylesheet" href="../css/admin_inquiries.css">

	<title>MITZIANPC WIRED INTERNET SERVICES</title>

</head>
<body>
	<?php include 'admin_sidebar_header_profile.php'; ?>

		
				<div class="card w-75">
  				<div class="card-body">
			<div class = "table-container">
				<div class = "aligned" >
        <div class="searchbar-container">
                <input type="text" placeholder="Search.." name="search">
                <button type="submit">Search</button>
				</div>
</div>
		<br>
				<table class = "table table-secondary table-hover">
					<thead class = " table-info">
					<tr>
						<th> ID </th>
						<th>FULL NAME </th>
						<th>EMAIL ADDRESS</th>
						<th> CONTACT NUMBER </th>
						<th> DATE RECEIVED </th>
						<th> STATUS </th>
						<th> ACTION </th>
					</tr>
					</thead>
<?php
$sql = "SELECT * FROM inquiries_tbl";
$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
?>
    <tr>
        <td><?php echo $row['inquiries_id']; ?></td>
        <td><?php echo $row['full_name']; ?></td>
        <td><?php echo $row['email_address']; ?></td>
        <td><?php echo $row['contact_number']; ?></td>
        <td><?php echo $row['date_received']; ?></td>
     	<td>
			<?php

				if($row['status']=="Pending"){
  				  echo '<span class="badge status-badge bg-warning text-dark">Pending</span>';
					}
				elseif($row['status']=="Ongoing"){
  				  echo '<span class="badge status-badge bg-primary">Ongoing</span>';
					}
				elseif($row['status']=="Resolved"){
   				 echo '<span class="badge status-badge bg-success">Resolved</span>';
					}
?>
</td>
        <td>
            <a href="admin_view_inquiries.php?inquiries_id=<?php echo $row['inquiries_id']; ?>"
                 class = "btn btn-primary">View
				
            </a>
			<a href="../crud/delete_inquiry.php?inquiries_id=<?php echo $row['inquiries_id']; ?>"
   class="btn btn-danger"
   onclick="return confirm('Are you sure you want to delete this inquiry?');">
    Delete
</a>
        </td> 	
    </tr>
<?php
} 
?>
</table>
</div>
</div>		
</body>
</html>