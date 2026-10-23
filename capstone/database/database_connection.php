<?php
$conn = mysqli_connect("localhost", "root", "", "mitztianpc_wired_internet_services");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>