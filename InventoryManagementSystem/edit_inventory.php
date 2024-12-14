<?php
include '../db.php';

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
    <title>Edit Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            text-align: center;
        }
        .form-container {
            width: 50%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        input {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 1em;
        }
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        a {
            text-decoration: none;
            color: #007bff;
            display: block;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Inventory Record</h2>
        <form method="POST">
            <label>Grade:</label>
            <input type="text" name="grade" value="<?php echo $record['grade']; ?>" required>
            
            <label>Quantity (kg):</label>
            <input type="number" name="quantity" value="<?php echo $record['quantity']; ?>" step="0.01" required>
            
            <label>Price per kg (RM):</label>
            <input type="number" name="price_per_kg" value="<?php echo $record['price_per_kg']; ?>" step="0.01" required>
            
            <button type="submit">Update Record</button>
        </form>
        <a href="view_inventory.php">Back to Inventory</a>
    </div>
</body>
</html>
