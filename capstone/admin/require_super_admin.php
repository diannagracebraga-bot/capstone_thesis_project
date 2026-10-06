<?php
require_once __DIR__ . '/require_admin_access.php';

if (strcasecmp($authenticatedAdminRole, 'Super Admin') !== 0) {
    http_response_code(403);
    exit('Access denied. Super Admin access is required.');
}
