<?php
require_once __DIR__ . '/require_super_admin.php';
$sql = "SELECT * FROM content_management_about_tbl WHERE about_id = 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$showPrivacyEditor = ($_GET['section'] ?? '') === 'privacy';
$privacy = null;
if ($showPrivacyEditor) {
    require_once __DIR__ . '/../database/privacy_statement_storage.php';
    $privacy = getPrivacyStatement($conn);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
	<link rel="stylesheet" href="../css/admin_content_management.css?v=privacy-editor-1">   
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css?v=announcement-theme-4">
	<title>MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>
		<?php include 'admin_sidebar_header_profile.php'; ?>
    <div class="card w-75 content-management-page-card">
  				<div class="card-body">
                    <div class="content-management-container">
    <div class="content-wrapper">
    <div class="pill-nav mb-3">
         <a class="<?php echo !$showPrivacyEditor ? 'active' : ''; ?>" href="admin_content_management.php">Edit Services</a>
         <a href="admin_edit_plan.php">Edit Internet Plan</a>
         <a class="<?php echo $showPrivacyEditor ? 'active' : ''; ?>" href="admin_content_management.php?section=privacy">Privacy Statement</a>
    </div>
        <?php if ($showPrivacyEditor): ?>
        <div class="business-info-card">
            <h2 class="content-section-title">Edit Privacy Statement</h2>
            <p class="text-secondary">Baguhin ang mga text sa ibaba at pindutin ang Save. Lalabas ang mga pagbabago sa Privacy Statement link ng application form.</p>
            <?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Na-save na ang Privacy Statement.</div><?php endif; ?>
            <form action="../crud/update_privacy_statement.php" method="POST" class="form-horizontal">
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><label class="form-label fw-bold">Effective Date</label><input class="form-control" name="effective_date" value="<?php echo privacyStatementEscape($privacy['effective_date']); ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Organization / Branch</label><input class="form-control" name="organization" value="<?php echo privacyStatementEscape($privacy['organization']); ?>" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Privacy Contact / Data Protection Officer</label><input class="form-control" name="privacy_contact" value="<?php echo privacyStatementEscape($privacy['privacy_contact']); ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Contact Number</label><input class="form-control" name="contact_number" value="<?php echo privacyStatementEscape($privacy['contact_number']); ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Email Address</label><input type="email" class="form-control" name="contact_email" value="<?php echo privacyStatementEscape($privacy['contact_email']); ?>"></div>
                    <div class="col-md-6"><label class="form-label fw-bold">Address</label><input class="form-control" name="contact_address" value="<?php echo privacyStatementEscape($privacy['contact_address']); ?>"></div>
                </div>
                <?php
                $sectionLabels = [
                    1 => '1. Introduction', 2 => '2. Information We Collect', 3 => '3. Purpose of Data Collection',
                    4 => '4. Payment Processing', 5 => '5. Data Sharing and Disclosure', 6 => '6. Data Storage and Security',
                    7 => '7. Data Retention', 8 => '8. Your Data Privacy Rights', 9 => '9. Changes to This Privacy Statement'
                ];
                foreach ($sectionLabels as $number => $label): ?>
                    <div class="mb-3"><label class="form-label fw-bold" for="privacy-section-<?php echo $number; ?>"><?php echo $label; ?></label><textarea class="form-control privacy-editor-textarea" id="privacy-section-<?php echo $number; ?>" name="section_<?php echo $number; ?>" rows="<?php echo in_array($number, [2, 3, 8], true) ? 7 : 4; ?>" required><?php echo privacyStatementEscape($privacy['section_' . $number]); ?></textarea><small class="form-text text-muted">Pwede ang maraming paragraph. Gumamit ng bagong linya para sa bawat item.</small></div>
                <?php endforeach; ?>
                <div class="mb-3"><label class="form-label fw-bold" for="privacy-acknowledgement">Closing Statement</label><textarea class="form-control" id="privacy-acknowledgement" name="acknowledgement" rows="3" required><?php echo privacyStatementEscape($privacy['acknowledgement']); ?></textarea></div>
                <button type="submit" class="btn btn-success save-btn">Save Privacy Statement</button>
            </form>
        </div>
        <?php else: ?>
   <form action="../crud/update_content_management.php" method="POST" class="form-horizontal">
        <div class="business-info-card">
            <h2 class="content-section-title">Business Information</h2>
            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">BUSINESS NAME:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="business_name"
                           value="<?php echo $row['business_name']; ?>">
                </div>
            </div>

            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">BUSINESS EMAIL:</label>
                <div class="col-sm-7">
                    <input type="email" class="form-control" name="business_email"
                           value="<?php echo $row['business_email']; ?>">
                </div>
            </div>
            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">BUSINESS CONTACT:</label>
                <div class="col-sm-7">
                    <input type="number" class="form-control" name="business_contact"
                           value="<?php echo $row['business_contact']; ?>">
                </div>
            </div>
            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">FACEBOOK <i class="fa fa-pagelines" aria-hidden="true"></i>:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="business_social_media"
                           value="<?php echo $row['business_social_media']; ?>">
                </div>
            </div>
            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">BUSINESS ADDRESS:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="business_address"
                           value="<?php echo $row['business_address']; ?>">
                </div>
            </div>
            <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">BUSINESS DESCRIPTION:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="business_description"
                           value="<?php echo $row['business_description']; ?>">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-9 offset-sm-6">
                    <button type="submit" name="update" class="btn btn-success save-btn">Save</button>
            </div>
        </div>
    </div>
</form>
        <?php endif; ?>
</div>
</div>
<br>	
</div>
<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>
</body>
</html>