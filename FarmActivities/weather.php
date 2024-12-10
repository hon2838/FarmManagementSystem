<?php
// Database connection
$host = "localhost";
$username = "r1";
$password = "";
$database = "farm_management_system";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the default timezone to Asia/Kuala_Lumpur
date_default_timezone_set('Asia/Kuala_Lumpur');

// Handle save current weather logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_weather'])) {
    $current_date = date("Y-m-d"); // Save date in Y-m-d format
    $current_temp = $_POST['temperature'];
    $current_humidity = $_POST['humidity'];
    $current_condition = $_POST['condition'];

    // Use prepared statement to prevent SQL injection
    $save_sql = "INSERT INTO weather_data (date, temperature, humidity, `condition`) 
                 VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($save_sql);
    $stmt->bind_param("sdis", $current_date, $current_temp, $current_humidity, $current_condition);

    if ($stmt->execute()) {
        $success_message = "Weather data saved successfully!";
    } else {
        $error_message = "Failed to save weather data: " . $conn->error;
    }
}

// Handle weekly filter
$week_start = isset($_GET['week_start']) ? $_GET['week_start'] : date("Y-m-d");
$week_start_date = date('Y-m-d', strtotime($week_start));
$week_end_date = date('Y-m-d', strtotime($week_start . ' +6 days'));

// Update the weather query to use prepared statement
$weather_query = "SELECT date, temperature, humidity, `condition` 
                 FROM weather_data 
                 WHERE date >= ? AND date <= ?
                 ORDER BY date ASC"; // Changed to ASC for chronological order

$stmt = $conn->prepare($weather_query);
$stmt->bind_param("ss", $week_start_date, $week_end_date);
$stmt->execute();
$result = $stmt->get_result();

// Add debugging to verify dates
$debug = false; // Set to true to see date ranges
if ($debug) {
    echo "Week Start: " . $week_start_date . "<br>";
    echo "Week End: " . $week_end_date . "<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: "Lato", sans-serif;
            margin: 0;
            background-color: #e8f5e9;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h2 {
            color: #2e7d32;
            font-size: 22px;
            text-align: center;
        }
        /* Weather container - Live Weather Information */
        .weather-container {
            background-color: #f1f8e9;
            padding: 20px;
            border: 1px solid #81c784;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .weather-container p {
            margin: 5px 0;
            color: #4caf50;
            font-size: 16px;
        }

        /* Saved Weather Data Table */
        table {
            width: 100%;
            border: 1px solid #dddddd;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }

        table th {
            background-color: #4caf50;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
          form button {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        form button:hover {
            background-color: #388e3c;
        }

        /* Message Success/Error */
        .success {
            color: #155724;
            text-align: center;
        }

        .error {
            color: #721c24;
            text-align: center;
        }
        
        /* Button styling */
        .button-container {
          text-align: center;
          margin-top: 20px;
        }

        .styled-button {
          display: inline-block;
          background-color: #4caf50;
          color: white;
          font-size: 16px;
          font-weight: bold;
          text-decoration: none;
          padding: 10px 20px;
          border-radius: 8px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Button shadow */
          transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .styled-button:hover {
          background-color: #388e3c;
          box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2); /* Deeper shadow on hover */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Current Weather Data</h2>

    <!-- Message Section -->
    <?php
    if (isset($success_message)) {
        echo "<div class='success'>$success_message</div>";
    }
    if (isset($error_message)) {
        echo "<div class='error'>$error_message</div>";
    }
    ?>

    <?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $apiKey = "7d280df58afbac062da095d39dcd43c5";
    $city = "Simpang Ampat";
    $country = "MY";

    $apiUrl = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode("$city,$country") . "&appid=$apiKey&units=metric";

    $response = @file_get_contents($apiUrl);
    if ($response === FALSE) {
        echo "<p>Error fetching weather data.</p>";
        exit;
    }

    $data = json_decode($response, true);

    if (isset($data['main'])) {
        $current_temp = $data['main']['temp'] ?? "N/A";
        $current_feels_like = $data['main']['feels_like'] ?? "N/A";
        $current_humidity = $data['main']['humidity'] ?? "N/A";
        $current_wind_speed = $data['wind']['speed'] ?? "N/A";
        $current_condition = $data['weather'][0]['description'] ?? "N/A";
    } else {
        $current_temp = $current_humidity = $current_wind_speed = $current_feels_like = $current_condition = "N/A";
    }
    ?>

    <!-- Current Weather Section -->
    <div class="weather-container">
        <p><strong>Date:</strong> <?php echo date("Y-m-d"); ?></p>
        <p><strong>Time:</strong> <?php echo date("h:i A"); ?></p>
        <p><strong>Day:</strong> <?php echo date("l"); ?></p>
        <p><strong>Temperature:</strong> <?php echo $current_temp; ?> °C</p>
        <p><strong>Feels Like:</strong> <?php echo $current_feels_like; ?> °C</p>
        <p><strong>Humidity:</strong> <?php echo $current_humidity; ?>%</p>
        <p><strong>Wind Speed:</strong> <?php echo $current_wind_speed; ?> m/s</p>
        <p><strong>Condition:</strong> <?php echo $current_condition; ?></p>
    </div>


    <!-- Save Weather -->
    <form method="POST" action="">
        <input type="hidden" name="temperature" value="<?php echo $current_temp; ?>">
        <input type="hidden" name="humidity" value="<?php echo $current_humidity; ?>">
        <input type="hidden" name="condition" value="<?php echo $current_condition; ?>">
        <button type="submit" name="save_weather">Save Weather</button>
    </form>

    <!-- Weekly Filter Section -->
    <form method="GET" action="" class="mb-3">
        <label>Select Week Starting From:</label>
        <input type="date" 
               name="week_start" 
               value="<?php echo htmlspecialchars($week_start); ?>"
               max="<?php echo date('Y-m-d'); ?>"
               required>
        <button type="submit">Apply Weekly Filter</button>
    </form>

    <!-- Display the Saved Weather Data -->
    <h3>Weather Data for <?php echo date('M d', strtotime($week_start_date)); ?> - <?php echo date('M d, Y', strtotime($week_end_date)); ?></h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Temperature (°C)</th>
                <th>Humidity (%)</th>
                <th>Condition</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . date('Y-m-d', strtotime($row['date'])) . "</td>";
                    echo "<td>{$row['temperature']}</td>";
                    echo "<td>{$row['humidity']}</td>";
                    echo "<td>{$row['condition']}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No weather data available for the selected week.</td></tr>";
            }
            ?>
        </tbody>
    </table>
      <!-- Button Container -->
  <div class="button-container">
      <a href="index.php" class="styled-button">Back to Farm Activities System</a>
</div>
</body>
</html>
