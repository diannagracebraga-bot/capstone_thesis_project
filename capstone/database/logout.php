<?php
session_start();

session_unset();
session_destroy();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

 echo "<script>
            alert('Your account has been log out!');
            window.location='../index.php';
          </script>";
exit();
?>