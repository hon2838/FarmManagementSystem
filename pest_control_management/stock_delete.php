<?php
include '../db.php';

// Get the ID of the stock item to delete
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID.");
}

// Delete the stock item
$stmt = $pdo->prepare("DELETE FROM stock_management WHERE id = ?");
$stmt->execute([$id]);

echo "Stock item deleted successfully!";
header("Location: stock_management.php");
exit;
?>
