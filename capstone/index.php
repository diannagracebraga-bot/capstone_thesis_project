<?php include 'database/database_connection.php';

session_start();
// Internet Plans
$plan_sql = "SELECT * FROM internet_plan_tbl ORDER BY plan_id ASC";
$plan_result = mysqli_query($conn, $plan_sql);

// Content Management
$about_sql = "SELECT * FROM content_management_about_tbl WHERE about_id = 1";
$about_result = mysqli_query($conn, $about_sql);
$about = mysqli_fetch_assoc($about_result);


?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/index_style.css?v=landing-fit-3">
    <link rel="stylesheet" href="css/inquire.css?v=landing-ref-1">
    <link rel="stylesheet" href="css/plan.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/apply_internet.css?v=modal-form-1">
    <link rel="stylesheet" href="css/login.css?v=login-ui-2">
</head>

<body>

<nav class="navbar navbar-expand-lg site-nav fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand site-brand" href="#home" aria-label="MITZTIANPC home">
            <img src="images/bg_logo.png" alt="MITZTIANPC logo">
            <span class="site-brand-copy">
                <strong>MITZTIANPC</strong>
                <small>WIRED INTERNET SERVICES</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Open navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-lg offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">MENU</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="#home">Home</a>
                    <a class="nav-link" href="#inquire">Inquire</a>
                    <a class="nav-link" href="#plan">Plan</a>
                    <a class="nav-link" href="#login" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                    <a class="nav-link" href="#about">About</a>
                </div>
            </div>
        </div>
    </div>
</nav>
<main class="landing-split">
    <section id="home" class="brand-hero" aria-label="MITZTIANPC internet services">
        <img class="hero-logo" src="images/bg_logo.png" alt="MITZTIANPC wired internet services logo">
        <h1 class="hero-wordmark">MITZTIANPC</h1>
        <p class="hero-submark">WIRED INTERNET SERVICES</p>
        <span class="hero-rule" aria-hidden="true"></span>
        <p class="hero-tagline">Fast Connection. Better Tomorrow.</p>

        <div class="service-highlights" aria-label="Service highlights">
            <div class="service-highlight">
                <span class="service-icon"><i class="bi bi-wifi" aria-hidden="true"></i></span>
                <span>Reliable</span>
            </div>
            <div class="service-highlight">
                <span class="service-icon"><i class="bi bi-shield-check" aria-hidden="true"></i></span>
                <span>Secure</span>
            </div>
            <div class="service-highlight">
                <span class="service-icon"><i class="bi bi-globe2" aria-hidden="true"></i></span>
                <span>Always Connected</span>
            </div>
        </div>
    </section>

    <section id="inquire" class="inquiry-section" aria-labelledby="inquiry-title">
        <div class="form inquiry-card">
            <div class="inquiry-card-heading">
                <i class="bi bi-send" aria-hidden="true"></i>
                <h2 id="inquiry-title">Inquire</h2>
            </div>
            <div class="inquiry-card-body">
                <p class="inquiry-intro">Fill out the form below and we'll get back to you<br class="desktop-break"> as soon as possible.</p>
                <form action="php/submit_inquiry.php" method="POST">
                    <div class="inquiry-field">
                        <label for="name"><i class="bi bi-person" aria-hidden="true"></i><span>Full Name</span></label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" autocomplete="name" required>
                    </div>
                    <div class="inquiry-field">
                        <label for="email"><i class="bi bi-envelope" aria-hidden="true"></i><span>Email Address</span></label>
                        <input type="email" id="email" name="email" placeholder="Enter your email address" autocomplete="email" required>
                    </div>
                    <div class="inquiry-field">
                        <label for="contact"><i class="bi bi-telephone-fill" aria-hidden="true"></i><span>Contact Number</span></label>
                        <input type="text" id="contact" name="contact" placeholder="Enter your contact number" autocomplete="tel" required>
                    </div>
                    <div class="inquiry-field inquiry-field-description">
                        <label for="description"><i class="bi bi-file-earmark-text" aria-hidden="true"></i><span>Description</span></label>
                        <textarea id="description" name="description" rows="3" placeholder="Tell us about your inquiry..." required></textarea>
                    </div>
                    <button class="inquiry-submit" type="submit"><i class="bi bi-send" aria-hidden="true"></i><span>Submit</span></button>
                </form>
            </div>
        </div>
    </section>
</main>

<section id="plan">
    <section class="plans">

        <?php while ($plan = mysqli_fetch_assoc($plan_result)) { ?>

            <div class="plan-card">
                
            <h1 class="plan-name">
                <?php echo $plan['plan_name'];?>
            </h1>
                <h3><?php echo $plan['internet_mbps']; ?> Mbps</h3>
                <h2>
                    ₱<?php echo number_format($plan['internet_price']); ?> / Month </h2>
                     <button type="button" data-bs-toggle="modal" data-bs-target="#applicationModal">APPLY NOW</button>
              </div>
        <?php } ?>
    </section>

