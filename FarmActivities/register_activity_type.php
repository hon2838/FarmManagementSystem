<?php
// Database connection
$host = "localhost";
$username = "r1";
$password = "";
$database = "farm_management_system";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity_type = trim($_POST['new_activity_type']);
    $description = trim($_POST['activity_description']);
    
    // Validate input
    if (empty($activity_type)) {
        echo json_encode(['success' => false, 'message' => 'Activity type is required']);
        exit;
    }
    
    // Check if activity type already exists
    $check_sql = "SELECT id FROM activity_types WHERE type_name = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $activity_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Activity type already exists']);
        exit;
    }
    
    // Insert new activity type
    $sql = "INSERT INTO activity_types (type_name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $activity_type, $description);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>