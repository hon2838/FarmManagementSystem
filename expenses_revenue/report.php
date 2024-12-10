<?php
session_start();
include 'database.php';

function formatCurrency($amount) {
    return "RM " . number_format($amount, 2);
}



// Update the totals calculation to be year-specific
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Calculate totals for selected year
$total_profit = $conn->prepare("
    SELECT COALESCE(SUM(price), 0) AS total 
    FROM profits 
    WHERE YEAR(record_date) = ?
");
$total_profit->bind_param("i", $year);
$total_profit->execute();
$total_profit = $total_profit->get_result()->fetch_assoc()['total'];

$total_expenses = $conn->prepare("
    SELECT COALESCE(SUM(price), 0) AS total 
    FROM expenses 
    WHERE YEAR(date) = ?
");
$total_expenses->bind_param("i", $year);
$total_expenses->execute();
$total_expenses = $total_expenses->get_result()->fetch_assoc()['total'];

$revenue_loss = $total_profit - $total_expenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report - Expenses & Revenue Tracking</title>
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
                            href="profit.php">Profit</a>
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
        <!-- Overall Summary Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Overall Summary for <?php echo $year; ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Profit</h6>
                                <h4><?php echo formatCurrency($total_profit); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Expenses</h6>
                                <h4><?php echo formatCurrency($total_expenses); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card <?php echo $revenue_loss >= 0 ? 'bg-success' : 'bg-warning'; ?> text-white">
                            <div class="card-body">
                                <h6 class="card-title"><?php echo $revenue_loss >= 0 ? 'Total Revenue' : 'Total Loss'; ?></h6>
                                <h4><?php echo formatCurrency(abs($revenue_loss)); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yearly Summary Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Monthly Breakdown</h5>
                <a href="balance_sheet.php?year=<?php echo $year; ?>" 
                   class="btn btn-primary" target="_blank">
                    Generate Balance Sheet
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="year" class="form-label">Select Year</label>
                            <input type="number" class="form-control" id="year" name="year" 
                                   value="<?php echo $year; ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Show</button>
                        </div>
                    </div>
                </form>

                <?php
                if (isset($_GET['year'])) {
                    $year = intval($_GET['year']);
                    
                    // Get monthly data
                    $monthly_data = array();
                    for ($month = 1; $month <= 12; $month++) {
                        // Get monthly profit
                        $profit_query = $conn->prepare("
                            SELECT COALESCE(SUM(price), 0) AS monthly_profit 
                            FROM profits 
                            WHERE YEAR(record_date) = ? AND MONTH(record_date) = ?
                        ");
                        $profit_query->bind_param("ii", $year, $month);
                        $profit_query->execute();
                        $monthly_profit = $profit_query->get_result()->fetch_assoc()['monthly_profit'];

                        // Get monthly expenses
                        $expense_query = $conn->prepare("
                            SELECT COALESCE(SUM(price), 0) AS monthly_expenses 
                            FROM expenses 
                            WHERE YEAR(date) = ? AND MONTH(date) = ?
                        ");
                        $expense_query->bind_param("ii", $year, $month);
                        $expense_query->execute();
                        $monthly_expenses = $expense_query->get_result()->fetch_assoc()['monthly_expenses'];

                        $balance = $monthly_profit - $monthly_expenses;
                        $monthly_data[$month] = array(
                            'profit' => $monthly_profit,
                            'expenses' => $monthly_expenses,
                            'balance' => $balance
                        );
                    }
                    ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Profit</th>
                                    <th>Expenses</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($monthly_data as $month => $data) {
                                    $month_name = date('F', mktime(0, 0, 0, $month, 1));
                                    $balance_class = $data['balance'] >= 0 ? 'text-success' : 'text-danger';
                                    echo "<tr>
                                            <td>{$month_name}</td>
                                            <td>" . formatCurrency($data['profit']) . "</td>
                                            <td>" . formatCurrency($data['expenses']) . "</td>
                                            <td class='{$balance_class}'>" . formatCurrency($data['balance']) . "</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
