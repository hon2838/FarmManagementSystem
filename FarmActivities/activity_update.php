<?php
session_start(); // Enable sessions for notifications

include '../db.php';

// Handle activity_id passed from the query string to fetch data for editing
if (isset($_GET['activity_id'])) {
    $activity_id = intval($_GET['activity_id']);

    // Fetch the activity data for pre-filling the form
    $sql = "SELECT * FROM farm_activities WHERE activity_id = $activity_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $activity_data = $result->fetch_assoc();
    } else {
        die("Invalid activity ID or no data found.");
    }
} else {
    die("No activity ID provided.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity_type = $_POST['activity_type'];
    $activity_date = $_POST['activity_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $person_responsible = $_POST['person_responsible'];
    $plot_field = $_POST['plot_field'];
    $specific_area = $_POST['specific_area'] ?? null;
    $description = $_POST['description'];
    $material_used = $_POST['material_used'];
    $quantity_used = $_POST['quantity_used'];

    $sql = "UPDATE farm_activities SET 
                activity_type = '$activity_type',
                activity_date = '$activity_date',
                start_time = '$start_time',
                end_time = '$end_time',
                person_responsible = '$person_responsible',
                plot_field = '$plot_field',
                specific_area = '$specific_area',
                description = '$description',
                material_used = '$material_used',
                quantity_used = '$quantity_used'
              WHERE activity_id = $activity_id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Activity updated successfully!";
        header("Location: activity_update.php?activity_id=$activity_id");
        exit();
    } else {
        $_SESSION['error'] = "Error: Could not update activity.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* General styles */
        body {
            font-family: "Lato", sans-serif;
            margin: 0;
            background-color: #e8f5e9;
        }

        /* Main container */
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            padding: 30px;
            border: 2px solid #4caf50;
        }

        h2 {
            text-align: center;
            color: #2e7d32;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Success/Error message notifications */
        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Form field styles */
        form label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #388e3c;
        }

        form input,
        form select,
        form textarea,
        form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        form input:focus,
        form select:focus,
        form textarea:focus {
            border-color: #388e3c;
            box-shadow: 0 0 5px rgba(56, 142, 60, 0.5);
            outline: none;
        }

        form button {
            background-color: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #388e3c;
        }

        /* Button container */
        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .styled-button {
            display: inline-block;
            background-color: #4caf50;
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .styled-button:hover {
            background-color: #388e3c;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        /* Responsive design */
        @media screen and (max-width: 768px) {
            h2 {
                font-size: 20px;
            }

            form button {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Update Activity</h2>

    <!-- Success/Error messages -->
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="message success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php } elseif (isset($_SESSION['error'])) { ?>
        <div class="message error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php } ?>

    <form action="" method="POST">
        <label for="activity_type">Type of Activity</label>
        <select id="activity_type" name="activity_type" required>
            <option value="Watering" <?php echo $activity_data['activity_type'] == 'Watering' ? 'selected' : ''; ?>>Watering</option>
            <option value="Fertilizing" <?php echo $activity_data['activity_type'] == 'Fertilizing' ? 'selected' : ''; ?>>Fertilizing</option>
            <option value="Weeding" <?php echo $activity_data['activity_type'] == 'Weeding' ? 'selected' : ''; ?>>Weeding</option>
            <option value="Planting" <?php echo $activity_data['activity_type'] == 'Planting' ? 'selected' : ''; ?>>Planting</option>
        </select>

        <label for="activity_date">Activity Date</label>
        <input type="date" id="activity_date" name="activity_date" required value="<?php echo $activity_data['activity_date']; ?>">

        <label for="start_time">Start Time</label>
        <input type="time" id="start_time" name="start_time" required value="<?php echo $activity_data['start_time']; ?>">

        <label for="end_time">End Time</label>
        <input type="time" id="end_time" name="end_time" required value="<?php echo $activity_data['end_time']; ?>">

        <label for="person_responsible">Person Responsible</label>
        <input type="text" id="person_responsible" name="person_responsible" placeholder="Enter name of person" required value="<?php echo $activity_data['person_responsible']; ?>">

        <label for="plot_field">Plot/Field</label>
        <select id="plot_field" name="plot_field" required>
            <option value="Plot A" <?php echo $activity_data['plot_field'] == 'Plot A' ? 'selected' : ''; ?>>Plot A</option>
            <option value="Plot B" <?php echo $activity_data['plot_field'] == 'Plot B' ? 'selected' : ''; ?>>Plot B</option>
            <option value="Plot C" <?php echo $activity_data['plot_field'] == 'Plot C' ? 'selected' : ''; ?>>Plot C</option>
        </select>

        <label for="specific_area">Specific Area/Row (Optional)</label>
        <input type="text" id="specific_area" name="specific_area" placeholder="Enter specific area or row" value="<?php echo $activity_data['specific_area']; ?>">

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?php echo $activity_data['description']; ?></textarea>

        <label for="material_used">Materials/Tools Used</label>
        <input type="text" id="material_used" name="material_used" placeholder="E.g., Fertilizer, Hoe, Water" required value="<?php echo $activity_data['material_used']; ?>">

        <label for="quantity_used">Quantity Used</label>
        <input type="number" id="quantity_used" name="quantity_used" placeholder="E.g., 10 kg, 5 liters" required value="<?php echo $activity_data['quantity_used']; ?>">

        <button type="submit">Update Activity</button>
    </form>

    <div class="button-container">
        <a href="activity_list.php" class="styled-button">Back to Activity List</a>
    </div>
</div>

</body>
</html>
