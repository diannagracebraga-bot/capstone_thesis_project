<?php
session_start();

require_once '../database/database_connection.php';
require_once '../pdf_library/fpdf19/fpdf.php';

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'customer'
) {
    http_response_code(403);
    exit('Unauthorized access.');
}

$user_id = (int) $_SESSION['user_id'];

$month = filter_input(INPUT_GET, 'month', FILTER_VALIDATE_INT);
$year  = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT);

if (
    !$month || $month < 1 || $month > 12 ||
    !$year || $year < 2000 || $year > 2100
) {
    http_response_code(400);
    exit('Invalid billing month.');
}

$start_date = sprintf('%04d-%02d-01', $year, $month);
$end_date = date(
    'Y-m-d',
    strtotime($start_date . ' +1 month')
);

$sql = "SELECT
            c.user_id,
            c.account_number,
            c.f_name,
            c.m_name,
            c.l_name,
            c.due_date,
            p.plan_name,
            p.internet_price,
            p.internet_mbps
        FROM customer_tbl c
        INNER JOIN internet_plan_tbl p
            ON c.internet_plan = p.plan_id
        WHERE c.user_id = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$customer = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$customer) {
    http_response_code(404);
    exit('Customer account not found.');
}

$payment_sql = "SELECT
                    id,
                    amount,
                    payment_method,
                    payment_status,
                    created_at
                FROM payment_tbl
                WHERE user_id = ?
                  AND created_at >= ?
                  AND created_at < ?
                ORDER BY created_at ASC, id ASC";

$stmt = mysqli_prepare($conn, $payment_sql);
mysqli_stmt_bind_param(
    $stmt,
    'iss',
    $user_id,
    $start_date,
    $end_date
);
mysqli_stmt_execute($stmt);

$payment_result = mysqli_stmt_get_result($stmt);
$payments = [];
$total_paid = 0.0;

while ($payment = mysqli_fetch_assoc($payment_result)) {
    $payments[] = $payment;

    if (strcasecmp(trim($payment['payment_status']), 'Paid') === 0) {
        $total_paid += (float) $payment['amount'];
    }
}

mysqli_stmt_close($stmt);

$monthly_charge = (float) $customer['internet_price'];
$balance = $monthly_charge - $total_paid;
$amount_due = max(0, $balance);
$credit = max(0, -$balance);
$customer_name = trim(
    ($customer['f_name'] ?? '') . ' ' .
    ($customer['m_name'] ?? '') . ' ' .
    ($customer['l_name'] ?? '')
);

$billing_period = date('F Y', strtotime($start_date));
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetTitle('Statement of Account - ' . $customer['account_number']);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, 'MITZTIANPC', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 9, 'STATEMENT OF ACCOUNT', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, 'Billing Period: ' . $billing_period, 0, 1, 'C');
$pdf->Ln(6);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, 'CUSTOMER INFORMATION', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(
    95, 7,
    'Customer: ' . $customer_name,
    0, 0
);
$pdf->Cell(
    0, 7,
    'Account No.: ' . $customer['account_number'],
    0, 1
);

$pdf->Cell(
    95, 7,
    'Plan: ' . $customer['plan_name'],
    0, 0
);
$pdf->Cell(
    0, 7,
    'Speed: ' . $customer['internet_mbps'] . ' Mbps',
    0, 1
);

$pdf->Cell(
    0, 7,
    'Due Date: ' .
    (
        !empty($customer['due_date']) &&
        $customer['due_date'] !== '0000-00-00'
            ? date('F d, Y', strtotime($customer['due_date']))
            : 'Not set'
    ),
    0, 1
);

$pdf->Ln(6);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, 'BILLING SUMMARY', 0, 1);
$pdf->SetFont('Arial', '', 10);
$summary = [
    ['Monthly Internet Charge', $monthly_charge],
    ['Payments Received', -$total_paid],
    ['AMOUNT DUE', $amount_due],
];

foreach ($summary as $item) {
    $is_total = ($item[0] === 'AMOUNT DUE');
    $pdf->SetFont('Arial', $is_total ? 'B' : '', 10);
    $pdf->Cell(120, 8, $item[0], 1, 0);
    $pdf->Cell(
        0, 8,
        ($item[1] < 0 ? '- ' : '') .
        'PHP ' . number_format(abs($item[1]), 2),
        1, 1, 'R'
    );
}
if ($credit > 0) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(120, 8, 'CUSTOMER CREDIT', 1, 0);
    $pdf->Cell(
        0, 8,
        'PHP ' . number_format($credit, 2),
        1, 1, 'R'
    );
}
$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, 'PAYMENT HISTORY FOR THIS BILLING PERIOD', 0, 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 8, 'Date', 1);
$pdf->Cell(55, 8, 'Payment Method', 1);
$pdf->Cell(35, 8, 'Status', 1);
$pdf->Cell(55, 8, 'Amount', 1, 1, 'R');
$pdf->SetFont('Arial', '', 9);

if (count($payments) === 0) {
    $pdf->Cell(175, 9, 'No payment records for this month.', 1, 1);
} else {
    foreach ($payments as $payment) {
        $pdf->Cell(
            30, 8,
            date('m-d-Y', strtotime($payment['created_at'])),
            1
        );
        $pdf->Cell(
            55, 8,
            substr($payment['payment_method'], 0, 28),
            1
        );
        $pdf->Cell(
            35, 8,
            substr($payment['payment_status'], 0, 16),
            1
        );
        $pdf->Cell(
            55, 8,
            'PHP ' . number_format((float) $payment['amount'], 2),
            1, 1, 'R'
        );
    }
}
$pdf->Ln(8);
$pdf->SetFont('Arial', 'I', 9);
$pdf->MultiCell(
    0, 6,
    'This statement is based on the customer plan and payment records ' .
    'available in the system for the selected billing period.'
);
$pdf->Output(
    'D',
    'SOA_' . preg_replace(
        '/[^A-Za-z0-9_-]/',
        '_',
        (string) $customer['account_number']
    ) . '_' . $year . '_' . sprintf('%02d', $month) . '.pdf'
);
exit;