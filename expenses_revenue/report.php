<?php
session_start();
include '../db.php';

function formatCurrency($amount) {
    return "RM " . number_format($amount, 2);
}

// Calculate fiscal metrics for selected year
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Calculate total revenue for selected year
$total_revenue = $conn->prepare("
    SELECT COALESCE(SUM(price), 0) AS total 
    FROM profits 
    WHERE YEAR(record_date) = ?
");
$total_revenue->bind_param("i", $year);
$total_revenue->execute();
$total_revenue = $total_revenue->get_result()->fetch_assoc()['total'];

// Calculate total expenditure
$total_expenditure = $conn->prepare("
    SELECT COALESCE(SUM(price), 0) AS total 
    FROM expenses 
    WHERE YEAR(date) = ?
");
$total_expenditure->bind_param("i", $year);
$total_expenditure->execute();
$total_expenditure = $total_expenditure->get_result()->fetch_assoc()['total'];

// Calculate net income
$net_income = $total_revenue - $total_expenditure;

// Calculate monthly financial data
$monthly_data = $conn->prepare("
    SELECT 
        MONTH(record_date) as month,
        COALESCE(SUM(price), 0) as revenue
    FROM profits 
    WHERE YEAR(record_date) = ?
    GROUP BY MONTH(record_date)
    ORDER BY month
");

$monthly_expenses = $conn->prepare("
    SELECT 
        MONTH(date) as month,
        COALESCE(SUM(price), 0) as expenditure
    FROM expenses 
    WHERE YEAR(date) = ?
    GROUP BY MONTH(date)
    ORDER BY month
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report <?php echo $year; ?></title>
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
        <h2>Annual Financial Report <?php echo $year; ?></h2>
        
        <div class="summary-cards">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <p class="card-text"><?php echo formatCurrency($total_revenue); ?></p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Expenditure</h5>
                    <p class="card-text"><?php echo formatCurrency($total_expenditure); ?></p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Net Income</h5>
                    <p class="card-text <?php echo $net_income >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <?php echo formatCurrency($net_income); ?>
                    </p>
                </div>
            </div>
        </div>


        <!-- Yearly Summary Card -->
        <div class="card mt-2">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Monthly Breakdown</h5>
                <a href="pnlstmt.php?year=<?php echo $year; ?>" 
                   class="btn btn-primary" target="_blank">
                    Generate Profit and Loss Statement
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
                        // Get monthly Revenue
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
                                    <th>Revenue</th>
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

        <div class="chart-container mt-4">
            <h3>Financial Performance Trends</h3>
            <canvas id="financialChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
