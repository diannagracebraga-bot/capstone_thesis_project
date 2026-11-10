<?php
session_start();
if (!isset($_SESSION['account_id'])) {
    echo "<script>
            alert('You are not authorized to access this page!');
            window.location='../index.php';
          </script>";
    exit();
}
if ($_SESSION['role'] != "Admin" && $_SESSION['role'] != "Super Admin") {
    echo "<script>
            alert('You are not authorized to access this page!');
            window.location='../index.php';
          </script>";
    exit();
}
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>