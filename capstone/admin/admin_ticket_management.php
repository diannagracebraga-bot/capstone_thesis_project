<?php
include '../database/database_connection.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $search_safe = mysqli_real_escape_string($conn, $search);

    $query = "SELECT * FROM ticket_management_tbl
              WHERE ticket_id LIKE '%$search_safe%'
              OR full_name LIKE '%$search_safe%'
              OR email_address LIKE '%$search_safe%'
              OR contact_number LIKE '%$search_safe%'
              OR concern_type LIKE '%$search_safe%'
              OR date_received LIKE '%$search_safe%'
              OR status LIKE '%$search_safe%'
              ORDER BY ticket_id DESC";
} else {
    $query = "SELECT * FROM ticket_management_tbl
              ORDER BY ticket_id ASC";
}

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
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
	<link rel="stylesheet" href="../css/admin_ticket_management.css">
	<title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>
	<?php include 'admin_sidebar_header_profile.php'; ?>
		
		
			<div class="card w-75">
  				<div class="card-body">
			<div class = "table-container">
				<div class = "aligned" >

<div class="searchbar-container">
    <form method="GET" action="" class="input-group">
        <input type="text" name="search" placeholder="Search..." class="form-control" value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">

        <button type="submit" class="btn btn-primary">
            Search
        </button>

        <?php if ($search !== '') { ?>
            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
               class="btn btn-secondary">
                Clear
            </a>
        <?php } ?>
    </form>
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
						<th>DATE RECEIVED </th>
						<th> STATUS </th>
						<th> ACTION </th>
					</tr>
					</thead>
					<tbody>
						<?php while ($ticket = mysqli_fetch_assoc($result)): ?>
						<tr>
							<td><?php echo $ticket['ticket_id']; ?></td>
							<td><?php echo $ticket['full_name']; ?></td>
							<td><?php echo $ticket['email_address']; ?></td>
							<td><?php echo $ticket['contact_number']; ?></td>
							<td><?php echo $ticket['concern_type']; ?></td>
							<td><?php echo $ticket['date_received']; ?></td>
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
    <a href="admin_view_ticket_management.php?ticket_id=<?php echo $ticket['ticket_id']; ?>"
        class="btn btn-primary">View
    </a>
<a href="../crud/delete_ticket.php?ticket_id=<?php echo $ticket['ticket_id']; ?>"
   class="btn btn-danger"
   onclick="return confirm('Are you sure you want to delete this ticket?');">
    Delete
</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>

</table>
<script>
document.getElementById("searchBtn").addEventListener("click", function () {
    let searchValue = document.getElementById("ticketSearch").value.toLowerCase().trim();
    let rows = document.querySelectorAll("table tbody tr");
    rows.forEach(function (row) {
        let rowText = row.textContent.toLowerCase();
        if (rowText.includes(searchValue)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
document.getElementById("ticketSearch").addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        document.getElementById("searchBtn").click();
    }
});
</script>

</div>
</div>		
</body>
</html>
