<?php
// Include the database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $delivery_address = $_POST['delivery_address'];

    $sql = "INSERT INTO customers (customer_name, customer_phone, delivery_address) 
            VALUES ('$customer_name', '$customer_phone', '$delivery_address')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Customer registered successfully.</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Customer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0fff0;
            color: #006400;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #228B22;
        }
        form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #e8ffe8;
            border: 1px solid #c3e6c3;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 128, 0, 0.2);
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #006400;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #c3e6c3;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #32CD32;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #228B22;
        }
        .back-button {
            text-align: center;
            margin-top: 20px;
        }
        .back-button a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FF6347;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-button a:hover {
            background-color: #CD5C5C;
        }
    </style>
</head>
<body>
    <h1>Register New Customer</h1>
    <form method="POST">
        <label for="customer_name">Customer Name:</label>
        <input type="text" id="customer_name" name="customer_name" required>

        <label for="customer_phone">Phone Number:</label>
        <input type="text" id="customer_phone" name="customer_phone" required>

        <label for="delivery_address">Delivery Address:</label>
        <textarea id="delivery_address" name="delivery_address" required></textarea>

        <button type="submit">Register</button>
    </form>

    <div class="back-button">
        <a href="index.php">Back to Menu</a>
    </div>
</body>
</html>
