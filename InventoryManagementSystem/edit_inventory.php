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

// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the record to be edited
    $sql = "SELECT * FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if record exists
    if ($result->num_rows == 1) {
        $record = $result->fetch_assoc();
    } else {
        echo "Record not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

// Update the record when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grade = $_POST['grade'];
    $quantity = $_POST['quantity'];
    $price_per_kg = $_POST['price_per_kg'];
    $total_cost = $quantity * $price_per_kg;

    // Update query
    $update_sql = "UPDATE inventory SET grade = ?, quantity = ?, price_per_kg = ?, total_cost = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sdddi", $grade, $quantity, $price_per_kg, $total_cost, $id);

    if ($update_stmt->execute()) {
        echo "<script>
                alert('Record updated successfully!');
                window.location.href = 'view_inventory.php';
              </script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventory - Inventory Management</title>
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
            color: #2e7d32;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        form input, form select {
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
            margin-bottom: 1rem;
        }
        .btn-save:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
        }
        .btn-back {
            display: block;
            text-align: center;
            text-decoration: none;
            color: white;
            background-color: #666;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background-color: #555;
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <div class="text-center mb-4">
                <h1 class="welcome-text">Edit Inventory</h1>
                <p class="subtitle">Update inventory item details</p>
            </div>

            <div class="form-container">
                <form method="POST">
                    <div class="mb-3">
                        <label for="grade">Grade:</label>
                        <select name="grade" id="grade" required>
                            <option value="">Select Grade</option>
                            <option value="A" <?= $record['grade'] === 'A' ? 'selected' : '' ?>>Grade A</option>
                            <option value="B" <?= $record['grade'] === 'B' ? 'selected' : '' ?>>Grade B</option>
                            <option value="C" <?= $record['grade'] === 'C' ? 'selected' : '' ?>>Grade C</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity">Quantity (kg):</label>
                        <input type="number" name="quantity" id="quantity" value="<?= htmlspecialchars($record['quantity']) ?>" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="price_per_kg">Price per kg (RM):</label>
                        <input type="number" name="price_per_kg" id="price_per_kg" value="<?= htmlspecialchars($record['price_per_kg']) ?>" step="0.01" required>
                    </div>

                    <button type="submit" class="btn-save">Save Changes</button>
                    <a href="view_inventory.php" class="btn-back">Back to Inventory</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
