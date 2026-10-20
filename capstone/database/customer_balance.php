<?php
/**
 * Estimate unpaid monthly billing cycles from the customer's current due date.
 * A payment advances due_date by one month, so each due date that has arrived
 * represents one monthly charge still owed.
 */
function calculateCustomerBalance($monthlyCharge, $dueDate, ?DateTimeImmutable $asOf = null)
{
    $dueDate = trim((string) $dueDate);
    $parsedDueDate = DateTimeImmutable::createFromFormat('!Y-m-d', $dueDate);
    $validDueDate = $dueDate !== ''
        && $dueDate !== '0000-00-00'
        && $parsedDueDate instanceof DateTimeImmutable
        && $parsedDueDate->format('Y-m-d') === $dueDate;

    if (!$validDueDate) {
        return [
            'has_due_date' => false,
            'balance' => 0.0,
            'cycle_count' => 0,
            'months' => [],
            'remaining_months' => 0,
        ];
    }

    $asOf = $asOf ?? new DateTimeImmutable('today');
    if ($parsedDueDate > $asOf) {
        return [
            'has_due_date' => true,
            'balance' => 0.0,
            'cycle_count' => 0,
            'months' => [],
            'remaining_months' => 0,
        ];
    }

    $dueMonthIndex = ((int) $parsedDueDate->format('Y') * 12) + (int) $parsedDueDate->format('n');
    $currentMonthIndex = ((int) $asOf->format('Y') * 12) + (int) $asOf->format('n');
    $cycleCount = max(1, $currentMonthIndex - $dueMonthIndex + 1);

    $months = [];
    $monthsToList = min($cycleCount, 3);
    $firstBillingMonth = $parsedDueDate->modify('first day of this month');
    for ($index = 0; $index < $monthsToList; $index++) {
        $months[] = $firstBillingMonth->modify('+' . $index . ' months')->format('F Y');
    }

    return [
        'has_due_date' => true,
        'balance' => max(0, (float) $monthlyCharge) * $cycleCount,
        'cycle_count' => $cycleCount,
        'months' => $months,
        'remaining_months' => max(0, $cycleCount - count($months)),
    ];
}
?>
