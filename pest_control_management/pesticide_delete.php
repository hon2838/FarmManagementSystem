<?php
include 'db.php';

// Get the ID of the pesticide schedule to delete
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID.");
}

// Delete the schedule
$stmt = $pdo->prepare("DELETE FROM pesticide_schedule WHERE id = ?");
$stmt->execute([$id]);

echo "Pesticide schedule deleted successfully!";
header("Location: pesticide_schedule.php");
exit;
?>
