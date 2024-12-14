<?php
include '../db.php';

// Fetch daily inventory records
$sql = "SELECT * FROM inventory ORDER BY recorded_date DESC";
$result = $conn->query($sql);

// Fetch total inventory by grade (accumulated total)
$sql_totals = "SELECT grade, SUM(quantity) AS total_quantity 
               FROM inventory 
               GROUP BY grade 
               ORDER BY grade ASC";
$totals = $conn->query($sql_totals);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        h2 {
            text-align: center;
            font-size: 1.8em;
            margin-top: 20px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
            text-align: center;
            padding: 10px;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        td {
            background-color: white;
        }
        .button-container {
            text-align: center;
            margin-top: 30px;
        }
        .action-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745; /* Green button */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px;
            transition: background-color 0.3s;
        }
        .action-button:hover {
            background-color: #218838; /* Darker green on hover */
        }
        .view-button {
            display: inline-block;
            text-align: center;
            background-color: #28a745; /* Green button */
            color: white;
            text-decoration: none;
            padding: 8px 20px; /* Smaller button size */
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
        }
        .view-button:hover {
            background-color: #218838; /* Darker green on hover */
        }
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            font-size: 0.9em;
        }
        .edit-btn {
            background-color: #007bff; /* Blue button */
        }
        .edit-btn:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .delete-btn {
            background-color: #dc3545; /* Red button */
        }
        .delete-btn:hover {
            background-color: #c82333; /* Darker red on hover */
        }
        .filter-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .filter-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filter-form select, 
        .filter-form input[type="date"] {
            padding: 8px;
            border: 1px solid #28a745;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-form button {
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #218838;
        }

        /* Update existing styles and add centered heading */
        h3 {
            text-align: center;
            color: #1b5e20;
            font-size: 20px;
            margin: 30px 0 20px;
            font-weight: bold;
        }

        /* Keep existing table and other styles */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        /* Rest of your existing styles... */
    </style>
</head>
<body>
    <h2>Limau Kasturi Inventory Records</h2>

    <!-- Filter Forms -->
    <div class="filter-container">
        <!-- Monthly Filter -->
        <form method="GET" class="filter-form">
            <select name="month" required>
                <option value="">Select Month</option>
                <?php 
                for ($m = 1; $m <= 12; $m++) {
                    $selected = (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '';
                    echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                }
                ?>
            </select>
            <select name="year" required>
                <option value="">Select Year</option>
                <?php 
                $current_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
                for ($year = 2024; $year <= 2040; $year++) {
                    $selected = ($current_year == $year) ? 'selected' : '';
                    echo "<option value='$year' $selected>$year</option>";
                }
                ?>
            </select>
            <button type="submit" name="filter_monthly">Filter by Month</button>
        </form>

        <!-- Weekly Filter -->
        <form method="GET" class="filter-form">
            <label>Week Starting From:</label>
            <input type="date" name="week_start" 
                   value="<?php echo isset($_GET['week_start']) ? $_GET['week_start'] : date('Y-m-d'); ?>"
                   max="<?php echo date('Y-m-d'); ?>" 
                   required>
            <button type="submit" name="filter_weekly">Filter by Week</button>
        </form>
    </div>

    <?php
    // Modify the SQL queries based on filters
    if (isset($_GET['month']) && isset($_GET['year'])) {
        $month = $_GET['month'];
        $year = $_GET['year'];
        $sql = "SELECT * FROM inventory 
                WHERE MONTH(recorded_date) = $month 
                AND YEAR(recorded_date) = $year 
                ORDER BY recorded_date DESC";
        $sql_totals = "SELECT grade, SUM(quantity) AS total_quantity 
                       FROM inventory 
                       WHERE MONTH(recorded_date) = $month 
                       AND YEAR(recorded_date) = $year 
                       GROUP BY grade 
                       ORDER BY grade ASC";
    } elseif (isset($_GET['week_start'])) {
        $week_start = $_GET['week_start'];
        $week_end = date('Y-m-d', strtotime($week_start . ' +6 days'));
        $sql = "SELECT * FROM inventory 
                WHERE recorded_date BETWEEN '$week_start' AND '$week_end' 
                ORDER BY recorded_date DESC";
        $sql_totals = "SELECT grade, SUM(quantity) AS total_quantity 
                       FROM inventory 
                       WHERE recorded_date BETWEEN '$week_start' AND '$week_end' 
                       GROUP BY grade 
                       ORDER BY grade ASC";
    }

    // Update result sets
    $result = $conn->query($sql);
    $totals = $conn->query($sql_totals);
    ?>

    <div style="margin-top: 40px;">
        <!-- Total Inventory by Grade -->
        <h3>Total Inventory by Grade</h3>
        <table>
            <thead>
                <tr>
                    <th>Grade</th>
                    <th>Total Quantity (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($totals->num_rows > 0) {
                    while ($row = $totals->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['grade']}</td>
                                <td>{$row['total_quantity']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No inventory found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 40px;">
        <!-- Daily Inventory Records -->
        <h3>Daily Inventory Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Grade</th>
                    <th>Quantity (kg)</th>
                    <th>Price per kg (RM)</th>
                    <th>Total Cost (RM)</th>
                    <th>Recorded Date</th>
                    <th>Actions</th> <!-- Actions Column -->
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['grade']}</td>
                                <td>{$row['quantity']}</td>
                                <td>{$row['price_per_kg']}</td>
                                <td>{$row['total_cost']}</td>
                                <td>{$row['recorded_date']}</td>
                                <td>
                                    <a href='edit_inventory.php?id={$row['id']}' class='edit-btn'>Edit</a>
                                    <a href='delete_inventory.php?id={$row['id']}' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Button Container -->
    <div class="button-container">
        <a href="index.php" class="action-button">Go Back to Main Page</a>
        <a href="add_inventory.php" class="action-button">Go Back to Add Inventory</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
