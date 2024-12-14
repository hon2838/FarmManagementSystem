<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // First get the stock history record details before deleting
        $stmt = $pdo->prepare("
            SELECT sh.item_id, sh.quantity, sh.action 
            FROM stock_history sh 
            WHERE sh.id = ?
        ");
        $stmt->execute([$id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($record) {
            // Undo the stock modification
            // If action was 'Added', we subtract the quantity
            $quantity_change = -$record['quantity']; // Negative to undo addition
            
            // Update the stock_management table
            $update_stmt = $pdo->prepare("
                UPDATE stock_management 
                SET quantity = quantity + ?, 
                    last_updated = CURRENT_DATE 
                WHERE id = ?
            ");
            $update_stmt->execute([$quantity_change, $record['item_id']]);
            
            // Delete the history record
            $delete_stmt = $pdo->prepare("DELETE FROM stock_history WHERE id = ?");
            $delete_stmt->execute([$id]);
            
            $pdo->commit();
            echo "Record deleted successfully";
        } else {
            throw new Exception("Record not found");
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error deleting record: " . $e->getMessage();
    }
} else {
    echo "Invalid request";
}
?>