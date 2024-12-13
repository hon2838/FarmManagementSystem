<?php
// Database connection
$host = "localhost";
$username = "r1";
$password = "1234";
$database = "farm_management_system";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the activity ID is provided
if (isset($_GET['activity_id'])) {
    $activity_id = intval($_GET['activity_id']);

    // SQL to delete the record
    $sql = "DELETE FROM farm_activities WHERE activity_id = $activity_id";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Activity deleted successfully!</p>";
    } else {
        echo "<p>Error deleting record: " . $conn->error . "</p>";
    }
} else {
    echo "<p>Invalid request!</p>";
}

echo "<a href='activity_list.php'>Back to Activity List</a>";
$conn->close();
?>
