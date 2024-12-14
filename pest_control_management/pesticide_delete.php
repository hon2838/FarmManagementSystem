<?php
include '../db.php';

// Get the ID of the pesticide schedule to delete
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID.");
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // First get the schedule details before deleting
    $stmt = $pdo->prepare("
        SELECT pesticide_name, quantity_used 
        FROM pesticide_schedule 
        WHERE id = ?
    ");
    $stmt->execute([$id]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($schedule) {
        // Return the quantity back to stock
        $update_stmt = $pdo->prepare("
            UPDATE stock_management 
            SET quantity = quantity + ?, 
                last_updated = CURRENT_DATE 
            WHERE item_name = ?
        ");
        $update_stmt->execute([$schedule['quantity_used'], $schedule['pesticide_name']]);
        
        // Delete the schedule
        $delete_stmt = $pdo->prepare("DELETE FROM pesticide_schedule WHERE id = ?");
        $delete_stmt->execute([$id]);
        
        $pdo->commit();
        echo "Pesticide schedule deleted and stock updated successfully!";
    } else {
        throw new Exception("Schedule not found");
    }
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error deleting schedule: " . $e->getMessage();
}

header("Location: pesticide_schedule.php");
exit;
?>
