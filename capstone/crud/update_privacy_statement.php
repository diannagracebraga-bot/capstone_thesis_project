<?php
require_once __DIR__ . '/../admin/require_super_admin.php';
require_once __DIR__ . '/../database/privacy_statement_storage.php';

$defaults = privacyStatementDefaults();
$privacy = getPrivacyStatement($conn);
$columns = array_keys($defaults);
$values = [];
foreach ($columns as $column) {
    $value = trim((string) ($_POST[$column] ?? ''));
    if ($value === '' && in_array($column, ['privacy_contact', 'contact_address', 'contact_email', 'contact_number'], true)) {
        $value = $privacy[$column];
    }
    if ($value === '' && !in_array($column, ['privacy_contact', 'contact_address', 'contact_email', 'contact_number'], true)) {
        http_response_code(400);
        exit('Please complete all required privacy statement fields.');
    }
    $values[] = $value;
}

$sql = 'UPDATE privacy_statement_tbl SET ' . implode(', ', array_map(static fn($column) => "`$column` = ?", $columns)) . ' WHERE privacy_id = 1';
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    http_response_code(500);
    exit('Unable to prepare the Privacy Statement update.');
}
$types = str_repeat('s', count($values));
$bind = [$types];
foreach ($values as $index => $value) {
    $bind[] = &$values[$index];
}
call_user_func_array([$stmt, 'bind_param'], $bind);
$updated = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$updated) {
    http_response_code(500);
    exit('Unable to save the Privacy Statement. Please try again.');
}

header('Location: ../admin/admin_content_management.php?section=privacy&saved=1');
exit;
?>