</section>

<div class="modal fade" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="application-page">
                <?php
                $applicationAction = 'admin/admin_applicants.php';
                include 'front_page_menus/application_form_fields.php';
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<section id="about">
<footer>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <h6>About</h6>

                <p class="text-justify">
                    <strong><?php echo $about['business_name']; ?></strong>
                    <i> ALWAYS CONNECTED </i><br><br>

                    <?php echo $about['business_description']; ?>
                </p>
            </div>

            <div class="col-xs-6 col-md-3">
                <h6 class = "contact-title">Contact</h6>
<div class="footer-links">

    <div class="contact-item">
             <img src="images/mail.png" alt="mail">
        <span><?php echo $about['business_email']; ?></span>
    </div>

    <div class="contact-item">
         <img src="images/phone.png" alt="phone">
        <span><?php echo $about['business_contact']; ?></span>
    </div>

    <div class="contact-item">
        <img src="images/location.png" alt="location">
        <span><?php echo $about['business_address']; ?></span>
    </div>

</div>
            </div>
        </div>
        <hr>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-sm-6 col-xs-12">

                    <a href="#">
                        <?php echo $about['business_name']; ?>
                    </a>.   All Rights Reserved.
            </div>
            </div>
        </div>
    </div>
</footer>
</section>

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="login-container">
                    <h2 id="loginModalTitle">Login</h2>
                    <form action="database/login.php" method="POST">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="email" class="form-control"
                               placeholder="Username" required>
                      <label for="password">Password:</label>

                <div class="password-input">
                     <input type="password" id="password" name="password" class="form-control"
                         placeholder="Password" required>

                     <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password" aria-pressed="false">
                            <i class="bi bi-eye"></i>
                     </button>
                </div>
                        <div class="forgot-password">
                            <a href="forgot_password.php">
                                <i>Forgot Password?</i>
                            </a>
                        </div>

                    <button type="submit" name="login" class="btn btn-primary">
    Login
</button>

                    </form>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Keep Apply Now usable even when the remote Bootstrap script is unavailable.
document.addEventListener("DOMContentLoaded", function () {
    var applicationModal = document.getElementById("applicationModal");
    if (!applicationModal || window.bootstrap) return;

    function closeApplicationModal() {
        applicationModal.classList.remove("show");
        applicationModal.style.display = "none";
        applicationModal.setAttribute("aria-hidden", "true");
        document.body.classList.remove("modal-open");
        document.body.classList.remove("application-modal-fallback");
        document.body.style.removeProperty("overflow");
    }

    document.querySelectorAll('[data-bs-target="#applicationModal"]').forEach(function (button) {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            applicationModal.style.display = "block";
            applicationModal.classList.add("show");
            applicationModal.removeAttribute("aria-hidden");
            document.body.classList.add("modal-open");
            document.body.classList.add("application-modal-fallback");
            document.body.style.overflow = "hidden";
        });
    });

    applicationModal.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function (button) {
        button.addEventListener("click", closeApplicationModal);
    });
    applicationModal.addEventListener("click", function (event) {
        if (event.target === applicationModal) closeApplicationModal();
    });
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && applicationModal.classList.contains("show")) closeApplicationModal();
    });
});
</script>

<script>
const togglePassword = document.getElementById("togglePassword");
const password = document.getElementById("password");
const eyeIcon = togglePassword.querySelector("i");

togglePassword.addEventListener("click", function () {

    if (password.type === "password") {

        password.type = "text";
        togglePassword.setAttribute("aria-label", "Hide password");
        togglePassword.setAttribute("aria-pressed", "true");

        eyeIcon.classList.remove("bi-eye");
        eyeIcon.classList.add("bi-eye-slash");

    } else {

        password.type = "password";
        togglePassword.setAttribute("aria-label", "Show password");
        togglePassword.setAttribute("aria-pressed", "false");

        eyeIcon.classList.remove("bi-eye-slash");
        eyeIcon.classList.add("bi-eye");
    }
});
</script>
</script>


<script type="module" src="https://cdn.landbot.io/landbot-3/landbot-3.0.0.mjs"></script>
<script type="module">
  var myLandbot = new Landbot.Livechat({
    configUrl: 'https://storage.googleapis.com/landbot.online/v3/H-3515005-HNL32TAA5X8KFTRE/index.json',
  });
</script>

<script>
document.querySelectorAll(".site-nav .navbar-nav .nav-link[href^='#']").forEach(function (link) {
    if (link.hasAttribute("data-bs-toggle")) return;

    link.addEventListener("click", function () {
        document.querySelectorAll(".site-nav .navbar-nav .nav-link").forEach(function (item) {
            item.classList.remove("active");
            item.removeAttribute("aria-current");
        });

        link.classList.add("active");
        link.setAttribute("aria-current", "page");
    });
});
</script>
</body>
</html>



