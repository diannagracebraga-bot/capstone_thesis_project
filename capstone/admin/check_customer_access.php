<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != "customer") {
    echo "<script>
            alert('You are not authorized to access this page!');
            window.location='../index.php';
          </script>";
    exit();
}
?>