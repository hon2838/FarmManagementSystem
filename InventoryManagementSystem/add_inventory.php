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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grade = $_POST['grade'];
    $quantity = $_POST['quantity'];
    $price_per_kg = $_POST['price_per_kg'];
    
    // Remove total_cost from the INSERT since it's a generated column
    $sql = "INSERT INTO inventory (grade, quantity, price_per_kg, recorded_date) 
            VALUES (?, ?, ?, NOW())";
            
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sid", $grade, $quantity, $price_per_kg);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Inventory added successfully!');
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Inventory - Inventory Management</title>
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
        .content-container {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            gap: 30px;
        }
        .form-section {
            flex: 1;
            background: white;
            padding: 25px;
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
        .price-guide {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .price-guide th, 
        .price-guide td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .price-guide th {
            background-color: #43a047;
            color: white;
            font-weight: 500;
        }
        .price-guide tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .price-guide tr:hover {
            background-color: #f1f1f1;
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn-submit {
            background-color: #43a047;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #388e3c;
            transform: translateY(-2px);
        }
        .btn-back {
            background-color: #666;
            color: white;
            padding: 0.8rem 1.5rem;
            text-decoration: none;
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
            <div class="text-center mb-5">
                <h1 class="welcome-text">Add Inventory</h1>
                <p class="subtitle">Add new items to your inventory</p>
            </div>

            <div class="content-container">
                <div class="form-section">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="grade">Grade:</label>
                            <select name="grade" id="grade" required>
                                <option value="">Select Grade</option>
                                <option value="A">Grade A</option>
                                <option value="B">Grade B</option>
                                <option value="C">Grade C</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quantity">Quantity (kg):</label>
                            <input type="number" name="quantity" id="quantity" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label for="price_per_kg">Price per kg (RM):</label>
                            <input type="number" name="price_per_kg" id="price_per_kg" step="0.01" required>
                        </div>

                        <button type="submit" class="btn-submit w-100 mb-3">Add Inventory</button>
                        <a href="index.php" class="btn-back d-block text-center">Back to Menu</a>
                    </form>
                </div>

                <div class="form-section">
                    <h2 class="text-center mb-4">Price Guide Reference</h2>
                    <table class="price-guide">
                        <thead>
                            <tr>
                                <th>Grade</th>
                                <th>Price Range (RM/kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Grade A</td>
                                <td>RM8.00 - RM10.00</td>
                            </tr>
                            <tr>
                                <td>Grade B</td>
                                <td>RM5.00 - RM7.00</td>
                            </tr>
                            <tr>
                                <td>Grade C</td>
                                <td>RM3.00 - RM4.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
