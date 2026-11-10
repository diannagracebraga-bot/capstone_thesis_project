<?php
require_once '../admin/check_admin_access.php';

http_response_code(403);
header('Content-Type: text/plain; charset=UTF-8');
echo 'Payment records are retained and cannot be deleted.';
?>
