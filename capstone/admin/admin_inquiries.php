<?php
include '../database/database_connection.php';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $sql = "SELECT * FROM inquiries_tbl
            WHERE inquiries_id LIKE '%$search_safe%'
            OR full_name LIKE '%$search_safe%'
            OR email_address LIKE '%$search_safe%'
            OR contact_number LIKE '%$search_safe%'
            OR date_received LIKE '%$search_safe%'
            OR status LIKE '%$search_safe%'
            ORDER BY inquiries_id DESC";
} else {
    $sql = "SELECT * FROM inquiries_tbl
            ORDER BY inquiries_id DESC";
}
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css">
    <link rel="stylesheet" href="../css/admin_inquiries.css">
    <title>MITZIANPC WIRED INTERNET SERVICES</title>
</head>
<body>
<?php include 'admin_sidebar_header_profile.php'; ?>
<div class="card w-75">
    <div class="card-body">
        <div class="table-container">
            <div class="aligned">
                <form method="GET" action="" class="searchbar-container">
                    <input type="text" placeholder="Search inquiries..." name="search" value="<?php echo htmlspecialchars($search); ?>" >
                    <button type="submit" class="btn btn-primary"> Search </button>
                    <?php if ($search !== '') { ?>
                        <a href="admin_inquiries.php" class="btn btn-secondary"> Clear </a>
                    <?php } ?>
                </form>
            </div>
            <br>
            <table class="table table-secondary table-hover">
                <thead class="table-info">
                    <tr>
                        <th>ID</th>
                        <th>FULL NAME</th>
                        <th>EMAIL ADDRESS</th>
                        <th>CONTACT NUMBER</th>
                        <th>DATE RECEIVED</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                      <td>
                         <?php echo $row['inquiries_id']; ?>
                     </td>
                      <td>
                         <?php echo $row['full_name']; ?>
                     </td>
                      <td>
                         <?php echo $row['email_address']; ?>
                     </td>
                      <td>
                         <?php echo $row['contact_number']; ?>
                     </td>
                      <td>
                        <?php echo $row['date_received']; ?>
                     </td>
                        <td>
                            <?php
                            if ($row['status'] == "Pending") {
                                echo '<span class="badge status-badge bg-warning text-dark">Pending</span>';
                            }
                            elseif ($row['status'] == "Ongoing") {
                                echo '<span class="badge status-badge bg-primary">Ongoing</span>';
                            }
                            elseif ($row['status'] == "Resolved") {
                                echo '<span class="badge status-badge bg-success">Resolved</span>';
                            }
                            else {
                                echo '<span class="badge status-badge bg-secondary">'
                                    . htmlspecialchars($row['status'])
                                    . '</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="admin_view_inquiries.php?inquiries_id=<?php echo $row['inquiries_id']; ?>">
                                <button type="button" class="btn btn-primary"> View </button>
                            </a>
                        </td>
                    </tr>
                <?php
                    }

                } else {
                ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            No inquiries found.
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
