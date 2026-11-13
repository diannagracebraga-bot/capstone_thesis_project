<?php
$planStatusColumn = mysqli_query($conn, "SHOW COLUMNS FROM internet_plan_tbl LIKE 'is_active'");
if ($planStatusColumn && mysqli_num_rows($planStatusColumn) === 0) {
    mysqli_query($conn, 'ALTER TABLE internet_plan_tbl ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1');
}
$plan_query = "SELECT plan_id, plan_name, internet_mbps, internet_price 
               FROM internet_plan_tbl
               WHERE is_active = 1
               ORDER BY plan_id ASC";

$plan_result = mysqli_query($conn, $plan_query);

$privacyStatementHref = strpos((string)($applicationAction ?? ""), "../") === 0
    ? "privacy_statement.php"
    : "front_page_menus/privacy_statement.php";
?>

<form class="application-form" action="<?php echo htmlspecialchars($applicationAction, ENT_QUOTES, 'UTF-8'); ?>" method="POST" enctype="multipart/form-data">
    <div class="container application-form-card">
        <h1>Internet Application Form</h1>

        <div class="form_group application-section-heading">
            <label>Personal Details</label>
        </div>

        <div class="form_group">
            <label for="application_first_name">First Name</label>
            <input type="text" id="application_first_name" class="form-control" name="first_name" autocomplete="given-name" required>
        </div>

        <div class="form_group">
            <label for="application_middle_name">Middle Name</label>
            <input type="text" id="application_middle_name" class="form-control" name="middle_name" autocomplete="additional-name">
        </div>

        <div class="form_group">
            <label for="application_last_name">Last Name</label>
            <input type="text" id="application_last_name" class="form-control" name="last_name" autocomplete="family-name" required>
        </div>

        <fieldset class="form_group application-choice-field">
            <legend>Sex</legend>

            <label class="application-radio">
                <input class="form-check-input" type="radio" name="sex" value="male" required>
                Male
            </label>

            <label class="application-radio">
                <input class="form-check-input" type="radio" name="sex" value="female">
                Female
            </label>
        </fieldset>


        <div class="form_group application-section-heading">
            <label>Contact and Address Details</label>
        </div>

       
<div class="form_group">
    <label for="application_contact_number">Contact Number</label>
    <input type="tel" id="application_contact_number" class="form-control" name="contact_number" autocomplete="tel" maxlength="11"
           pattern="09[0-9]{9}" title="Please enter exactly 11 digits starting with 09." required>
</div>

<div class="form_group">
    <label for="application_email">Email Address</label>
    <input type="email" id="application_email" class="form-control" name="email" autocomplete="email"
        pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|ph|net|org|edu|gov)"
        title="Please enter an email ending in .com, .ph, .net, .org, .edu, or .gov."
        required>
</div>


        <div class="form_group">
            <label for="application_facebook_account">Facebook Account</label>
            <input type="text" id="application_facebook_account" class="form-control" name="facebook_account" placeholder="Enter your Facebook name or profile link" required>
        </div>

        <div class="form_group">
            <label for="application_barangay">Barangay</label>
            <select class="form-control" id="application_barangay" name="barangay" required>
                <option value="">-- Select Barangay --</option>
                <option value="Bagtas">Bagtas</option>
                <option value="Punta I">Punta I</option>
            </select>
        </div>

        <div class="form_group">
            <label for="application_address">Address</label>

            <input type="text" id="application_address" class="form-control" name="address" placeholder="Example: B17 L17 P2 / 333 CALLE 60 / B1 L1 S1 P1 PABAHAY" required >


        </div>


        <div class="form_group">
            <label for="application_plan">Internet Plan</label>
           <select class="form-control" id="application_plan" name="internet_plan" required>
    <option value="">-- Select Plan --</option>

    <?php while ($plan = mysqli_fetch_assoc($plan_result)) { ?>
        <option value="<?php echo $plan['plan_id']; ?>">
            <?php echo $plan['plan_name']; ?> -
            <?php echo $plan['internet_mbps']; ?> Mbps -
            ₱<?php echo number_format($plan['internet_price'], 2); ?>
        </option>
    <?php } ?>
