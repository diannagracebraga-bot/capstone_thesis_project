<?php
session_start();
session_unset();
session_destroy();

 echo "<script>
            alert('Your account has been log out!');
            window.location='../index.php';
          </script>";

?>