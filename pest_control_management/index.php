<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pest Control Management</title>
    <style>
        /* Global Styles */
        body {
            font-family: "Lato", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e8f5e9; /* Light green background */
            color: #2e7d32;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Header Styling */
        header {
            width: 100%;
            background-color: #66bb6a; /* Green header */
            color: white;
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        }
        header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Main Container */
        main {
            margin: 3rem 0;
            max-width: 900px;
            width: 90%;
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        main h2 {
            color: #2e7d32;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Menu Links */
        .menu {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .menu a {
            text-decoration: none;
            color: white;
            background-color: #43a047; /* Button color */
            padding: 1rem 2.5rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        .menu a:hover {
            background-color: #388e3c; /* Darker green on hover */
            transform: translateY(-4px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }
        .menu a:active {
            transform: translateY(2px);
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <header>
        <h1>Pest Control Management System</h1>
    </header>
    <main>
        <h2>Welcome, Farmer!</h2>
        <div class="menu">
            <a href="pesticide_schedule.php">Pesticide Scheduling</a>
            <a href="stock_management.php">Stock Management</a>
            <a href="view_stock.php">View Stock Available</a>
            <a href="/FarmManagementSystem/dashboard.php" class="button">Main Page</a>
        </div>
    </main>
</body>
</html>
