<?php
session_start();
// Check if user is coming from main dashboard or has active session
if (isset($_GET['user'])) {
    $_SESSION['username'] = $_GET['user'];
} elseif (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../header.php';
require_once '../db.php';

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
    <title>Edit Pesticide Schedule - Pest Control Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        }
        .navbar {
            background: rgba(33, 37, 41, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-1px);
        }
        .dropdown-menu {
            background: rgba(33, 37, 41, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .dropdown-item {
            color: rgba(255,255,255,0.85) !important;
            transition: all 0.3s ease;
        }
        .dropdown-item:hover {
            background: rgba(255,255,255,0.1);
            color: #fff !important;
            transform: translateX(5px);
        }
        .main-content {
            padding: 4rem 0;
        }
        .welcome-text {
            font-size: 2.5rem;
            font-weight: 600;
            color: #2e7d32;
            margin-bottom: 1rem;
        }
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        }
        form label {
            font-size: 16px;
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }
        form select, form input, form textarea {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .btn-save {
            background-color: #43a047;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
        }
        .btn-back {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #666;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            margin-top: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-back:hover {
            background-color: #555;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="text-center mb-4">
                <h1 class="welcome-text">Edit Pesticide Schedule</h1>
                <p class="subtitle">Update pesticide application details</p>
            </div>

            <div class="form-container">
                <form method="POST">
                    <div class="mb-3">
                        <label for="pesticide_name">Pesticide Name:</label>
                        <input type="text" id="pesticide_name" name="pesticide_name" value="<?= htmlspecialchars($schedule['pesticide_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="method">Method:</label>
                        <select id="method" name="method" required>
                            <option value="chemical" <?= $schedule['method'] === 'chemical' ? 'selected' : '' ?>>Chemical</option>
                            <option value="organic" <?= $schedule['method'] === 'organic' ? 'selected' : '' ?>>Organic</option>
                            <option value="biological" <?= $schedule['method'] === 'biological' ? 'selected' : '' ?>>Biological</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="application_date">Application Date:</label>
                        <input type="date" id="application_date" name="application_date" value="<?= htmlspecialchars($schedule['application_date']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="reapplication_interval">Reapplication Interval (days):</label>
                        <input type="number" id="reapplication_interval" name="reapplication_interval" value="<?= htmlspecialchars($schedule['reapplication_interval']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="notes">Notes:</label>
                        <textarea id="notes" name="notes" rows="3"><?= htmlspecialchars($schedule['notes']) ?></textarea>
                    </div>

                    <button type="submit" class="btn-save">Save Changes</button>
                    <a href="pesticide_schedule.php" class="btn-back">Back to Schedule</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
