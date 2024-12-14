<?php
// Database connection
include '../db.php';

// Check if the activity ID is provided
if (isset($_GET['activity_id'])) {
    $activity_id = intval($_GET['activity_id']);

    // SQL to delete the record
    $sql = "DELETE FROM farm_activities WHERE activity_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $activity_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid request!";
}

$conn->close();
?>
