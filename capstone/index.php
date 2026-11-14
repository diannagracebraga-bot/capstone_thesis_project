<?php include 'database/database_connection.php';

session_start();
 $planStatusColumn = mysqli_query($conn, "SHOW COLUMNS FROM internet_plan_tbl LIKE 'is_active'");
if ($planStatusColumn && mysqli_num_rows($planStatusColumn) === 0) {
    mysqli_query($conn, 'ALTER TABLE internet_plan_tbl ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1');
}
// Internet Plans
$plan_sql = "SELECT * FROM internet_plan_tbl WHERE is_active = 1 ORDER BY plan_id ASC";
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
    <link rel="icon" type="image/png" href="/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/index_style.css?v=hero-balance-6">
    <link rel="stylesheet" href="css/inquire.css?v=tablet-spacing-2">
    <link rel="stylesheet" href="css/plan.css?v=plans-mobile-4">
    <link rel="stylesheet" href="css/about.css?v=footer-spacing-5">
    <link rel="stylesheet" href="css/apply_internet.css?v=privacy-modal-logo-3">
    <link rel="stylesheet" href="css/login.css?v=login-ui-2">
    <link rel="stylesheet" href="css/index-redesign.css?v=12">
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
<main class="homepage-redesign">
    <section id="home" class="home-hero" aria-label="MITZTIANPC internet services">
        <div class="home-hero-copy">
            <h1>Fast &amp; Reliable Internet?<br><span>Switch na sa MPC Bagtas!!</span></h1>
            <a class="home-hero-plans-link" href="#plan">View Plans <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="home-hero-image-wrap">
            <img class="home-hero-image" src="images/homepage-router.jpg" alt="Home Wi-Fi router and connected devices">
            <div class="hero-image-services">
                <div class="service-highlights" aria-label="Service highlights infographic">
                    <div class="service-highlights-heading"><span></span>THE MPC ADVANTAGE</div>
                    <div class="service-highlight service-highlight-primary"><i class="bi bi-tools" aria-hidden="true"></i><span class="service-highlight-copy"><strong>Free Installation</strong><small>No extra installation fees.</small></span></div>
                    <div class="service-highlight service-highlight-quick"><i class="bi bi-receipt" aria-hidden="true"></i><span class="service-highlight-copy"><strong>No hidden charges</strong><small>Transparent and affordable pricing.</small></span></div>
                    <div class="service-highlight service-highlight-quick"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i><span class="service-highlight-copy"><strong>Kabit Agad</strong><small>Quick and hassle-free connection.</small></span></div>
                    <div class="service-highlight service-highlight-wide"><i class="bi bi-unlock-fill" aria-hidden="true"></i><span class="service-highlight-copy"><strong>No lock-in period</strong><small>Flexible internet subscription.</small></span></div>
                </div>
            </div>
        </div>
    </section>

    <section id="plan" class="plans-section" aria-labelledby="plans-title">
        <div class="section-heading">
            <p class="section-kicker">CHOOSE THE RIGHT CONNECTION</p>
            <h2 id="plans-title">Explore Our Internet Plans</h2>
            <p>Simple plan options for your home and everyday needs.</p>
        </div>
        <div class="plans-carousel">
            <section class="plans" id="plansTrack" tabindex="0" aria-label="Internet plans">
                <?php while ($plan = mysqli_fetch_assoc($plan_result)) { ?>
                    <article class="plan-card">
                        <h3 class="plan-name"><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                        <p class="plan-speed"><strong><?php echo htmlspecialchars($plan['internet_mbps']); ?> Mbps</strong><span>Up to</span></p>
                        <p class="plan-price"><span>For only</span><strong>₱<?php echo number_format((float) $plan['internet_price']); ?></strong><span>per month</span></p>
                        <p class="plan-inclusion"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Free installation and activation</p>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#applicationModal">Apply Now</button>
                    </article>
                <?php } ?>
            </section>
        </div>
    </section>

    <section id="inquire" class="inquiry-section" aria-labelledby="inquiry-title">
        <div class="inquiry-prompt">
            <img class="inquiry-watermark" src="images/bg_logo.png" alt="" aria-hidden="true">
            <p class="section-kicker">LET'S GET YOU CONNECTED</p>
            <h2>Are you interested in partnering with us?</h2>
            <p>Send us an inquiry so we can help you kickstart your journey.</p>
        </div>
        <div class="form inquiry-card">
            <div class="inquiry-card-heading">
                <i class="bi bi-send" aria-hidden="true"></i>
                <h2 id="inquiry-title">Inquire</h2>
            </div>
            <div class="inquiry-card-body">
                <p class="inquiry-intro">Fill out the form below and we'll get back to you as soon as possible.</p>
                <form action="php/submit_inquiry.php" method="POST">
                    <div class="inquiry-field">
                        <label for="name"><i class="bi bi-person" aria-hidden="true"></i><span>Full Name</span></label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" autocomplete="name" required>
                    </div>
                    <div class="inquiry-field">
                        <label for="email"><i class="bi bi-envelope" aria-hidden="true"></i><span>Email Address</span></label>
                        <input type="email" id="email" name="email" placeholder="Enter your email address" autocomplete="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|ph|net|org|edu|gov)" title="Enter an email ending in .com, .ph, .net, .org, .edu, or .gov." required>
                    </div>
                    <div class="inquiry-field">
                        <label for="contact"><i class="bi bi-telephone-fill" aria-hidden="true"></i><span>Contact Number</span></label>
                        <input type="tel" id="contact" name="contact" placeholder="Enter your contact number" autocomplete="tel" maxlength="11" pattern="09[0-9]{9}" title="Please enter exactly 11 digits starting with 09." required>
                    </div>
                    <div class="inquiry-field inquiry-field-description">
                        <label for="description"><i class="bi bi-file-earmark-text" aria-hidden="true"></i><span>Description</span></label>
                        <textarea id="description" name="description" rows="3" placeholder="Tell us about your inquiry..." required></textarea>
                    </div>
                    <section class="privacy-notice inquiry-privacy-notice" aria-label="Privacy Policy acknowledgement">
                        <label class="privacy-acknowledgement" for="inquiry_privacy_acknowledged">
                            <input type="checkbox" id="inquiry_privacy_acknowledged" name="privacy_notice_acknowledged" value="1" required>
                            <span>I agree to the <a class="privacy-policy-link" href="front_page_menus/privacy_statement.php" data-open-index-privacy aria-haspopup="dialog">Privacy Policy</a>.</span>
                        </label>
                    </section>
                    <button class="inquiry-submit" type="submit"><i class="bi bi-send" aria-hidden="true"></i><span>Submit</span></button>
                </form>
            </div>
        </div>
        <img class="inquiry-background-image" src="images/homepage-inquiry.jpg" alt="Person working on a laptop">
    </section>
