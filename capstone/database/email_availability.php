<?php

function emailIsAlreadyUsed(mysqli $conn, string $email): ?bool
{
    $email = strtolower(trim($email));
    $checks = [
        ['user_accounts_tbl', 'email'],
        ['internet_application_tbl', 'email']
    ];

    try {
        foreach ($checks as [$table, $column]) {
            $statement = mysqli_prepare($conn, "SELECT 1 FROM `$table` WHERE LOWER(`$column`) = ? LIMIT 1");
            if (!$statement) {
                return null;
            }

            mysqli_stmt_bind_param($statement, 's', $email);
            if (!mysqli_stmt_execute($statement)) {
                mysqli_stmt_close($statement);
                return null;
            }

            mysqli_stmt_store_result($statement);
            $exists = mysqli_stmt_num_rows($statement) > 0;
            mysqli_stmt_close($statement);
            if ($exists) {
                return true;
            }
        }
    } catch (Throwable $error) {
        return null;
    }

    return false;
}
