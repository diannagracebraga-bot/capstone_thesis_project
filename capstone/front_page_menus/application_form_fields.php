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

        <div class="form_group">
            <label for="application_birth_date">Birth Date</label>
            <input type="date" id="application_birth_date" class="form-control" name="birth_date" required>
        </div>

        <fieldset class="form_group application-sex-field">
            <legend>Sex</legend>
            <label class="application-radio"><input class="form-check-input" type="radio" name="sex" value="male" required> Male</label>
            <label class="application-radio"><input class="form-check-input" type="radio" name="sex" value="female"> Female</label>
        </fieldset>

        <div class="form_group application-section-heading">
            <label>Contact and Address Details</label>
        </div>

        <div class="form_group">
            <label for="application_contact_number">Contact Number</label>
            <input type="tel" id="application_contact_number" class="form-control" name="contact_number" autocomplete="tel" required>
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
            <label for="application_house_number">House Number</label>
            <input type="text" id="application_house_number" class="form-control" name="house_number" required>
        </div>

        <div class="form_group">
            <label for="application_street">Street</label>
            <input type="text" id="application_street" class="form-control" name="street">
        </div>

        <div class="form_group">
            <label for="application_subdivision">Subdivision</label>
            <input type="text" id="application_subdivision" class="form-control" name="subdivision">
        </div>

        <div class="form_group">
            <label for="application_plan">Internet Plan</label>
            <select class="form-control" id="application_plan" name="internet_plan" required>
                <option value="">-- Select Plan --</option>
                <option>BRONZE 800 - 50 Mbps</option>
                <option>SILVER 900 - 70 Mbps</option>
                <option>GOLD 1000 - 100 Mbps</option>
                <option>DIAMOND 1500 - 150 Mbps</option>
                <option>PLATINUM 1800 - 200 Mbps</option>
            </select>
        </div>

        <div class="form_group application-id-upload">
            <label>Upload Valid ID (Optional)</label>
            <label for="application_id_front">Front</label>
            <input type="file" class="form-control" name="id_front" id="application_id_front" accept="image/*">
            <label for="application_id_back">Back</label>
            <input type="file" class="form-control" name="id_back" id="application_id_back" accept="image/*">
        </div>

        <div class="form_group application-filled-by">
            <label for="application_filled_up_by">Filled Up By:</label>
            <input type="text" id="application_filled_up_by" class="form-control" name="filled_up_by" autocomplete="name" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit Application</button>
    </div>
</form>
