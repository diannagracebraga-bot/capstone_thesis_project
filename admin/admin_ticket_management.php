<?php
include '../database/database_connection.php';

$query = "SELECT * FROM ticket_management_tbl ORDER BY ticket_id ASC";
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
					<tbody>
						<?php while ($ticket = mysqli_fetch_assoc($result)): ?>
						<tr>
							<td><?php echo htmlspecialchars($ticket['ticket_id']); ?></td>
							<td><?php echo htmlspecialchars($ticket['full_name']); ?></td>
							<td><?php echo htmlspecialchars($ticket['email_address']); ?></td>
							<td><?php echo htmlspecialchars($ticket['contact_number']); ?></td>
							<td><?php echo htmlspecialchars($ticket['concern_type']); ?></td>
							<td><?php echo htmlspecialchars($ticket['date_received']); ?></td>
								<td>
			<?php

				if($ticket['status']=="Pending"){
  				  echo '<span class="badge status-badge bg-warning text-dark">Pending</span>';
					}
				elseif($ticket['status']=="Ongoing"){
  				  echo '<span class="badge status-badge bg-primary">Ongoing</span>';
					}
				elseif($ticket['status']=="Resolved"){
   				 echo '<span class="badge status-badge bg-success">Resolved</span>';
					}
?>
</td>
						<td>
    <a href="admin_view_ticket_management.php?ticket_id=<?php echo $ticket['ticket_id']; ?>">
        <button class="btn btn-primary">View</button>
    </a>
</td>
						</tr>
						<?php endwhile; ?>
					</tbody>

</table>
</div>
</div>		
</body>
</html>
