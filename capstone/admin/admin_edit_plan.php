<?php
require_once __DIR__ . '/require_super_admin.php';

$error = '';
$alertMessage = '';
$statusColumn = mysqli_query($conn, "SHOW COLUMNS FROM internet_plan_tbl LIKE 'is_active'");
if ($statusColumn && mysqli_num_rows($statusColumn) === 0) {
    mysqli_query($conn, 'ALTER TABLE internet_plan_tbl ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1');
}
if (isset($_GET['added'])) $alertMessage = 'Plan added successfully.';
if (isset($_GET['saved'])) $alertMessage = 'Plan changes saved successfully.';
if (isset($_GET['status'])) $alertMessage = 'Plan status updated successfully.';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_plan'])) {
    $planId = filter_var($_POST['toggle_plan'], FILTER_VALIDATE_INT);
    $stmt = $planId ? mysqli_prepare($conn, 'UPDATE internet_plan_tbl SET is_active = IF(is_active = 1, 0, 1) WHERE plan_id = ?') : false;
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $planId);
        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1) {
            header('Location: admin_edit_plan.php?status=1');
            exit();
        }
        mysqli_stmt_close($stmt);
    }
    $error = 'Unable to update plan status. Please try again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan'])) {
    $planName = trim($_POST['plan_name'] ?? '');
    $internetPrice = filter_var($_POST['internet_price'] ?? null, FILTER_VALIDATE_FLOAT);
    $internetMbps = filter_var($_POST['internet_mbps'] ?? null, FILTER_VALIDATE_INT);

    if ($planName === '' || $internetPrice === false || $internetPrice < 0 || $internetMbps === false || $internetMbps < 1) {
        $error = 'Enter a valid plan name, a price of zero or more, and a speed of at least 1 Mbps.';
    } else {
        $stmt = mysqli_prepare($conn, 'INSERT INTO internet_plan_tbl (plan_name, internet_price, internet_mbps, is_active) VALUES (?, ?, ?, 1)');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sdi', $planName, $internetPrice, $internetMbps);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: admin_edit_plan.php?added=1');
                exit();
            }
            mysqli_stmt_close($stmt);
        }
        $error = 'Could not add the plan. Please try again.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_plans'])) {
    $names = $_POST['plan_name'] ?? [];
    $prices = $_POST['internet_price'] ?? [];
    $speeds = $_POST['internet_mbps'] ?? [];
    $validated = [];
    foreach ($names as $id => $name) {
        $price = filter_var($prices[$id] ?? null, FILTER_VALIDATE_FLOAT);
        $speed = filter_var($speeds[$id] ?? null, FILTER_VALIDATE_INT);
        $name = trim((string) $name);
        if (!ctype_digit((string) $id) || $name === '' || $price === false || $price < 0 || $speed === false || $speed < 1) {
            $error = 'A plan has an invalid name, price, or speed. No changes were saved.';
            break;
        }
        $validated[] = [(int) $id, $name, $price, $speed];
    }
    if ($error === '') {
        $stmt = mysqli_prepare($conn, 'UPDATE internet_plan_tbl SET plan_name = ?, internet_price = ?, internet_mbps = ? WHERE plan_id = ?');
        if ($stmt) {
            foreach ($validated as [$planId, $planName, $internetPrice, $internetMbps]) {
                mysqli_stmt_bind_param($stmt, 'sdii', $planName, $internetPrice, $internetMbps, $planId);
                if (!mysqli_stmt_execute($stmt)) {
                    $error = 'Could not save the plans. Please try again.';
                    break;
                }
            }
            mysqli_stmt_close($stmt);
            if ($error === '') {
                header('Location: admin_edit_plan.php?saved=1');
                exit();
            }
        } else {
            $error = 'Could not save the plans. Please try again.';
        }
    }
}

$planResult = mysqli_query($conn, 'SELECT plan_id, plan_name, internet_price, internet_mbps, is_active FROM internet_plan_tbl ORDER BY plan_id ASC');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet" href="../css/admin_edit_plan.css?v=plans-management-2">
    <title>Plan Management | MITZTIANPC WIRED INTERNET SERVICES</title>
</head>
<body>
<?php include 'admin_sidebar_header_profile.php'; ?>
<div class="card plan-page-card">
    <div class="card-body">
        <div class="plan-container">
            <div class="plan-heading">
                <div><h2>Plan Management</h2></div>
            </div>
            <?php if ($alertMessage !== ''): ?><script>alert(<?php echo json_encode($alertMessage); ?>);</script><?php endif; ?>
            <?php if ($error !== ''): ?><script>alert(<?php echo json_encode($error); ?>);</script><?php endif; ?>

            <section class="add-plan-panel">
                <h3>Add Plan</h3>
                <form method="POST" class="add-plan-form">
                    <label>Plan Name<input type="text" name="plan_name" maxlength="100" required></label>
                    <label>Monthly Price (₱)<input type="number" name="internet_price" min="0" step="0.01" required></label>
                    <label>Speed (Mbps)<input type="number" name="internet_mbps" min="1" step="1" required></label>
                    <button type="submit" name="add_plan" class="save-btn">Add Plan</button>
                </form>
            </section>

            <form method="POST" class="plan-list-form">
                <h3>All Plans</h3>
                <div class="plan-container-inner">
                    <?php if ($planResult && mysqli_num_rows($planResult) > 0): ?>
                        <?php while ($plan = mysqli_fetch_assoc($planResult)): ?>
                            <div class="form-group row mb-4">
                                <label class="col-sm-2 col-form-label">Plan Name:</label>
                                <div class="col-sm-2"><input type="text" class="form-control" name="plan_name[<?php echo (int) $plan['plan_id']; ?>]" value="<?php echo htmlspecialchars($plan['plan_name'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                                <label class="col-sm-2 col-form-label">Internet Price:</label>
                                <div class="col-sm-2"><input type="number" min="0" step="0.01" class="form-control" name="internet_price[<?php echo (int) $plan['plan_id']; ?>]" value="<?php echo htmlspecialchars($plan['internet_price'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                                <label class="col-sm-1 col-form-label">Mbps:</label>
                                <div class="col-sm-2"><input type="number" min="1" step="1" class="form-control" name="internet_mbps[<?php echo (int) $plan['plan_id']; ?>]" value="<?php echo htmlspecialchars($plan['internet_mbps'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                                <div class="plan-status-actions">
                                    <span class="plan-status <?php echo (int) $plan['is_active'] === 1 ? 'active' : 'inactive'; ?>"><?php echo (int) $plan['is_active'] === 1 ? 'Active' : 'Inactive'; ?></span>
                                    <button type="submit" name="toggle_plan" value="<?php echo (int) $plan['plan_id']; ?>" class="status-btn <?php echo (int) $plan['is_active'] === 1 ? 'deactivate' : 'activate'; ?>"><?php echo (int) $plan['is_active'] === 1 ? 'Deactivate' : 'Activate'; ?></button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty-plans">There are no plans yet. Use the Add Plan form to create one.</p>
                    <?php endif; ?>
                </div>
                <button type="submit" name="save_plans" class="save-btn">Save Changes</button>
            </form>
        </div>
    </div>
</div>
<script>
window.addEventListener('pageshow', function(event) {
    if (event.persisted) window.location.reload();
});
</script>
</body>
</html>