</select>
        </div>


        <div class="form_group application-section-heading">
            <label>Installation Details</label>
        </div>

        <div class="form_group">
            <label for="application_installation_date">
                Desired Installation Date
            </label>

            <input type="date" id="application_installation_date" class="form-control" name="desired_installation_date" required>

            <small class="form-text text-muted">
                Please select your preferred installation date.
                <strong>Installation is not available every Sunday.</strong>
            </small>
        </div>

        <div class="alert alert-info" role="alert">
            <strong>Installation Notice:</strong>
            Installation may take up to <strong>1–3 days</strong> depending on
            availability, location, and installation requirements.
        </div>


        <div class="form_group application-section-heading">
            <label>HOA Requirement</label>
        </div>

        <fieldset class="form_group application-choice-field">
            <legend>Are you from Phase 7 Carissa?</legend>
            <label class="application-radio">
                <input class="form-check-input" type="radio" name="phase_7_carissa" value="Yes" required>
                Yes
            </label>
            <label class="application-radio">
                <input class="form-check-input" type="radio" name="phase_7_carissa" value="No">
                No
            </label>
        </fieldset>

      <div class="form_group">
    <label for="application_hoa_certificate">
        HOA Certificate
    </label>

    <input type="file" id="application_hoa_certificate" class="form-control" name="hoa_certificate" accept=".jpg,.jpeg,.png,.pdf">

    <small class="form-text text-muted">
        <strong>Required for customers from Phase 7 Carissa.</strong>
        Please upload your HOA Certificate if applicable.
        Accepted file types: JPG, JPEG, PNG, and PDF.
    </small>
</div>
        <div class="form_group application-filled-by">
            <label for="application_filled_up_by"> Filled Up By:</label>

            <input type="text" id="application_filled_up_by" class="form-control" name="filled_up_by" autocomplete="name" required >
        </div>
        <section class="privacy-notice" aria-labelledby="application-privacy-title">
            <h2 id="application-privacy-title">Privacy Notice</h2>
            <p>Your personal information will be collected and processed for account registration, subscription management, payment monitoring, and customer support. Please read our Privacy Policy to understand how your information is handled.</p>
            <a class="privacy-policy-link" href="<?php echo htmlspecialchars($privacyStatementHref, ENT_QUOTES, 'UTF-8'); ?>" data-open-privacy-modal aria-haspopup="dialog">
                <span aria-hidden="true">↗</span> Read our Privacy Policy
            </a>
            <hr>
            <label class="privacy-acknowledgement" for="application_privacy_acknowledged">
                <input type="checkbox" id="application_privacy_acknowledged" name="privacy_notice_acknowledged" value="1" required>
                <span>I acknowledge that I have read the Privacy Notice.</span>
            </label>
            <p class="privacy-disclaimer">This acknowledgment is not automatically the same as consent to every type of data processing. Separate consent will be requested when it is the applicable lawful basis.</p>
        </section>
        <div class="privacy-statement-overlay" data-privacy-modal aria-hidden="true">
            <section class="privacy-statement-dialog" role="dialog" aria-modal="true" aria-label="Privacy Statement" tabindex="-1">
                <header class="privacy-statement-modal-header">
                    <img class="privacy-statement-modal-logo" src="<?php echo strpos((string)($applicationAction ?? ''), '../') === 0 ? '../images/bg_logo.png' : 'images/bg_logo.png'; ?>" alt="MITZTIANPC Wired Internet Services">
                    <button type="button" class="privacy-statement-close" data-close-privacy-modal aria-label="Close Privacy Statement">&times;</button>
                </header>
                <iframe src="<?php echo htmlspecialchars($privacyStatementHref . '?embed=1', ENT_QUOTES, 'UTF-8'); ?>" title="MitztianPC Privacy Statement" loading="lazy"></iframe>
            </section>
        </div>        <button type="submit" class="btn btn-primary">
            Submit Application
        </button>

    </div>
</form>
<script>
document.querySelectorAll("[data-open-privacy-modal]").forEach(function (link) {
    link.addEventListener("click", function (event) {
        var form = link.closest(".application-form");
        var overlay = form && form.querySelector("[data-privacy-modal]");
        if (!overlay) return;

        event.preventDefault();
        overlay.classList.add("is-open");
        overlay.setAttribute("aria-hidden", "false");
        var closeButton = overlay.querySelector("[data-close-privacy-modal]");
        if (closeButton) closeButton.focus();
    });
});

document.querySelectorAll("[data-privacy-modal]").forEach(function (overlay) {
    function closePrivacyModal() {
        overlay.classList.remove("is-open");
        overlay.setAttribute("aria-hidden", "true");
        var link = overlay.closest(".application-form")?.querySelector("[data-open-privacy-modal]");
        if (link) link.focus();
    }

    overlay.querySelector("[data-close-privacy-modal]").addEventListener("click", closePrivacyModal);
    overlay.addEventListener("click", function (event) {
        if (event.target === overlay) closePrivacyModal();
    });
    overlay.addEventListener("keydown", function (event) {
        if (event.key === "Escape") closePrivacyModal();
    });
});
</script>
