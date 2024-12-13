<?php
header('Content-Type: application/json');
error_reporting(0); // Disable error reporting for JSON response

// Database connection
$host = "localhost";
$username = "r1";
$password = "";
$database = "farm_management_system";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle deletion
if (isset($_POST['delete_type'])) {
    $type_id = intval($_POST['type_id']);
    
    // Check if type is in use
    $check_sql = "SELECT COUNT(*) as count FROM farm_activities WHERE activity_type IN (SELECT type_name FROM activity_types WHERE id = ?)";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $type_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        die("Cannot delete: This activity type is in use");
    }
    
    // Delete the activity type
    $delete_sql = "DELETE FROM activity_types WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $type_id);
    
    if ($stmt->execute()) {
        echo "Activity type deleted successfully";
    } else {
        die("Error deleting activity type: " . $conn->error);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity_type = trim($_POST['new_activity_type']);
    $description = trim($_POST['activity_description']);
    
    // Validate input
    if (empty($activity_type)) {
        die("Activity type is required");
    }
    
    // Check if activity type already exists
    $check_sql = "SELECT id FROM activity_types WHERE type_name = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $activity_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        die("Activity type already exists");
    }
    
    // Insert new activity type
    $sql = "INSERT INTO activity_types (type_name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $activity_type, $description);
    
    if ($stmt->execute()) {
        echo "Activity type added successfully";
    } else {
        die("Error adding activity type: " . $conn->error);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>