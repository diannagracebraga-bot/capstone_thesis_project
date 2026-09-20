<?php 
include '../database/database_connection.php'; 
 
if (!isset($_GET['inquiries_id'])) { 
    header("Location: ../admin/admin_inquiries.php"); 
    exit(); 
} 
 
$inquiries_id = intval($_GET['inquiries_id']); 
 
$query = "DELETE FROM inquiries_tbl WHERE inquiries_id = $inquiries_id"; 
 
if (mysqli_query($conn, $query)) { 
    echo "<script> 
        alert('Inquiry deleted successfully'); 
        window.location.href='../admin/admin_inquiries.php'; 
    </script>"; 
} else { 
    echo "Error deleting record: " . mysqli_error($conn); 
} 
?>