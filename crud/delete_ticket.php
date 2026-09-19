<?php 
include '../database/database_connection.php'; 
 
if (!isset($_GET['ticket_id'])) { 
    header("Location: ../admin/admin_ticket_management.php"); 
    exit(); 
} 
 
$ticket_id = intval($_GET['ticket_id']); 
 
$query = "DELETE FROM ticket_management_tbl WHERE ticket_id = $ticket_id"; 
 
if (mysqli_query($conn, $query)) { 
    echo "<script> 
        alert('Record deleted successfully'); 
        window.location.href='../admin/admin_ticket_management.php'; 
    </script>"; 
} else { 
    echo "Error deleting record: " . mysqli_error($conn); 
} 
?>