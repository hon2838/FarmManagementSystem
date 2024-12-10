
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Expenses & Revenue Tracking</title>
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
        
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="d-grid gap-3">
                    <button onclick="location.href='expenses.php'" class="btn btn-primary btn-lg">
                        Expenses
                    </button>
                    <button onclick="location.href='profit.php'" class="btn btn-success btn-lg">
                        Profit
                    </button>
                    <button onclick="location.href='report.php'" class="btn btn-info btn-lg">
                        Report
                    </button>
                    <button onclick="location.href='/FarmManagementSystem/dashboard.php'" class="btn btn-secondary btn-lg">
                        Back to Main Dashboard
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
