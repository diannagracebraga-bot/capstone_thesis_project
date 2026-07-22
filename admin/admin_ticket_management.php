<?php
include '../database/database_connection.php';

$query = "SELECT * FROM ticket_management_tbl
          ORDER BY date_received DESC, ticket_id DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Ticket query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
	<link rel="stylesheet" href="../css/admin_ticket_management.css">
	<title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>
	<?php include 'admin_sidebar_header_profile.php'; ?>
		
		<h1> USER MANAGEMENT TRACKING </h1>
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
					<thead class = "table-info">
					<tr>
						<th>TICKET ID </th>
						<th>FULL NAME </th>
						<th>EMAIL ADDRESS</th>
						<th>CONTACT NUMBER</th>
						<th>CONCERN TYPE</th>
						<th>DATE RECIEVED </th>
						<th> STATUS </th>
						<th> ACTION </th>
					</tr>
					</thead>
					<tr>
						<td>32</td>
						<td>Heart Joy</td>
						<td>heart@gmail.com</td>
						<td>09 266 849 711</td>
						<td>Slow Internet Connection</td>
						<td>06-24-26</td>
						<td>Pending</td>
						<td><a href="admin_view_ticket_management.php"><button>View</button></a></td>
						</tr>

</table>
</div>
</div>
				
</body>
</html>