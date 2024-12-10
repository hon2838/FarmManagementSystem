<?php
include 'db_connection.php';

// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Delete query
    $sql = "DELETE FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Record deleted successfully!');
                window.location.href = 'view_inventory.php';
              </script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
    exit;
}

$conn->close();
?>

