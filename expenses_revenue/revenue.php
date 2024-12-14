<?php
session_start();
include '../db.php';

// Add this function after database.php include
function formatCurrency($amount) {
    return "RM " . number_format($amount, 2);
}

// Get the latest order ID
$latest_order = $conn->query("
    SELECT order_id 
    FROM profits 
    WHERE order_id != '' 
    ORDER BY CAST(SUBSTRING(order_id, 4) AS UNSIGNED) DESC 
    LIMIT 1
")->fetch_assoc();

$next_order_id = '001';
if ($latest_order) {
    // Extract the number from the latest ID and increment it
    $latest_num = intval(substr($latest_order['order_id'], 3));
    $next_order_id = sprintf("%03d", $latest_num + 1);
}
$next_order_id = 'ORD' . $next_order_id;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['record_profit'])) {
        $customer_name = trim($_POST['customer_name']);
        $record_date = $_POST['record_date'];
        $order_id = trim($_POST['order_id']);
        $price = $_POST['price'];
        $delivery_address = trim($_POST['delivery_address']);
        $recorded_by = $_SESSION['username'];

        // Validate order_id format
        if (!preg_match('/^ORD\d{3}$/', $order_id)) {
            $error = "Invalid order ID format";
        } else {
            $stmt = $conn->prepare("INSERT INTO profits (customer_name, record_date, order_id, price, delivery_address, recorded_by) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdss", $customer_name, $record_date, $order_id, $price, $delivery_address, $recorded_by);

            if ($stmt->execute()) {
                $success = "Profit recorded successfully!";
                
                // Get the next order ID after successful registration
                $latest_order = $conn->query("
                    SELECT order_id 
                    FROM profits 
                    WHERE order_id != '' 
                    ORDER BY CAST(SUBSTRING(order_id, 4) AS UNSIGNED) DESC 
                    LIMIT 1
                ")->fetch_assoc();
                
                if ($latest_order) {
                    $latest_num = intval(substr($latest_order['order_id'], 3));
                    $next_order_id = sprintf("%03d", $latest_num + 1);
                    $next_order_id = 'ORD' . $next_order_id;
                }
                
                // Add JavaScript to update the form
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('order_id').value = '" . $next_order_id . "';
                        document.getElementById('customer_name').value = '';
                        document.getElementById('record_date').value = '';
                        document.getElementById('price').value = '';
                        document.getElementById('delivery_address').value = '';
                    });
                </script>";
            } else {
                $error = "Error recording profit.";
            }
            $stmt->close();
        }
    }

    // Handle delete
    if (isset($_POST['delete_profit'])) {
        $profit_id = $_POST['profit_id'];
        $stmt = $conn->prepare("DELETE FROM profits WHERE id = ?");
        $stmt->bind_param("i", $profit_id);
        
        if ($stmt->execute()) {
            $success = "Record deleted successfully!";
        } else {
            $error = "Error deleting record.";
        }
        $stmt->close();
    }
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
        <!-- Record Profit Form -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Record Revenue</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="record_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="record_date" name="record_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="order_id" class="form-label">Order ID</label>
                            <input type="text" class="form-control" id="order_id" name="order_id" 
                                   value="<?php echo htmlspecialchars($next_order_id); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="delivery_address" class="form-label">Delivery Address</label>
                            <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="record_profit" class="btn btn-primary w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Profit Summary -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Total Revenue Summary</h5>
                </div>
                <div class="card-body">
                    <h3>Total Revenue: <?php echo formatCurrency($total_profit); ?></h3>
                    
                    <!-- Monthly Profit -->
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

                    <!-- Weekly Profit -->
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
                </div>

                <!-- Profit Records Table -->
                <div class="card">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial order ID when page loads
        document.getElementById('order_id').value = '<?php echo $next_order_id; ?>';
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        if (this.checkValidity()) {
            setTimeout(() => {
                // Clear all fields except order_id after successful submission
                document.getElementById('customer_name').value = '';
                document.getElementById('record_date').value = '';
                document.getElementById('price').value = '';
                document.getElementById('delivery_address').value = '';
            }, 100);
        }
    });
    </script>
</body>
</html>