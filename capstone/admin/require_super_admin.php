<?php
require_once __DIR__ . '/require_admin_access.php';

$checkSuperAdmin = strtolower(trim($authenticatedAdminRole));

if ($checkSuperAdmin !== 'super admin') {
    echo "<script>
            alert('You are not authorized to access this page!');
            window.location='../index.php';
          </script>";
    exit();
}
?>