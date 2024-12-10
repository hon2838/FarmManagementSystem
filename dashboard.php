<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Main Page - Limau Kasturi</title>
        <style>
            body {
                font-family: "Lato", sans-serif;
                margin: 0;
                background-color: #e8f5e9;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
                color: #2e7d32;
            }
            h1 {
                font-size: 48px;
                font-weight: bold;
                color: #2e7d32;
                margin-bottom: 20px;
                text-shadow: 1px 1px 4px #a5d6a7;
            }
            p {
                font-size: 18px;
                margin-bottom: 40px;
                color: #4caf50;
            }
            .button-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
                max-width: 800px;
                margin: 0 auto;
            }
            .button {
                display: inline-block;
                padding: 15px 40px;
                font-size: 18px;
                font-weight: bold;
                color: #ffffff;
                background-color: #66bb6a;
                text-decoration: none;
                border-radius: 10px;
                border: 2px solid #43a047;
                transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .button:hover {
                background-color: #43a047;
                transform: translateY(-3px);
                box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
            }
            .button:active {
                transform: translateY(1px);
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            }
            footer {
                position: absolute;
                bottom: 20px;
                text-align: center;
                font-size: 14px;
                color: #388e3c;
            }
        </style>
    </head>
    <body>
        <h1>Limau Kasturi</h1>
        <h2>Green Farm Livestocks</h2>
        <p>Welcome to the Limau Kasturi management system. Please select an option below:</p>
        <div class="button-container">
            <a href="FarmActivities/index.php" class="button">Farm Activity</a>
            <a href="pest_control_management/index.php" class="button">Pest Control</a>
            <a href="InventoryManagementSystem/index.php" class="button">Inventory Management System</a>
            <a href="limau_kasturi_orders/index.php" class="button">Sales</a>
            <a href="expenses_revenue/dashboard.php" class="button">Expenses and Revenue</a>
        </div>
        <footer>&copy; 2024 Limau Kasturi Management System</footer>
    </body>
</html>
