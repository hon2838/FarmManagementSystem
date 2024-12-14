<?php
session_start();
include '../db.php';

function formatCurrency($amount) {
    return "RM " . number_format($amount, 2);
}

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get revenue data
$annual_revenue = $conn->prepare("
    SELECT COALESCE(SUM(price), 0) as total 
    FROM profits 
    WHERE YEAR(record_date) = ?
");
$annual_revenue->bind_param("i", $year);
$annual_revenue->execute();
$total_revenue = $annual_revenue->get_result()->fetch_assoc()['total'];

// Get expenses data
$annual_expenses = $conn->prepare("
    SELECT i.item_name, COALESCE(SUM(e.price), 0) as total 
    FROM expenses e
    JOIN items i ON e.item_id = i.id
    WHERE YEAR(e.date) = ?
    GROUP BY i.item_name
");
$annual_expenses->bind_param("i", $year);
$annual_expenses->execute();
$expenses_result = $annual_expenses->get_result();

// Calculate totals
$total_expenses = 0;
$expenses_by_category = [];
while ($row = $expenses_result->fetch_assoc()) {
    $expenses_by_category[$row['item_name']] = $row['total'];
    $total_expenses += $row['total'];
}

$gross_profit = $total_revenue;
$operating_profit = $gross_profit - $total_expenses;
$net_profit = $operating_profit; // Add other income/expenses if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss Statement <?php echo $year; ?> - Green Farm Livestocks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .print-button { display: block; }
        @media print {
            .print-button { display: none; }
            .container { width: 100%; max-width: none; }
        }
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .profit-positive { color: #198754; }
        .profit-negative { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="text-end mb-4">
            <button onclick="window.print()" class="btn btn-primary print-button">Print Statement</button>
        </div>

        <div class="text-center mb-4">
            <h2>Green Farm Livestocks</h2>
            <h3>Profit & Loss Statement</h3>
            <p>For the Year Ended December 31, <?php echo $year; ?></p>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-borderless">
                    <!-- Revenue Section -->
                    <tr>
                        <td colspan="2"><strong>Revenue</strong></td>
                    </tr>
                    <tr>
                        <td class="ps-4">Sales Revenue</td>
                        <td class="text-end"><?php echo formatCurrency($total_revenue); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td>Gross Profit</td>
                        <td class="text-end"><?php echo formatCurrency($gross_profit); ?></td>
                    </tr>

                    <!-- Operating Expenses Section -->
                    <tr>
                        <td colspan="2" class="pt-3"><strong>Operating Expenses</strong></td>
                    </tr>
                    <?php foreach ($expenses_by_category as $category => $amount): ?>
                    <tr>
                        <td class="ps-4"><?php echo htmlspecialchars($category); ?></td>
                        <td class="text-end"><?php echo formatCurrency($amount); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td>Total Operating Expenses</td>
                        <td class="text-end"><?php echo formatCurrency($total_expenses); ?></td>
                    </tr>

                    <!-- Operating Profit -->
                    <tr class="total-row">
                        <td>Operating Profit</td>
                        <td class="text-end <?php echo $operating_profit >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                            <?php echo formatCurrency($operating_profit); ?>
                        </td>
                    </tr>

                    <!-- Net Profit -->
                    <tr class="total-row">
                        <td><strong>Net Profit</strong></td>
                        <td class="text-end <?php echo $net_profit >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                            <strong><?php echo formatCurrency($net_profit); ?></strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>