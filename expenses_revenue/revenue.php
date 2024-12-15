<?php
session_start();
include '../db.php';

function formatCurrency($amount) {
    return "RM " . number_format($amount, 2);
}

// Calculate totals
$total_profit = $conn->query("SELECT SUM(price) AS total FROM profits")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">E&R Tracking</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'expenses.php' ? 'active' : ''; ?>" 
                    href="expenses.php">Expenses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profit.php' ? 'active' : ''; ?>" 
                    href="revenue.php">Revenue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>" 
                    href="report.php">Report</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Remove this entire section -->
        <!-- <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Record Revenue</h5>
                </div>
                <div class="card-body">
                    <form method="POST">...</form>
                </div>
            </div>
        </div> -->

        <!-- Modify the profit summary section to take full width -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Total Revenue Summary</h5>
                </div>
                <div class="card-body">
                    <h3>Total Revenue: <?php echo formatCurrency($total_profit); ?></h3>
                    
                    <!-- Monthly Revenue section -->
                    <div class="mt-4">
                        <h5>Monthly Revenue</h5>
                        <form method="GET" class="row g-3 mb-3">
                            <div class="col-md-8">
                                <select name="month" class="form-select">
                                    <?php
                                    for ($m = 1; $m <= 12; $m++) {
                                        $selected = (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '';
                                        echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Show</button>
                            </div>
                        </form>
                        <?php
                        if (isset($_GET['month'])) {
                            $month = $_GET['month'];
                            $year = date('Y');
                            $monthly_total = $conn->query("
                                SELECT SUM(price) as total 
                                FROM profits 
                                WHERE MONTH(record_date) = $month 
                                AND YEAR(record_date) = $year
                            ")->fetch_assoc()['total'];
                            
                            echo "<h6>Total for " . date('F', mktime(0, 0, 0, $month, 1)) . ": " 
                                 . formatCurrency($monthly_total ?? 0) . "</h6>";
                        }
                        ?>
                    </div>

                    <!-- Weekly Revenue section -->
                    <div class="mt-4">
                        <h5>Weekly Revenue</h5>
                        <form method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" 
                                           value="<?php echo $_GET['start_date'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Show Week</button>
                                </div>
                            </form>
                            <?php
                            if (isset($_GET['start_date'])) {
                                $start_date = $_GET['start_date'];
                                $end_date = date('Y-m-d', strtotime($start_date . ' +6 days')); // Add 6 days to make it a week
                                
                                $weekly_total = $conn->query("
                                    SELECT SUM(price) as total 
                                    FROM profits 
                                    WHERE record_date BETWEEN '$start_date' AND '$end_date'
                                ")->fetch_assoc()['total'];
                                
                                echo "<h4>Weekly Total from " . date('M d, Y', strtotime($start_date)) 
                                     . " to " . date('M d, Y', strtotime($end_date)) 
                                     . ": " . formatCurrency($weekly_total ?? 0) . "</h4>";

                                // Daily breakdown for the week
                                $weekly_breakdown = $conn->query("
                                    SELECT 
                                        DATE(record_date) as profit_date,
                                        SUM(price) as daily_total 
                                    FROM profits 
                                    WHERE record_date BETWEEN '$start_date' AND '$end_date'
                                    GROUP BY profit_date
                                    ORDER BY profit_date ASC
                                ");

                                if ($weekly_breakdown->num_rows > 0) {
                                    echo "<div class='table-responsive mt-3'>
                                            <table class='table table-sm'>
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>";
                                    
                                    // Create an array to store all dates in the week
                                    $all_dates = array();
                                    $current_date = $start_date;
                                    while ($current_date <= $end_date) {
                                        $all_dates[$current_date] = 0;
                                        $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                                    }

                                    // Fill in the actual profits
                                    while ($day = $weekly_breakdown->fetch_assoc()) {
                                        $all_dates[$day['profit_date']] = $day['daily_total'];
                                    }

                                    // Display all dates, including those with no profits
                                    foreach ($all_dates as $date => $amount) {
                                        echo "<tr>
                                                <td>" . date('M d, Y', strtotime($date)) . "</td>
                                                <td>" . formatCurrency($amount) . "</td>
                                              </tr>";
                                    }
                                    echo "</tbody></table></div>";
                                } else {
                                    echo "<div class='alert alert-info mt-3'>No profits recorded for this week.</div>";
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Revenue Records Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Revenue Records</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Customer Name</th>
                                            <th>Date</th>
                                            <th>Order ID</th>
                                            <th>Price</th>
                                            <th>Delivery Address</th>
                                            <th>Recorded By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $profits = $conn->query("
                                            SELECT * FROM profits 
                                            ORDER BY record_date DESC
                                        ");
                                        
                                        while ($profit = $profits->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($profit['customer_name']); ?></td>
                                            <td><?php echo $profit['record_date']; ?></td>
                                            <td><?php echo htmlspecialchars($profit['order_id']); ?></td>
                                            <td><?php echo formatCurrency($profit['price']); ?></td>
                                            <td><?php echo htmlspecialchars($profit['delivery_address']); ?></td>
                                            <td><?php echo htmlspecialchars($profit['recorded_by']); ?></td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="profit_id" value="<?php echo $profit['id']; ?>">
                                                    <button type="submit" name="delete_profit" 
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete this record?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>