<?php
session_start();
include '../database/database_connection.php';
$customer_query = "
    SELECT COUNT(*) AS total_customers
    FROM customer_tbl
";
$customer_result = mysqli_query($conn, $customer_query);
if (!$customer_result) {
    die("Customer query failed: " . mysqli_error($conn));
}
$customer_data = mysqli_fetch_assoc($customer_result);
$total_customers = $customer_data['total_customers'];
$pending_query = "
    SELECT COUNT(*) AS pending_applicants
    FROM internet_application_tbl
    WHERE status = 'Pending'
";
$pending_result = mysqli_query($conn, $pending_query);
if (!$pending_result) {
    die("Pending applicants query failed: " . mysqli_error($conn));
}
$pending_data = mysqli_fetch_assoc($pending_result);
$pending_applicants = $pending_data['pending_applicants'];
$active_query = "
    SELECT COUNT(*) AS active_users
    FROM customer_tbl
    WHERE connection_status = 'Connected'
";
$active_result = mysqli_query($conn, $active_query);
if (!$active_result) {
    die("Active user query failed: " . mysqli_error($conn));
}
$active_data = mysqli_fetch_assoc($active_result);
$active_users = $active_data['active_users'];
$collection_query = "
    SELECT COALESCE(SUM(amount), 0) AS total_collected
    FROM payment_tbl
    WHERE payment_status = 'Paid'
    AND YEAR(created_at) = YEAR(CURDATE())
    AND MONTH(created_at) = MONTH(CURDATE())
";
$frozen_query = "
    SELECT COUNT(*) AS frozen_accounts
    FROM customer_tbl
    WHERE connection_status = 'Frozen'
";

$frozen_result = mysqli_query($conn, $frozen_query);

if (!$frozen_result) {
    die("Frozen accounts query failed: " . mysqli_error($conn));
}

$frozen_data = mysqli_fetch_assoc($frozen_result);
$frozen_accounts = (int) $frozen_data['frozen_accounts'];


$inactive_query = "
    SELECT COUNT(*) AS inactive_clients
    FROM customer_tbl
    WHERE connection_status = 'Inactive'
";

$inactive_result = mysqli_query($conn, $inactive_query);

if (!$inactive_result) {
    die("Inactive clients query failed: " . mysqli_error($conn));
}

$inactive_data = mysqli_fetch_assoc($inactive_result);
$inactive_clients = (int) $inactive_data['inactive_clients'];

$collection_result = mysqli_query($conn, $collection_query);
if (!$collection_result) {
    die("Collection query failed: " . mysqli_error($conn));
}
$collection_data = mysqli_fetch_assoc($collection_result);
$total_collected = $collection_data['total_collected'];
$balance_query = "
    SELECT COUNT(*) AS clients_with_balance
    FROM (
        SELECT
            c.customer_id,
            p.internet_price,
            COALESCE(SUM(
                CASE
                    WHEN pay.payment_status = 'Paid'
                    AND YEAR(pay.created_at) = YEAR(CURDATE())
                    AND MONTH(pay.created_at) = MONTH(CURDATE())
                    THEN pay.amount
                    ELSE 0
                END
            ), 0) AS total_paid
        FROM customer_tbl c

        LEFT JOIN internet_plan_tbl p
            ON c.internet_plan = p.plan_id
        LEFT JOIN payment_tbl pay
            ON c.user_id = pay.user_id
        WHERE c.connection_status = 'Connected'
        GROUP BY
            c.customer_id,
            p.internet_price
        HAVING
            COALESCE(p.internet_price, 0) >
            COALESCE(total_paid, 0)
    ) AS balance_table
";
$balance_result = mysqli_query($conn, $balance_query);
if (!$balance_result) {
    die("Balance query failed: " . mysqli_error($conn));
}
$balance_data = mysqli_fetch_assoc($balance_result);
$clients_with_balance = $balance_data['clients_with_balance'];
$new_active_query = "
    SELECT COUNT(*) AS new_active_clients
    FROM customer_tbl c
    INNER JOIN (
        SELECT
            user_id,
            MIN(created_at) AS first_payment
        FROM payment_tbl
        WHERE payment_status = 'Paid'
        GROUP BY user_id
    ) first_payment
        ON c.user_id = first_payment.user_id
    WHERE c.connection_status = 'Connected'
    AND YEAR(first_payment.first_payment) = YEAR(CURDATE())
    AND MONTH(first_payment.first_payment) = MONTH(CURDATE())
";
$new_active_result = mysqli_query($conn, $new_active_query);
if (!$new_active_result) {
    die("New active clients query failed: " . mysqli_error($conn));
}
$new_active_data = mysqli_fetch_assoc($new_active_result);
$new_active_clients = $new_active_data['new_active_clients'];
$barangay_query = "
    SELECT
        COALESCE(NULLIF(barangay, ''), 'Unknown') AS barangay,
        COUNT(*) AS total_clients
    FROM customer_tbl
    GROUP BY barangay
    ORDER BY total_clients DESC
