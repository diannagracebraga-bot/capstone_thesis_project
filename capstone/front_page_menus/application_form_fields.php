<?php
$plan_query = "SELECT plan_id, plan_name, internet_mbps, internet_price 
               FROM internet_plan_tbl 
               ORDER BY plan_id ASC";

$plan_result = mysqli_query($conn, $plan_query);
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

        <fieldset class="form_group application-sex-field">
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
            <input type="tel" id="application_contact_number" class="form-control" name="contact_number" autocomplete="tel" required >
        </div>

        <div class="form_group">
            <label for="application_email">Email Address</label>
            <input type="email" id="application_email" class="form-control" name="email" autocomplete="email" placeholder="Enter your email address" required>
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

            <small class="form-text text-muted">
                <strong>Address in this format ONLY!</strong><br>
                B = BLOCK, L = LOT, P = PHASE, S = SECTION
            </small>
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

        <div class="form_group">
            <label for="application_carissa_phase">
                Are you from Phase 7 Carissa?
            </label>

            <select
                class="form-control"
                id="application_carissa_phase"
                name="phase_7_carissa"
                required
            >
                <option value="">-- Select --</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>

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
        <button type="submit" class="btn btn-primary">
            Submit Application
        </button>

    </div>
</form>