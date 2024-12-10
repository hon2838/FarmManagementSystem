<?php
include 'db_connection.php';

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
                window.location.href = 'view_inventory.php';
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
    <title>Add Inventory</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        h2 {
            text-align: center;
            font-size: 1.8em;
            margin-top: 20px;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-size: 1em;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        button {
            background-color: #28a745; /* Green button color */
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838; /* Darker green on hover */
        }
        .view-button {
            display: inline-block;
            width: auto;
            text-align: center;
            background-color: #28a745; /* Green button color */
            color: white;
            text-decoration: none;
            padding: 8px 20px; /* Smaller button size */
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }
        .view-button:hover {
            background-color: #218838; /* Darker green on hover */
        }
    </style>
</head>
<body>
    <h2>Add Limau Kasturi Inventory</h2>
    <form action="add_inventory.php" method="POST">
        <label for="grade">Grade:</label>
        <select name="grade" id="grade" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
        </select>

        <label for="quantity">Quantity (kg):</label>
        <input type="number" name="quantity" id="quantity" required>

        <label for="price_per_kg">Price per kg (RM):</label>
        <input type="number" name="price_per_kg" id="price_per_kg" step="0.50" min="0.50" required>

        <button type="submit">Add Inventory</button>
    </form>

    <div class="button-container">
        <a href="index.php" class="view-button">Go Back to Main Page</a>
        <a href="view_inventory.php" class="view-button">Go to View Inventory</a>
    </div>

    
</body>
</html>
