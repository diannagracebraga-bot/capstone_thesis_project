<?php
require_once __DIR__ . '/require_admin_access.php';
include __DIR__ . '/admin_sidebar_header_profile.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
    <div class="card shadow-sm mx-auto" style="max-width: 1100px;">
        <div class="card-body">
            <?php include __DIR__ . '/admin_add_customer.php'; ?>
        </div>
    </div>
</main>
