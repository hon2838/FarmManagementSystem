<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body {
  font-family: "Lato", sans-serif;
  margin: 0;
  background-color: #e8f5e9; /* Light green background */
}

.container {
  max-width: 800px;
  margin: 50px auto;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
  padding: 30px;
  border: 2px solid #4caf50; /* Green border */
}

h2 {
  text-align: center;
  color: #2e7d32; /* Dark green */
  font-size: 24px;
  margin-bottom: 20px;
}

.recommendation {
  font-size: 18px;
  background-color: #c8e6c9; /* Light green */
  color: #1b5e20; /* Darker green */
  padding: 10px;
  border: 1px solid #66bb6a;
  border-radius: 5px;
  text-align: center;
  margin-bottom: 20px;
}

form label {
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
  color: #388e3c; /* Medium green */
}

form input, form select, form textarea, form button {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
}

form input:focus, form select:focus, form textarea:focus {
  border-color: #388e3c;
  box-shadow: 0 0 5px rgba(56, 142, 60, 0.5);
  outline: none;
}

form button {
  background-color: #4caf50;
  color: white;
  border: none;
  cursor: pointer;
  font-size: 18px;
}

form button:hover {
  background-color: #388e3c;
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

/* Modal styling */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #4caf50;
    border-radius: 8px;
    width: 80%;
    max-width: 500px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.close {
    color: #4caf50;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: #388e3c;
}

.add-type-btn {
    background-color: #4caf50;
    color: white;
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    margin-bottom: 10px;
}

.add-type-btn:hover {
    background-color: #388e3c;
}
</style>
</head>
<body>

<!-- Main Content -->
<div class="container">
  <h2>Activity Recording Form</h2>

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

  // Weather API settings
  $apiKey = "7d280df58afbac062da095d39dcd43c5"; 
  $city = "Simpang Ampat";
  $country = "MY";
  $apiUrl = "http://api.openweathermap.org/data/2.5/weather?q=" . urlencode("$city,$country") . "&appid=$apiKey&units=metric";

  // Fetch weather data
  $weather = ['temperature' => 'N/A', 'humidity' => 'N/A', 'condition' => 'N/A'];
  $response = @file_get_contents($apiUrl);
  if ($response !== FALSE) {
      $data = json_decode($response, true);
      if (isset($data['main'])) {
          $weather['temperature'] = $data['main']['temp'] ?? 'N/A';
          $weather['humidity'] = $data['main']['humidity'] ?? 'N/A';
          $weather['condition'] = $data['weather'][0]['description'] ?? 'N/A';
      }
  }

  // Recommendation logic
  function get_weather_recommendation($temperature, $humidity, $condition) {
      if (strpos($condition, 'rain') !== false) {
          return "Rain detected.<br>Recommended activity: Fertilizing as the soil is moist for nutrient absorption.";
      } elseif ($temperature > 32) {
          return "High temperature detected.<br>Recommended activity: Watering to prevent dehydration of plants.";
      } elseif ($humidity > 80 && strpos($condition, 'cloud') !== false) {
          return "High humidity with cloudy weather detected.<br>Recommended activity: Routine maintenance such as weeding or pest control.";
      } elseif (strpos($condition, 'clear sky') !== false || strpos($condition, 'few clouds') !== false) {
          return "Clear weather detected.<br>Recommended activity: Harvesting as the fruits are less likely to spoil.";
      } else {
          return "Moderate weather detected.<br>Recommended activity: Routine maintenance.";
      }
  }

  $recommendation = get_weather_recommendation($weather['temperature'], $weather['humidity'], $weather['condition']);

  // Handle form submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $activity_type = $_POST['activity_type'];
      $activity_date = $_POST['activity_date'];
      $start_time = $_POST['start_time'];
      $end_time = $_POST['end_time'];
      $person_responsible = $_POST['person_responsible'];
      $plot_field = $_POST['plot_field'];
      $specific_area = $_POST['specific_area'] ?? null;
      $description = $_POST['description'];
      $fertilizer1 = $_POST['fertilizer1'];
      $fertilizer1_amount = $_POST['fertilizer1_amount'];
      $water1 = $_POST['water1'];
      $water1_amount = $_POST['water1_amount'];

      $other_materials = $_POST['other_materials'];

      $temperature = $weather['temperature'];
      $humidity = $weather['humidity'];
      $condition = $weather['condition'];

      // Combine materials and quantities into formatted strings
      $materials_used = [];
      $quantities_used = [];

      // Process fertilizers
      if (!empty($_POST['fertilizer1'])) {
          $materials_used[] = $_POST['fertilizer1'];
          $quantities_used[] = $_POST['fertilizer1_amount'] . " kg";
      }

      // Process water
      if (!empty($_POST['water1'])) {
          $materials_used[] = $_POST['water1'];
          $quantities_used[] = $_POST['water1_amount'] . " L";
      }

      // Add other materials
      if (!empty($_POST['other_materials'])) {
          $materials_used[] = $_POST['other_materials'];
          $quantities_used[] = "N/A";
      }

      $material_used = implode(", ", $materials_used);
      $quantity_used = implode(", ", $quantities_used);

      $sql = "INSERT INTO farm_activities (
          activity_type, 
          activity_date, 
          start_time, 
          end_time, 
          person_responsible, 
          plot_field, 
          specific_area, 
          description, 
          fertilizer1, 
          fertilizer1_amount, 
          water1, 
          water1_amount, 
          other_materials, 
          temperature, 
          humidity, 
          `condition`
      ) VALUES (
          '$activity_type',
          '$activity_date',
          '$start_time',
          '$end_time',
          '$person_responsible',
          '$plot_field',
          '$specific_area',
          '$description',
          '$fertilizer1',
          '$fertilizer1_amount',
          '$water1',
          '$water1_amount',
          '$other_materials',
          '$temperature',
          '$humidity',
          '$condition'
      )";

      if ($conn->query($sql) === TRUE) {
          echo "<div class='recommendation'>Activity recorded successfully!</div>";
      } else {
          echo "<div class='recommendation' style='background-color: #f8d7da; color: #721c24;'>Error: " . $conn->error . "</div>";
      }
  }

  $conn->close();
  ?>

  <!-- Display Weather Recommendation -->
  <div class="recommendation">
    <?php echo $recommendation; ?>
  </div>

  <!-- Form -->
  <form action="" method="POST">
      <label for="activity_type">Type of Activity</label>
      <select id="activity_type" name="activity_type" required>
          <option value="">Select Activity Type</option>
          <option value="Watering">Watering</option>
          <option value="Fertilizing">Fertilizing</option>
          <option value="Weeding">Weeding</option>
          <option value="Planting">Planting</option>
      </select>

      <!-- Add New Activity Type Button -->
      <button type="button" id="openActivityTypeModal" class="add-type-btn">+ Add New Activity Type</button>

      <label for="activity_date">Activity Date</label>
      <input type="date" id="activity_date" name="activity_date" required>

      <label for="start_time">Start Time</label>
      <input type="time" id="start_time" name="start_time" required>

      <label for="end_time">End Time</label>
      <input type="time" id="end_time" name="end_time" required>

      <label for="person_responsible">Person Responsible</label>
      <input type="text" id="person_responsible" name="person_responsible" placeholder="Enter name of person" required>

      <label for="plot_field">Plot/Field</label>
      <select id="plot_field" name="plot_field" required>
          <option value="">Select Plot</option>
          <option value="Plot A">Plot A</option>
          <option value="Plot B">Plot B</option>
          <option value="Plot C">Plot C</option>
      </select>

      <label for="specific_area">Specific Area/Row (Optional)</label>
      <input type="text" id="specific_area" name="specific_area" placeholder="Enter specific area or row">

      <label for="description">Description</label>
      <textarea id="description" name="description" rows="4" placeholder="Provide additional details about the activity"></textarea>

      <!-- Modified Materials/Tools Used Section -->
      <label>Fertilizer Details</label>
      <div style="margin-bottom: 15px;">
          <input type="text" id="fertilizer1" name="fertilizer1" placeholder="Fertilizer 1 name" style="width: 60%; margin-right: 2%;">
          <input type="number" id="fertilizer1_amount" name="fertilizer1_amount" placeholder="Amount (kg)" style="width: 38%;">
      </div>

      <label>Water Usage</label>
      <div style="margin-bottom: 15px;">
          <input type="text" id="water1" name="water1" placeholder="Water source 1" style="width: 60%; margin-right: 2%;">
          <input type="number" id="water1_amount" name="water1_amount" placeholder="Amount (L)" style="width: 38%;">
      </div>

      <label for="other_materials">Other Materials/Tools Used</label>
      <textarea id="other_materials" name="other_materials" rows="2" placeholder="List any other materials or tools used (e.g., Hoe, Gloves, etc.)"></textarea>

      <!-- Display Auto-recorded Weather Data -->
      <label>Weather Information (Auto-recorded)</label>
      <p><strong>Temperature:</strong> <?php echo $weather['temperature']; ?> °C</p>
      <p><strong>Humidity:</strong> <?php echo $weather['humidity']; ?>%</p>
      <p><strong>Condition:</strong> <?php echo $weather['condition']; ?></p>

      <button type="submit">Submit Activity</button>
  </form>

  <!-- Button Container -->
  <div class="button-container">
      <a href="activity_list.php" class="styled-button">Go to Activity List</a>
      <a href="index.php" class="styled-button">Back to Farm Activities System</a>
  </div>
</div>

<!-- Modal for Registering New Activity Type -->
<div id="activityTypeModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Register New Activity Type</h3>
        <form method="POST" id="activityTypeForm">
            <label for="new_activity_type">Activity Type:</label>
            <input type="text" id="new_activity_type" name="new_activity_type" required>
            <label for="activity_description">Description:</label>
            <textarea id="activity_description" name="activity_description" rows="3"></textarea>
            <button type="submit" name="register_activity_type">Register</button>
        </form>
    </div>
</div>

<!-- JavaScript for Modal Functionality -->
<script>
var modal = document.getElementById("activityTypeModal");
var btn = document.getElementById("openActivityTypeModal");
var span = document.getElementsByClassName("close")[0];

btn.onclick = function() {
    modal.style.display = "block";
}

span.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Handle form submission
document.getElementById("activityTypeForm").onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('register_activity_type.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Activity type registered successfully!');
            location.reload();
        } else {
            alert('Error registering activity type: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error registering activity type');
    });
};
</script>

</body>
</html>