";
$barangay_result = mysqli_query($conn, $barangay_query);
if (!$barangay_result) {
    die("Barangay query failed: " . mysqli_error($conn));
}
$barangays = [];
$barangay_counts = [];
while ($row = mysqli_fetch_assoc($barangay_result)) {
    $barangays[] = $row['barangay'];
    $barangay_counts[] = (int)$row['total_clients'];
}
$daily_collection_query = "
    SELECT
        DAY(created_at) AS payment_day,
        SUM(amount) AS daily_total
    FROM payment_tbl
    WHERE payment_status = 'Paid'
    AND YEAR(created_at) = YEAR(CURDATE())
    AND MONTH(created_at) = MONTH(CURDATE())
    GROUP BY DAY(created_at)
    ORDER BY DAY(created_at) ASC
";
$daily_collection_result = mysqli_query($conn, $daily_collection_query);
if (!$daily_collection_result) {
    die("Daily collection query failed: " . mysqli_error($conn));
}
$collection_days = [];
$collection_amounts = [];

while ($row = mysqli_fetch_assoc($daily_collection_result)) {
    $collection_days[] = "Day " . $row['payment_day'];
    $collection_amounts[] = (float)$row['daily_total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MITZTIANPC WIRED INTERNET SERVICES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="stylesheet" href="../css/admin_sidebar_topbar_searchbar_profile_icon.css" >
    <link rel="stylesheet" href="../css/admin_dashboard.css?v=dashboard-cards-6">
    <link rel="stylesheet" href="../css/admin_dashboard.css?v=dashboard-layout-11">
    <link rel="stylesheet" href="../css/admin_dashboard.css?v=999999">
</head>
<body>
<?php include 'admin_sidebar_header_profile.php'; ?>

<div class="stats-container">
    <div class="stat">
        <h2><?php echo (int) $total_customers; ?></h2>
        <p>Total Customer</p>
    </div>
    <div class="stat">
        <h2><?php echo (int) $active_users; ?></h2>
        <p>Active User</p>
    </div>
    <div class="stat">
        <h2><?php echo $frozen_accounts; ?></h2>
        <p>Frozen Accounts</p>
    </div>
    <div class="stat">
        <h2><?php echo (int) $pending_applicants; ?></h2>
        <p>Pending Applicants</p>
    </div>
</div>
<div class="dashboard">
    <div class="dashboard-summary">

        <div class="summary-card">
            <h3>Total Collected This Month</h3>
            <div class="summary-value">
                ₱<?php echo number_format((float) $total_collected, 2); ?>
            </div>
            <small>Paid payments this month</small>
        </div>
        <div class="summary-card">
            <h3>Clients With Balance</h3>
            <div class="summary-value">
                <?php echo (int) $clients_with_balance; ?>
            </div>
            <small>Connected clients with remaining balance</small>
        </div>
        <div class="summary-card">
            <h3>New Active Clients</h3>
            <div class="summary-value">
                <?php echo (int) $new_active_clients; ?>
            </div>
            <small>First payment made this month</small>
        </div>
        <div class="summary-card">
            <h3>Inactive Clients</h3>
            <div class="summary-value">
                <?php echo $inactive_clients; ?>
            </div>
            <small>Clients marked as inactive</small>
        </div>
    </div>

<div class="dashboard-charts">
    <div class="chart-card">
        <div class="chart-header">
            <h3>Total Collected</h3>
            <span>This Month</span>
        </div>
        <div class="chart-container">
            <canvas id="collectionChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-header">
            <h3>Client Balance</h3>
            <span>Current Month</span>
        </div>
        <div class="chart-container">
            <canvas id="balanceChart"></canvas>
        </div>
    </div>
    <div class="chart-card full-width">
        <div class="chart-header">
            <h3>Clients Per Barangay</h3>
            <span>Customer Distribution</span>
        </div>
        <div class="chart-container">
            <canvas id="barangayChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const collectionDays = <?php
    echo json_encode($collection_days);
?>;
const collectionAmounts = <?php
    echo json_encode($collection_amounts);
?>;
const barangays = <?php
    echo json_encode($barangays);
?>;
const barangayCounts = <?php
    echo json_encode($barangay_counts);
?>;
const clientsWithBalance = <?php
    echo (int) $clients_with_balance;
?>;
const activeUsers = <?php
    echo (int) $active_users;
?>;
new Chart(document.getElementById('collectionChart'), {
    type: 'bar',
    data: {
        labels: collectionDays,
        datasets: [{
            label: 'Collected (₱)',
            data: collectionAmounts,
            backgroundColor: '#3b82f6',
            borderColor: '#2563eb',
            borderWidth: 1,
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + Number(value).toLocaleString();
                    }
                }
            }
        }
    }
});
new Chart(document.getElementById('balanceChart'), {
    type: 'doughnut',
    data: {
        labels: [
            'With Balance',
            'No Balance'
        ],
        datasets: [{
            data: [
                clientsWithBalance,
                Math.max(activeUsers - clientsWithBalance, 0)
            ],
            backgroundColor: [
                '#f59e0b',
                '#10b981'
            ],

            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
new Chart(document.getElementById('barangayChart'), {
    type: 'bar',
    data: {
        labels: barangays,
        datasets: [{
            label: 'Number of Clients',
            data: barangayCounts,
            backgroundColor: '#6366f1',
            borderColor: '#4f46e5',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});
</script>
</body>
</html>
