<?php
require_once __DIR__ . '/../database/database_connection.php';
require_once __DIR__ . '/../database/privacy_statement_storage.php';
$privacy = getPrivacyStatement($conn);
$privacyText = static function (string $text): string {
    return nl2br(privacyStatementEscape($text), false);
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Statement | MitztianPC Wired Internet Service</title>
      <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link href="../bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/apply_internet.css?v=privacy-page-2">
</head>
<body class="privacy-document-page<?php echo isset($_GET['embed']) ? ' privacy-embedded-page' : ''; ?>">
    <header class="privacy-document-header">
        <a class="privacy-document-brand" href="../index.php">
            <img src="../images/bg_logo.png" alt="">
            <span>MITZTIANPC WIRED INTERNET SERVICES</span>
        </a>
        <a class="privacy-home-link" href="../index.php">Home</a>
    </header>

    <main class="privacy-document">
        <h1>Privacy Statement</h1>
        <p class="privacy-organization"><?php echo privacyStatementEscape($privacy['organization']); ?></p>
        <p class="privacy-effective-date"><strong>Effective Date:</strong> <?php echo privacyStatementEscape($privacy['effective_date']); ?></p>

        <section>
            <h2>1. Introduction</h2>
            <p><?php echo $privacyText($privacy['section_1']); ?></p>
        </section>

        <section>
            <h2>2. Information We Collect</h2>
            <p><?php echo $privacyText($privacy['section_2']); ?></p>
        </section>

        <section>
            <h2>3. Purpose of Data Collection</h2>
            <p><?php echo $privacyText($privacy['section_3']); ?></p>
        </section>

        <section>
            <h2>4. Payment Processing</h2>
            <p><?php echo $privacyText($privacy['section_4']); ?></p>
        </section>

        <section>
            <h2>5. Data Sharing and Disclosure</h2>
            <p><?php echo $privacyText($privacy['section_5']); ?></p>
        </section>

        <section>
            <h2>6. Data Storage and Security</h2>
            <p><?php echo $privacyText($privacy['section_6']); ?></p>
        </section>

        <section>
            <h2>7. Data Retention</h2>
            <p><?php echo $privacyText($privacy['section_7']); ?></p>
        </section>

        <section>
            <h2>8. Your Data Privacy Rights</h2>
            <p><?php echo $privacyText($privacy['section_8']); ?></p>
        </section>

        <section>
            <h2>9. Changes to This Privacy Statement</h2>
            <p><?php echo $privacyText($privacy['section_9']); ?></p>
        </section>

        <section>
            <h2>10. Contact Information</h2>
            <p>For questions, concerns, or requests regarding your personal information, you may contact:</p>
            <ul>
                <li><strong>Organization:</strong> <?php echo privacyStatementEscape($privacy['organization']); ?></li>
                <li><strong>Address:</strong> <?php echo privacyStatementEscape($privacy['contact_address']); ?></li>
                <li><strong>Privacy Contact / Data Protection Officer:</strong> <?php echo privacyStatementEscape($privacy['privacy_contact']); ?></li>
                <li><strong>Email Address:</strong> <?php echo privacyStatementEscape($privacy['contact_email']); ?></li>
                <li><strong>Contact Number:</strong> <?php echo privacyStatementEscape($privacy['contact_number']); ?></li>
            </ul>
        </section>

        <p><strong><?php echo privacyStatementEscape($privacy['acknowledgement']); ?></strong></p>
    </main>

    <footer class="privacy-document-footer">
        <a href="../index.php?openApplication=1#applicationModal">Return to the Internet Application Form</a>
    </footer>
</body>
</html>