</main>

<div class="privacy-statement-overlay" data-index-privacy-modal aria-hidden="true">
    <section class="privacy-statement-dialog" role="dialog" aria-modal="true" aria-label="Privacy Statement" tabindex="-1">
        <header class="privacy-statement-modal-header">
            <img class="privacy-statement-modal-logo" src="images/bg_logo.png" alt="MITZTIANPC Wired Internet Services">
            <button type="button" class="privacy-statement-close" data-close-index-privacy aria-label="Close Privacy Statement">&times;</button>
        </header>
        <iframe src="front_page_menus/privacy_statement.php?embed=1" title="MitztianPC Privacy Statement" loading="lazy"></iframe>
    </section>
</div>

<div class="modal fade" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="application-page">
                <?php
               $applicationAction = 'crud/submit_application.php';
include 'front_page_menus/application_form_fields.php';
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<section id="about" class="site-footer-section">
<footer class="site-footer">
    <div class="container">
        <div class="row footer-main-row">
            <div class="col-sm-12 col-md-7 footer-about-column">
                <h6>About Us</h6>

                <p class="text-justify">
                    <strong><?php echo $about['business_name']; ?></strong><span> – ALWAYS CONNECTED</span><br><br>

                    Quality products help to maintain customer satisfaction and loyalty and reduce the risk and cost of replacing faulty goods.
                </p>
            </div>

            <div class="col-xs-6 col-md-4 footer-contact-column">
                <h6 class="contact-title">Contact Us</h6>
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

    <a class="contact-item footer-facebook-link" href="https://www.facebook.com/profile.php?id=100084288545449" target="_blank" rel="noopener noreferrer" aria-label="Visit MitztianPC on Facebook">
        <i class="bi bi-facebook" aria-hidden="true"></i>
        <span>mpcwiredinternetservice</span>
    </a>

