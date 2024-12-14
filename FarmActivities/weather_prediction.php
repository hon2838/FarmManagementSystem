<?php
include '../db.php';

// Get weather data for the current week
$current_date = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));

$sql = "SELECT `condition` FROM weather_data 
        WHERE date BETWEEN '$week_start' AND '$week_end' 
        ORDER BY date ASC";
$result = $conn->query($sql);

$weather_conditions = [];
while ($row = $result->fetch_assoc()) {
    $weather_conditions[] = strtolower($row['condition']);
}

// Predict fruit production
function predictFruitProduction($weather_conditions) {
    if (empty($weather_conditions)) {
        return ["prediction" => 0, "message" => "No weather data available for this week"];
    }

    $rainy_count = 0;
    $cloudy_count = 0;
    $good_count = 0;

    foreach ($weather_conditions as $condition) {
        if (strpos($condition, 'rain') !== false) {
            $rainy_count++;
        } elseif (strpos($condition, 'cloud') !== false) {
            $cloudy_count++;
        } else {
            $good_count++;
        }
    }

    $total_days = count($weather_conditions);
    
    if ($rainy_count == $total_days) {
        return ["prediction" => 20, "message" => "Rainy weather all week - Expected lower fruit production"];
    } elseif ($rainy_count > 0 && $cloudy_count > 0) {
        return ["prediction" => 30, "message" => "Mixed rainy and cloudy weather - Expected moderate fruit production"];
    } elseif ($cloudy_count == $total_days) {
        return ["prediction" => 40, "message" => "Cloudy weather all week - Expected good fruit production"];
    } else {
        return ["prediction" => 50, "message" => "Good weather conditions - Expected excellent fruit production"];
    }
}

$prediction = predictFruitProduction($weather_conditions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather-based Production Prediction</title>
    <style>
        body {
            font-family: "Lato", sans-serif;
            margin: 0;
            background-color: #e8f5e9;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            border: 2px solid #4caf50;
        }
        h2 {
            color: #2e7d32;
            text-align: center;
        }
        .prediction-box {
            background-color: #f1f8e9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #81c784;
        }
        .weather-list {
            margin: 20px 0;
            padding: 15px;
            background-color: #ffffff;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .back-button {
            display: inline-block;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Weather-based Production Prediction</h2>
        
        <div class="prediction-box">
            <h3>Prediction for Current Week</h3>
            <p>Expected fruit production: <strong><?php echo $prediction['prediction']; ?> kg</strong></p>
            <p><?php echo $prediction['message']; ?></p>
        </div>

        <div class="weather-list">
            <h3>Current Week's Weather Conditions</h3>
            <ul>
                <?php
                if (!empty($weather_conditions)) {
                    foreach ($weather_conditions as $condition) {
                        echo "<li>" . ucfirst($condition) . "</li>";
                    }
                } else {
                    echo "<li>No weather data recorded for this week</li>";
                }
                ?>
            </ul>
        </div>

        <div class="button-container">
            <a href="index.php" class="back-button">Back to Farm Activities</a>
        </div>
    </div>
</body>
</html>