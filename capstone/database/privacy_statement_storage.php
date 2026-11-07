<?php
function privacyStatementDefaults(): array
{
    return [
        'effective_date' => '[Insert Date]',
        'organization' => 'MitztianPC Wired Internet Service – Tanza, Cavite',
        'privacy_contact' => '[Insert Name or Designated Contact]',
        'contact_address' => 'B25 L13 Carissa Homes, Bagtas, Tanza, Cavite, Philippines',
        'contact_email' => 'mpcwiredinternetservices@gmail.com',
        'contact_number' => '[Insert Official Contact Number]',
        'section_1' => 'MitztianPC Wired Internet Service respects your privacy and is committed to protecting your personal information in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173) and its implementing rules and regulations.\n\nThis Privacy Statement explains how the MitztianPC Web-Based Plan Monitoring and Admin Tracking System collects, uses, stores, and protects your personal information when you access or use our website.',
        'section_2' => 'Depending on your use of the system, we may collect the following information:\n• Personal Information: Full name, address, contact number, and email address.\n• Account Information: Account number, username, and relevant account details.\n• Subscription Information: Selected internet plan, subscription status, and payment due dates.\n• Payment Information: Payment amount, payment method, reference number, transaction date, and payment status.\n• Customer Concerns: Information submitted through inquiries, complaints, and support requests.',
        'section_3' => 'Your personal information may be collected and processed for the following purposes:\n• Managing customer accounts and registration applications.\n• Monitoring internet subscription plans and account status.\n• Recording and verifying payment transactions.\n• Providing payment history and Statements of Account.\n• Sending notifications regarding payments, due dates, and account updates.\n• Managing customer inquiries, complaints, and service concerns.\n• Improving the services and functionality of the system.',
        'section_4' => 'The system supports QR Ph payments through the integrated PayMongo API and manual recording of cash payments by authorized administrators.\n\nPayment transactions processed through PayMongo are subject to its applicable privacy and security practices. MitztianPC will handle payment-related information according to the purposes and processes implemented in the system.',
        'section_5' => 'Personal information will only be accessed by authorized personnel and relevant service providers when necessary for system operations and service delivery. Information may also be disclosed when required or permitted by applicable laws and regulations.\n\nMitztianPC does not intend to sell customers’ personal information to unauthorized third parties.',
        'section_6' => 'MitztianPC implements appropriate organizational, physical, and technical measures to help protect personal information against unauthorized access, disclosure, alteration, loss, or misuse.\n\nAccess to customer information is restricted to authorized users based on their assigned roles and responsibilities.',
        'section_7' => 'Personal information will be retained only for as long as necessary to fulfill the purposes for which it was collected, comply with applicable legal obligations, and address legitimate business requirements. Information will be securely disposed of or anonymized when it is no longer necessary to retain it, subject to applicable laws.',
        'section_8' => 'Under the Data Privacy Act of 2012, you may exercise applicable rights as a data subject, including:\n• The right to be informed about how your personal information is processed.\n• The right to access your personal information.\n• The right to correct inaccurate or incomplete information.\n• The right to object to certain types of processing.\n• The right to request erasure or blocking when legally applicable.\n• The right to file a complaint with the National Privacy Commission when your privacy rights have been violated.\n\nRequests will be handled in accordance with applicable data privacy laws.',
        'section_9' => 'MitztianPC may update this Privacy Statement when necessary to reflect changes in system features, data processing practices, or applicable regulations. Any updates will be published on the website.',
        'acknowledgement' => 'By using the system, you acknowledge that you have been informed about the collection and processing of your personal information as explained in this Privacy Statement.'
    ];
}

function getPrivacyStatement(mysqli $conn): array
{
    $defaults = privacyStatementDefaults();
    $columns = array_keys($defaults);
    $definitions = [];
    foreach ($columns as $column) {
        $definitions[] = "`$column` TEXT NOT NULL";
    }
    $createSql = 'CREATE TABLE IF NOT EXISTS privacy_statement_tbl (privacy_id TINYINT UNSIGNED NOT NULL PRIMARY KEY, ' . implode(', ', $definitions) . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    if (!mysqli_query($conn, $createSql)) {
        return $defaults;
    }

    $stmt = mysqli_prepare($conn, 'INSERT IGNORE INTO privacy_statement_tbl (privacy_id, ' . implode(', ', array_map(static fn($column) => "`$column`", $columns)) . ') VALUES (1, ' . implode(', ', array_fill(0, count($columns), '?')) . ')');
    if ($stmt) {
        $values = array_values($defaults);
        $types = str_repeat('s', count($values));
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $result = mysqli_query($conn, 'SELECT ' . implode(', ', array_map(static fn($column) => "`$column`", $columns)) . ' FROM privacy_statement_tbl WHERE privacy_id = 1 LIMIT 1');
    $saved = $result ? mysqli_fetch_assoc($result) : null;
    $policy = $saved ? array_merge($defaults, $saved) : $defaults;
    foreach ($policy as $key => $value) {
        $policy[$key] = str_replace('\\n', "\n", (string) $value);
    }
    return $policy;
}

function privacyStatementEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
