
<nav class="navbar navbar-expand-lg navbar-dark ">
    <div class="container-fluid">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>  </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="dropdown ms-auto">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">

                    <img src="" width="30" height="30"
                      class="rounded-circle me-2">    <?php echo $customer['f_name'] . ' ' . $customer['m_name'] . ' ' . $customer['l_name']; ?> </button>

                <div class="dropdown-menu dropdown-menu-end">
                    <div>
                        <a class="dropdown-item text-danger" href="../database/logout.php">
                            Logout
                        </a>
</div>
</div>
            </div>
        </div>
    </div>
</nav>

        
<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<div class="sidebar">
    <div class="logo">
        <img src="../images/bg_logo.png" alt="Logo" class="icon2">
    </div>
    <a class="dashboard <?php echo $current_page === 'customer-dashboard.php' ? 'active' : ''; ?>" href="customer-dashboard.php" <?php echo $current_page === 'customer-dashboard.php' ? 'aria-current="page"' : ''; ?>>Dashboard</a><br>
    <a class="dashboard <?php echo $current_page === 'customer_support.php' ? 'active' : ''; ?>" href="customer_support.php" <?php echo $current_page === 'customer_support.php' ? 'aria-current="page"' : ''; ?>>Support</a><br>
    <a class="dashboard <?php echo $current_page === 'customer_ticket.php' ? 'active' : ''; ?>" href="customer_ticket.php" <?php echo $current_page === 'customer_ticket.php' ? 'aria-current="page"' : ''; ?>>Ticket</a><br>
    <a class="dashboard <?php echo $current_page === 'customer_profile.php' ? 'active' : ''; ?>" href="customer_profile.php" <?php echo $current_page === 'customer_profile.php' ? 'aria-current="page"' : ''; ?>>Profile</a><br>
</div>
   <script>
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.querySelector('.navbar-toggler');
    var sidebar = document.querySelector('.sidebar');
    if (!toggle || !sidebar) return;
    toggle.addEventListener('click', function () {
        if (window.matchMedia('(max-width: 1024px)').matches || navigator.maxTouchPoints > 0) {
            sidebar.classList.toggle('mobile-open');
        }
    });
    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () { sidebar.classList.remove('mobile-open'); });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>