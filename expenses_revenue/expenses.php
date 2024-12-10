<?php
session_start();
include 'database.php';

// Add this function after database.php include
function formatCurrency($amount) {
    return "RM " . number_format($amount, 2);
}

// Get the latest item ID
$latest_item = $conn->query("
    SELECT item_id 
    FROM items 
    WHERE item_id != '' 
    ORDER BY CAST(SUBSTRING(item_id, 4) AS UNSIGNED) DESC 
    LIMIT 1
")->fetch_assoc();

$next_item_id = '001';
if ($latest_item) {
    // Extract the number from the latest ID and increment it
    $latest_num = intval(substr($latest_item['item_id'], 3));
    $next_item_id = sprintf("%03d", $latest_num + 1);
}
$next_item_id = 'ITM' . $next_item_id;

// Handle item registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_item'])) {
    $item_id = trim($_POST['item_id']);
    $item_name = trim($_POST['item_name']);
    
    // Validate item_id format
    if (!preg_match('/^ITM\d{3}$/', $item_id)) {
        $error = "Invalid item ID format";
    } else {
        $stmt = $conn->prepare("INSERT INTO items (item_id, item_name) VALUES (?, ?)");
        $stmt->bind_param("ss", $item_id, $item_name);
        
        if ($stmt->execute()) {
            $success = "Item registered successfully!";
            
            // Get the next item ID after successful registration
            $latest_item = $conn->query("
                SELECT item_id 
                FROM items 
                WHERE item_id != '' 
                ORDER BY CAST(SUBSTRING(item_id, 4) AS UNSIGNED) DESC 
                LIMIT 1
            ")->fetch_assoc();
            
            if ($latest_item) {
                $latest_num = intval(substr($latest_item['item_id'], 3));
                $next_item_id = sprintf("%03d", $latest_num + 1);
                $next_item_id = 'ITM' . $next_item_id;
            }
            
            // Add JavaScript to update the form
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('item_id').value = '" . $next_item_id . "';
                    document.getElementById('item_name').value = '';
                });
            </script>";
        } else {
            $error = "Error: Item ID or name already exists";
        }
        $stmt->close();
    }
}

// Handle expense recording
if (isset($_POST['record_expense'])) {
    $date = $_POST['date'];
    $item_id = $_POST['item_id'];
    $price = $_POST['price'];
    $recorded_by = $_SESSION['username'];
    
    $stmt = $conn->prepare("INSERT INTO expenses (date, item_id, price, recorded_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $date, $item_id, $price, $recorded_by);
    
    if ($stmt->execute()) {
        $success = "Expense recorded successfully!";
    } else {
        $error = "Error recording expense";
    }
}

// Handle expense deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_expense'])) {
    $expense_id = $_POST['expense_id'];
    
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->bind_param("i", $expense_id);
    
    if ($stmt->execute()) {
        $success = "Expense deleted successfully!";
        // Redirect to refresh the page and prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $error = "Error deleting expense";
    }
    $stmt->close();
}

