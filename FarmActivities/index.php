<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Farm Activities System</title>
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
                font-size: 36px;
                font-weight: bold;
                color: #2e7d32;
                margin-bottom: 30px;
                text-shadow: 1px 1px 4px #a5d6a7;
            }
            .button-container {
                display: flex;
                flex-direction: column;
                gap: 15px;
                max-width: 300px;
                width: 100%;
            }
            .button {
                display: inline-block;
                padding: 15px;
                font-size: 18px;
                font-weight: bold;
                color: #ffffff;
                background-color: #66bb6a;
                text-decoration: none;
                border-radius: 8px;
                border: 2px solid #43a047;
                text-align: center;
                transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                width: 100%;
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
        </style>
    </head>
    <body>
        <h1>Farm Activities System</h1>
        <div class="button-container">
            <a href="weather.php" class="button">Weather</a>
            <a href="schedule_activity.php" class="button">Schedule Activity</a>
            <a href="activity_list.php" class="button">Activity List</a>
            <a href="weather_prediction.php" class="button">Production Prediction</a>
            <a href="/FarmManagementSystem/dashboard.php" class="button">Main Page</a>
        </div>
    </body>
</html>
