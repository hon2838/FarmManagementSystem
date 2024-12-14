<?php
session_start();
include '../db.php';

function formatCurrency($amount) {
    return "RM " . number_format($amount, 2);
}



$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get annual data
$annual_profit = $conn->prepare("
    SELECT COALESCE(SUM(price), 0) as total 
    FROM profits 
    WHERE YEAR(record_date) = ?
");
$annual_profit->bind_param("i", $year);
$annual_profit->execute();
$total_profit = $annual_profit->get_result()->fetch_assoc()['total'];

$annual_expenses = $conn->prepare("
    SELECT COALESCE(SUM(price), 0) as total 
    FROM expenses 
    WHERE YEAR(date) = ?
");
$annual_expenses->bind_param("i", $year);
$annual_expenses->execute();
$total_expenses = $annual_expenses->get_result()->fetch_assoc()['total'];

$net_income = $total_profit - $total_expenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Sheet <?php echo $year; ?> - E&R Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .print-button { display: block; }
        @media print {
            .print-button { display: none; }
            .container { width: 100%; max-width: none; }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="text-end mb-4">
            <button onclick="window.print()" class="btn btn-primary print-button">Print Balance Sheet</button>
        </div>

        <div class="text-center mb-4">
            <h2>Green Farm Livestocks</h2>
            <h3>Balance Sheet</h3>
            <p>For the Year Ended December 31, <?php echo $year; ?></p>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Assets</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td colspan="2"><strong>Current Assets</strong></td>
                            </tr>
                            <tr>
                                <td class="ps-4">Cash and Cash Equivalents</td>
                                <td class="text-end"><?php echo formatCurrency($total_profit); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Assets</strong></td>
                                <td class="text-end border-top"><strong><?php echo formatCurrency($total_profit); ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Liabilities</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td colspan="2"><strong>Current Liabilities</strong></td>
                            </tr>
                            <tr>
                                <td class="ps-4">Accounts Payable</td>
                                <td class="text-end"><?php echo formatCurrency($total_expenses); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Liabilities</strong></td>
                                <td class="text-end border-top"><strong><?php echo formatCurrency($total_expenses); ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Owner's Equity</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="ps-4">Retained Earnings</td>
                                <td class="text-end"><?php echo formatCurrency($net_income); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Owner's Equity</strong></td>
                                <td class="text-end border-top"><strong><?php echo formatCurrency($net_income); ?></strong></td>
                            </tr>
                            <tr>
                                <td><strong>Total Liabilities and Owner's Equity</strong></td>
                                <td class="text-end border-top border-dark">
                                    <strong><?php echo formatCurrency($total_expenses + $net_income); ?></strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>