// Calculate totals
$total_expenses = $conn->query("SELECT SUM(price) AS total FROM expenses")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Management</title>
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
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Item Registration Form -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Register New Item</h5>
                    </div>
                    <!-- Update the item registration form -->
                    <div class="card-body">
                        <form method="POST" id="itemRegistrationForm">
                            <div class="mb-3">
                                <label for="item_id" class="form-label">Item ID</label>
                                <input type="text" class="form-control" id="item_id" name="item_id" 
                                       value="<?php echo htmlspecialchars($next_item_id); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="item_name" class="form-label">Item Name</label>
                                <input type="text" class="form-control" id="item_name" name="item_name" required>
                            </div>
                            <button type="submit" name="register_item" class="btn btn-primary">Register Item</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Record Expense Form -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Record Expense</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="mb-3">
                                <label for="item_id" class="form-label">Item</label>
                                <select class="form-select" id="item_id" name="item_id" required>
                                    <option value="">Select Item</option>
                                    <?php
                                    $items = $conn->query("SELECT * FROM items");
                                    while ($item = $items->fetch_assoc()) {
                                        echo "<option value='{$item['id']}'>{$item['item_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                            </div>
                            <button type="submit" name="record_expense" class="btn btn-success">Record Expense</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenses Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Expense Records</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Recorded By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $expenses = $conn->query("
                            SELECT e.*, i.item_name 
                            FROM expenses e 
                            JOIN items i ON e.item_id = i.id 
                            ORDER BY e.date DESC
                        ");
                        
                        while ($expense = $expenses->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $expense['date']; ?></td>
                            <td><?php echo $expense['item_name']; ?></td>
                            <td><?php echo formatCurrency($expense['price']); ?></td>
                            <td><?php echo $expense['recorded_by']; ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
                                    <button type="submit" name="delete_expense" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Expense Summary</h5>
            </div>
            <div class="card-body">
                <h3>Total Expenses: <?php echo formatCurrency($total_expenses); ?></h3>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Total Expenses by Item</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item ID</th>
                                <th>Item Name</th>
                                <th>Total Expenses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $item_totals = $conn->query("
                                SELECT i.item_id, i.item_name, COALESCE(SUM(e.price), 0) as total_expense
                                FROM items i
                                LEFT JOIN expenses e ON i.id = e.item_id
                                GROUP BY i.id, i.item_id, i.item_name
                                ORDER BY i.item_id
                            ");

                            while ($item = $item_totals->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['item_id']); ?></td>
                                <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                <td><?php echo formatCurrency($item['total_expense']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add this after the existing total summary card -->
        <div class="row mt-4">
            <!-- Monthly Expenses -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Monthly Expenses</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="mb-3">
                            <div class="row">
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
                            </div>
                        </form>
                        <?php
                        if (isset($_GET['month'])) {
                            $month = $_GET['month'];
                            $year = date('Y');
                            $monthly_total = $conn->query("
                                SELECT SUM(price) as total 
                                FROM expenses 
                                WHERE MONTH(date) = $month 
                                AND YEAR(date) = $year
                            ")->fetch_assoc()['total'];
                            
                            echo "<h4>Total for " . date('F', mktime(0, 0, 0, $month, 1)) . ": " 
                                 . formatCurrency($monthly_total ?? 0) . "</h4>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Weekly Expenses -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Weekly Expenses</h5>
                    </div>
                    <div class="card-body">
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
                            </div>
                        </form>
                        <?php
                        if (isset($_GET['start_date'])) {
                            $start_date = $_GET['start_date'];
                            $end_date = date('Y-m-d', strtotime($start_date . ' +6 days')); // Add 6 days to make it a week
                            
                            $weekly_total = $conn->query("
                                SELECT SUM(price) as total 
                                FROM expenses 
                                WHERE date BETWEEN '$start_date' AND '$end_date'
                            ")->fetch_assoc()['total'];
                            
                            echo "<h4>Weekly Total from " . date('M d, Y', strtotime($start_date)) 
                                 . " to " . date('M d, Y', strtotime($end_date)) 
                                 . ": " . formatCurrency($weekly_total ?? 0) . "</h4>";

                            // Daily breakdown for the week
                            $weekly_breakdown = $conn->query("
                                SELECT 
                                    DATE(date) as expense_date,
                                    SUM(price) as daily_total 
                                FROM expenses 
                                WHERE date BETWEEN '$start_date' AND '$end_date'
                                GROUP BY expense_date
                                ORDER BY expense_date ASC
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

                                // Fill in the actual expenses
                                while ($day = $weekly_breakdown->fetch_assoc()) {
                                    $all_dates[$day['expense_date']] = $day['daily_total'];
                                }

                                // Display all dates, including those with no expenses
                                foreach ($all_dates as $date => $amount) {
                                    echo "<tr>
                                            <td>" . date('M d, Y', strtotime($date)) . "</td>
                                            <td>" . formatCurrency($amount) . "</td>
                                          </tr>";
                                }
                                echo "</tbody></table></div>";
                            } else {
                                echo "<div class='alert alert-info mt-3'>No expenses recorded for this week.</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('itemRegistrationForm').addEventListener('submit', function(e) {
        if (this.checkValidity()) {
            setTimeout(() => {
                // Clear the item name field after successful submission
                document.getElementById('item_name').value = '';
            }, 100);
        }
    });
    </script>
</body>
</html>
