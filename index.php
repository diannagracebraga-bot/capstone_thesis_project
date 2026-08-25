<?php include 'database/database_connection.php';
// Internet Plans
$plan_sql = "SELECT * FROM internet_plan_tbl ORDER BY plan_id ASC";
$plan_result = mysqli_query($conn, $plan_sql);

// Content Management
$about_sql = "SELECT * FROM content_management_about_tbl WHERE about_id = 1";
$about_result = mysqli_query($conn, $about_sql);
$about = mysqli_fetch_assoc($about_result);

// Chatbot plan data
$chatbot_plans = [];

$chatbot_plan_sql = "
    SELECT plan_name, internet_mbps, internet_price
    FROM internet_plan_tbl
    ORDER BY plan_id ASC
";

$chatbot_plan_result = mysqli_query($conn, $chatbot_plan_sql);

while ($chatbot_plan = mysqli_fetch_assoc($chatbot_plan_result)) {
    $chatbot_plans[] = [
        'name' => $chatbot_plan['plan_name'],
        'mbps' => $chatbot_plan['internet_mbps'],
        'price' => number_format($chatbot_plan['internet_price'])
    ];
}

$chatbot_data = [
    'business_name' => $about['business_name'],
    'email' => $about['business_email'],
    'contact' => $about['business_contact'],
    'address' => $about['business_address'],
    'plans' => $chatbot_plans
];
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

<style>
#faqChatButton {
    position: fixed;
    right: 25px;
    bottom: 25px;
    width: 60px;
    height: 60px;
    border: none;
    border-radius: 50%;
    background: white;
    color: white;
    font-size: 25px;
    cursor: pointer;
    z-index: 9999;

    animation: chatbotFloat 2s ease-in-out infinite;
}

@keyframes chatbotFloat {
    0%, 100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-12px);
    }
}

#faqChatButton:hover {
    animation-play-state: paused;
}
#faqChatBox {
    display: none;
    position: fixed;
    right: 25px;
    bottom: 95px;
    width: 420px;
    height: 560px;
    max-width: calc(100% - 30px);
    background: white;
    border-radius: 14px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.3);
    overflow: hidden;
    z-index: 9999;

    flex-direction: column;
}

#faqChatHeader {
    flex-shrink: 0;
    background: #0d6efd;
    color: white;
    padding: 18px;
    font-size: 18px;
    font-weight: bold;
}

#faqChatMessages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f4f6f8;
}

#faqChatForm {
    flex-shrink: 0;
    display: flex;
    padding: 12px;
    gap: 8px;
    background: white;
    border-top: 1px solid #ddd;
}

#faqChatInput {
    flex: 1;
    min-width: 0;
    border: 1px solid #bbb;
    border-radius: 7px;
    padding: 11px;
    font-size: 14px;
}

#faqChatForm button {
    border: none;
    background: #0d6efd;
    color: white;
    border-radius: 7px;
    padding: 10px 16px;
    cursor: pointer;
}

.chat-message {
    display: block;
    width: fit-content;
    max-width: 85%;
    margin-bottom: 12px;
    padding: 10px 13px;
    border-radius: 12px;
    white-space: pre-line;
    overflow-wrap: anywhere;
}

.bot-message {
    background: white;
    color: #222;
    margin-right: auto;
}

.user-message {
    background: #0d6efd;
    color: white;
    margin-left: auto;
}
</style>

<button id="faqChatButton" type="button">💬</button>

<div id="faqChatBox">
    <div id="faqChatHeader">
        Mitztianpc Wired Internet Services
    </div>

    <div id="faqChatMessages">
        <div class="chat-message bot-message">
            Hello! How may I help you today ?
        <div class="chat-message bot-message">
            Hello! Ask me about our plans, prices, application process, contact details, or support.
        </div>
    </div>
</div>
    <form id="faqChatForm">
        <input
            type="text"
            id="faqChatInput"
            placeholder="Type your question..."
            autocomplete="off"
            required
        >
        <button type="submit">Send</button>
    </form>


<script>
const chatbotData = <?= json_encode(
    $chatbot_data,
    JSON_UNESCAPED_UNICODE |
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_AMP |
    JSON_HEX_QUOT
); ?>;

const chatButton = document.getElementById('faqChatButton');
const chatBox = document.getElementById('faqChatBox');
const chatForm = document.getElementById('faqChatForm');
const chatInput = document.getElementById('faqChatInput');
const chatMessages = document.getElementById('faqChatMessages');

chatButton.addEventListener('click', function () {
    chatBox.style.display = chatBox.style.display === 'flex'
        ? 'none'
        : 'flex';
});

function addChatMessage(message, type) {
    const messageElement = document.createElement('div');
    messageElement.className = 'chat-message ' + type;
    messageElement.textContent = message;

    chatMessages.appendChild(messageElement);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function getChatbotAnswer(question) {
    const text = question.toLowerCase();

    if (
        text.includes('plan') ||
        text.includes('price') ||
        text.includes('speed') ||
        text.includes('mbps') ||
        text.includes('internet')
    ) {
        if (chatbotData.plans.length === 0) {
            return 'Our internet plans are currently unavailable. Please contact us for assistance.';
        }

        return 'Our available plans are:\n\n' +
            chatbotData.plans.map(function (plan) {
                return plan.name + ' - ' +
                    plan.mbps + ' Mbps - ₱' +
                    plan.price + ' per month';
            }).join('\n');
    }

    if (
        text.includes('apply') ||
        text.includes('application') ||
        text.includes('install') ||
        text.includes('subscribe')
    ) {
        return 'To apply for an internet connection, select a plan and click the "APPLY NOW" button. You may also submit an inquiry through our Inquire section.';
    }

    if (
        text.includes('contact') ||
        text.includes('email') ||
        text.includes('phone') ||
        text.includes('number')
    ) {
        return 'You can contact us through:\nEmail: ' +
            chatbotData.email +
            '\nContact: ' +
            chatbotData.contact;
    }

    if (
        text.includes('address') ||
        text.includes('location') ||
        text.includes('where')
    ) {
        return 'Our address is:\n' + chatbotData.address;
    }

    if (
        text.includes('support') ||
        text.includes('problem') ||
        text.includes('connection') ||
        text.includes('internet is down')
    ) {
        return 'Please submit an inquiry with your full name, email address, contact number, and a description of the problem.';
    }

    if (
        text.includes('hello') ||
        text.includes('hi') ||
        text.includes('hey')
    ) {
        return 'Hello! How can I help you today? You can ask about plans, prices, applications, contact details, or support.';
    }

    return 'Sorry, I do not have an answer for that yet. Please ask about our plans, prices, application process, contact details, or support.';
}

chatForm.addEventListener('submit', function (event) {
    event.preventDefault();

    const question = chatInput.value.trim();

    if (question === '') {
        return;
    }

    addChatMessage(question, 'user-message');

    const answer = getChatbotAnswer(question);

    setTimeout(function () {
        addChatMessage(answer, 'bot-message');
    }, 300);

    chatInput.value = '';
});
</script>

</body>
</html>

