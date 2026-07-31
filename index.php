<?php include 'database/database_connection.php';
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
    <link rel="stylesheet" href="css/index_style.css">
    <link rel="stylesheet" href="css/inquire.css">
    <link rel="stylesheet" href="css/plan.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

<nav class="navbar bg-body-tertiary fixed-top">
    <div class="container-fluid">

        <a class="navbar-brand" href="#home">
            MITZTIANPC WIRED INTERNET SERVICES
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">

            <div class="offcanvas-header">
                <h5 class="offcanvas-title">MENU</h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="offcanvas">
                </button>
            </div>

            <div class="offcanvas-body">

                <div class="navbar-nav">
                    <a class="nav-link" href="#home">Home</a>
                    <a class="nav-link" href="#inquire">Inquire</a>
                    <a class="nav-link" href="#plan">Plan</a>
                    <a  class="nav-link"href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
    Login
</a>
                    <a class="nav-link" href="#about">About</a>
                </div>

            </div>

        </div>

    </div>
</nav>
<section id="home">
<br>
    <h1 class="center_name">
        MITZTIANPC WIRED INTERNET
    </h1>

    <h6 class="center_lower_name">
        NOW YOU'RE CONNECTED
    </h6>

    <div class="image-container">
    <img src="images/bg_logo.png" alt="Logo" class="img-fluid">
</div>
</section>

<section id="inquire">

    <div class="form">
        <h3>Inquire</h3>

        <form action="php/submit_inquiry.php" method="POST">

            <label for="name">Full Name:</label><br>
            <input type="text" id="name" name="name" placeholder="Full Name" required><br><br>

            <label for="email">Email Address:</label><br>
            <input type="email" id="email" name="email" placeholder="Email Address" required><br><br>

            <label for="contact">Contact Number:</label><br>
            <input type="text" id="contact" name="contact" placeholder="Contact Number" required><br><br>

            <label for="description">Description:</label><br>
            <textarea id="description"
                      name="description"
                      rows="4"
                      placeholder="Description"
                      required></textarea><br><br>

            <button type="submit">Submit</button>
        </form>
    </div>
</section>

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
                     <a href="front_page_menus/apply_internet.php"> <button>APPLY NOW</button> </a>
              </div>
        <?php } ?>
    </section>

</section>
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

<div class="modal fade" id="loginModal" tabindex="-1" >
    <div class="modal-dialog modal-dialog-centered  modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="login-container">
                    <h2>Login</h2>
                    <form action="database/login.php" method="POST">
                        <label for="username">Username:</label>
                        <input type="text"
                               id="username"
                               name="email"
                               class="form-control"
                               placeholder="Username"
                               required>
                        <br>
                        <label for="password">Password:</label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control"
                               placeholder="Password"
                               required>
                        <br>
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

</body>
</html>

