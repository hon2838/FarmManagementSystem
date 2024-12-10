<?php
include 'db.php';

// Get the ID of the pesticide schedule to edit
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid ID.");
}

// Fetch the schedule data
$stmt = $pdo->prepare("SELECT * FROM pesticide_schedule WHERE id = ?");
$stmt->execute([$id]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    die("Schedule not found.");
}

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesticide_name = $_POST['pesticide_name'];
    $method = $_POST['method'];
    $application_date = $_POST['application_date'];
    $reapplication_interval = $_POST['reapplication_interval'];
    $next_application_date = date('Y-m-d', strtotime($application_date . " + $reapplication_interval days"));
    $notes = $_POST['notes'];

    $update_stmt = $pdo->prepare("UPDATE pesticide_schedule SET pesticide_name = ?, method = ?, application_date = ?, reapplication_interval = ?, next_application_date = ?, notes = ? WHERE id = ?");
    $update_stmt->execute([$pesticide_name, $method, $application_date, $reapplication_interval, $next_application_date, $notes, $id]);

    echo "Pesticide schedule updated successfully!";
    header("Location: pesticide_schedule.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesticide Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f6fff2; /* Light green background */
            color: #333;
        }
        header {
            background-color: #82b74b; /* Green header */
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
        main {
            margin: 2rem auto;
            max-width: 800px;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #4a7c23;
        }
        form {
            display: grid;
            gap: 1rem;
        }
        form label {
            font-weight: bold;
            color: #4a7c23;
        }
        form input, form select, form textarea, form button {
            padding: 0.5rem;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            font-size: 1rem;
        }
        form button {
            background-color: #4a7c23;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        form button:hover {
            background-color: #3b621a;
        }
    </style>
</head>
<body>
    <header>
        <h1>Edit Pesticide Schedule</h1>
    </header>
    <main>
        <form method="POST">
            <label for="pesticide_name">Pesticide Name:</label>
            <input type="text" id="pesticide_name" name="pesticide_name" value="<?= htmlspecialchars($schedule['pesticide_name']) ?>" required>

            <label for="method">Method:</label>
            <select id="method" name="method" required>
                <option value="chemical" <?= $schedule['method'] === 'chemical' ? 'selected' : '' ?>>Chemical</option>
                <option value="organic" <?= $schedule['method'] === 'organic' ? 'selected' : '' ?>>Organic</option>
                <option value="biological" <?= $schedule['method'] === 'biological' ? 'selected' : '' ?>>Biological</option>
            </select>

            <label for="application_date">Application Date:</label>
            <input type="date" id="application_date" name="application_date" value="<?= htmlspecialchars($schedule['application_date']) ?>" required>

            <label for="reapplication_interval">Reapplication Interval (days):</label>
            <input type="number" id="reapplication_interval" name="reapplication_interval" value="<?= htmlspecialchars($schedule['reapplication_interval']) ?>" required>

            <label for="notes">Notes:</label>
            <textarea id="notes" name="notes" rows="3"><?= htmlspecialchars($schedule['notes']) ?></textarea>

            <button type="submit">Update Schedule</button>
        </form>
    </main>
</body>
</html>