</div>
            </div>
        </div>
        <hr>
    </div>

    <div class="container">
        <div class="row footer-copyright-row">
            <div class="copyright-text">
                <span>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($about['business_name'] ?? 'MITZTIANPC Wired Internet Services'); ?>. All Rights Reserved.</span>
                <nav class="footer-legal-links" aria-label="Legal links">
                    <a href="front_page_menus/privacy_statement.php">Privacy Policy</a>
                </nav>
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
// Reopen the homepage application modal when returning from the Privacy Statement.
document.addEventListener("DOMContentLoaded", function () {
    var params = new URLSearchParams(window.location.search);
    var applicationModal = document.getElementById("applicationModal");
    if (params.get("openApplication") !== "1" || !applicationModal) return;

    if (window.bootstrap && window.bootstrap.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(applicationModal).show();
        return;
    }

    var applyButton = document.querySelector('[data-bs-target="#applicationModal"]');
    if (applyButton) applyButton.click();
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
    link.addEventListener("click", function () {
        var menu = document.getElementById("offcanvasNavbar");
        if (menu && window.bootstrap && window.bootstrap.Offcanvas) {
            window.bootstrap.Offcanvas.getOrCreateInstance(menu).hide();
        }

        if (link.hasAttribute("data-bs-toggle")) return;

        document.querySelectorAll(".site-nav .navbar-nav .nav-link").forEach(function (item) {
            item.classList.remove("active");
            item.removeAttribute("aria-current");
        });

        link.classList.add("active");
        link.setAttribute("aria-current", "page");
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var privacyModal = document.querySelector("[data-index-privacy-modal]");
    var privacyLink = document.querySelector("[data-open-index-privacy]");
    if (!privacyModal || !privacyLink) return;

    var closeButton = privacyModal.querySelector("[data-close-index-privacy]");
    var previousFocus = null;

    function closePrivacyModal() {
        privacyModal.classList.remove("is-open");
        privacyModal.setAttribute("aria-hidden", "true");
        if (previousFocus) previousFocus.focus();
    }

    privacyLink.addEventListener("click", function (event) {
        event.preventDefault();
        previousFocus = document.activeElement;
        privacyModal.classList.add("is-open");
        privacyModal.setAttribute("aria-hidden", "false");
        if (closeButton) closeButton.focus();
    });

    if (closeButton) closeButton.addEventListener("click", closePrivacyModal);
    privacyModal.addEventListener("click", function (event) {
        if (event.target === privacyModal) closePrivacyModal();
    });
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && privacyModal.classList.contains("is-open")) {
            closePrivacyModal();
        }
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var track = document.getElementById("plansTrack");
    if (!track) return;

    var controls = document.querySelectorAll("[data-plans-direction]");
    function updatePlanControls() {
        var maxScroll = track.scrollWidth - track.clientWidth;
        controls.forEach(function (control) {
            var direction = Number(control.dataset.plansDirection);
            control.disabled = maxScroll <= 1 || (direction < 0 && track.scrollLeft <= 1) || (direction > 0 && track.scrollLeft >= maxScroll - 1);
        });
    }

    controls.forEach(function (control) {
        control.addEventListener("click", function () {
            track.scrollBy({ left: Number(control.dataset.plansDirection) * track.clientWidth * 0.85, behavior: "smooth" });
        });
    });
    track.addEventListener("scroll", updatePlanControls, { passive: true });
    track.addEventListener("keydown", function (event) {
        if (event.key === "ArrowRight" || event.key === "ArrowLeft") {
            event.preventDefault();
            track.scrollBy({ left: (event.key === "ArrowRight" ? 1 : -1) * track.clientWidth * 0.85, behavior: "smooth" });
        }
    });
    window.addEventListener("resize", updatePlanControls);
    updatePlanControls();
});
</script>
</body>
</html>



