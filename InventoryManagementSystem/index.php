<?php
session_start();

// Check if user is coming from main dashboard or has active session
if (isset($_GET['user'])) {
    $_SESSION['username'] = $_GET['user'];
} elseif (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../db.php';

$error = '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limau Kasturi Inventory System</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            background-image: url('limau-kasturi-background.jpg'); /* Background Image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #333;
        }
        header {
            background-color: rgba(40, 167, 69, 0.9); /* Green color with transparency */
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 2em;
        }
        header h1 {
            margin: 0;
            font-size: 2.2em;
            font-weight: bold;
            text-transform: uppercase;
        }
        .company-name {
            font-size: 1.2em;
            font-weight: normal;
            font-style: italic;
            margin-top: 5px;
            color: #fff;
        }
        .container {
            text-align: center;
            padding: 30px 20px;
        }
        .container h2 {
            font-size: 1.6em;
            color: #333;
            margin-bottom: 15px;
        }
        .container p {
            font-size: 1em;
            color: #555;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px; /* Smaller padding */
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px; /* Smaller font */
            font-weight: bold;
            transition: background-color 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 200px;
        }
        .button:hover {
            background-color: #218838;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .button:active {
            background-color: #1e7e34;
        }
        .button-container {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }
        .lime-icon {
            width: 30px;
            height: 30px;
            margin: 5px;
            object-fit: contain;
        }
        @media (max-width: 768px) {
            header h1 {
                font-size: 1.8em;
            }
            .company-name {
                font-size: 1em;
            }
            .button {
                width: 180px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Green Farm Livestocks</h1> <!-- Company name at the top -->
        <p class="company-name">Limau Kasturi Inventory System</p> <!-- Subheading for the system -->
    </header>
    <div class="container">
        <h2>Welcome to the Limau Kasturi Inventory Management System</h2>
        <p>Select an option below to manage your inventory:</p>
        
        <!-- Buttons for inventory actions -->
        <div class="button-container">
            <a href="add_inventory.php" class="button">Add Inventory</a>
            <a href="view_inventory.php" class="button">View Inventory</a>
            <a href="/FarmManagementSystem/dashboard.php" class="button">Main Page</a>

        </div>
    </div>
</body>
</html>



