
<?php
require_once __DIR__ . '/require_admin_access.php';

$checkSuperAdmin = strtolower(trim($authenticatedAdminRole));

if ($checkSuperAdmin !== 'super admin') {
    http_response_code(403);
    exit('Access denied. Super Admin access is required.');
}
?>
