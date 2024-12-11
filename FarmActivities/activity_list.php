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

/* Enhanced Table Styling */
.table-container {
  max-width: 95%; /* Wider table container for more space */
  margin: 40px auto; /* Added top margin for spacing */
  background: #ffffff;
  border-radius: 12px; /* Increased border radius for smooth edges */
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
  padding: 30px; /* Increased padding for more space around the table */
  border: 2px solid #4caf50; /* Green border */
  overflow-x: auto;
}

h2 {
  text-align: center;
  color: #1b5e20; /* Dark green */
  font-size: 24px;
  margin-bottom: 20px;
  font-weight: bold;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  font-size: 16px;
}

table th, table td {
  text-align: left;
  padding: 12px 15px;
}

table th {
  background-color: #4caf50;
  color: white;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-size: 14px;
}

table tr {
  border-bottom: 1px solid #dddddd;
}

table tr:last-of-type {
  border-bottom: 2px solid #4caf50;
}

table tr:hover {
  background-color: #f1f1f1;
}

table tr:nth-child(even) {
  background-color: #f9f9f9;
}

table td {
  color: #333;
  font-size: 14px;
}

/* Back Button Styling */
.back-button {
  display: block;
  width: fit-content;
  margin: 20px auto;
  padding: 10px 20px;
  background-color: #4caf50; /* Green background */
  color: white; /* White text */
  text-align: center;
  text-decoration: none;
  font-size: 16px;
  font-weight: bold;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Button shadow */
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.back-button:hover {
  background-color: #388e3c; /* Darker green on hover */
  box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2); /* Deeper shadow on hover */
}

/* Add a responsive design for smaller screens */
@media screen and (max-width: 768px) {
  table {
    font-size: 14px;
  }

  table th, table td {
    padding: 10px;
  }

  .table-container {
    padding: 15px;
  }
}

/* Filter Section Styling */
.filter-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 20px 0;
}

.filter-form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.filter-form select {
    padding: 8px;
    border: 1px solid #4caf50;
    border-radius: 4px;
    font-size: 14px;
}

.filter-form button {
    padding: 8px 16px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.filter-form button:hover {
    background-color: #388e3c;
}
</style>
<script>
// Confirmation prompt for deletion
function confirmDeletion(activityId) {
    if (confirm("Are you sure you want to delete this activity?")) {
        window.location.href = "activity_delete.php?activity_id=" + activityId;
    }
}
</script>
</head>
<body>

<!-- Main Content -->
<div class="table-container">
  <h2>List of Recorded Activities</h2>
  
  <!-- Add Filter Section -->
  <div class="filter-container">
      <form method="GET" class="filter-form">
          <select name="year">
              <option value="">Select Year</option>
              <?php 
              $current_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
              for ($year = 2024; $year <= 2040; $year++) {
                  $selected = ($current_year == $year) ? 'selected' : '';
                  echo "<option value='$year' $selected>$year</option>";
              }
              ?>
          </select>
          <button type="submit">Filter by Year</button>
      </form>

      <form method="GET" class="filter-form">
          <select name="month" required>
              <option value="">Select Month</option>
              <?php 
              for ($m = 1; $m <= 12; $m++) {
                  $selected = (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '';
                  echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
              }
              ?>
          </select>
          <select name="year" required>
              <option value="">Select Year</option>
              <?php 
              $current_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
              for ($year = 2024; $year <= 2040; $year++) {
                  $selected = ($current_year == $year) ? 'selected' : '';
                  echo "<option value='$year' $selected>$year</option>";
              }
              ?>
          </select>
          <button type="submit">Filter by Month</button>
      </form>
  </div>

  <table>
    <thead>
      <tr>
        <th>Type of Activity</th>
        <th>Activity Date</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Person Responsible</th>
        <th>Plot/Field</th>
        <th>Specific Area/Row</th>
        <th>Description</th>
        <th>Fertilizer</th>
        <th>Fertilizer Amount (kg)</th>
        <th>Water Source</th>
        <th>Water Amount (L)</th>
        <th>Other Materials</th>
        <th>Temperature</th>
        <th>Humidity</th>
        <th>Condition</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Database connection
      $host = "localhost";
      $username = "root";
      $password = "";
      $database = "farm_management_system";
      
      $conn = new mysqli($host, $username, $password, $database);
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      // Fetch data from activities table
      $sql = "SELECT * FROM farm_activities WHERE 1=1";
      
      if (isset($_GET['year']) && !isset($_GET['month'])) {
          $year = $_GET['year'];
          $sql .= " AND YEAR(activity_date) = '$year'";
      }
      
      if (isset($_GET['month']) && isset($_GET['year'])) {
          $month = $_GET['month'];
          $year = $_GET['year'];
          $sql .= " AND MONTH(activity_date) = '$month' AND YEAR(activity_date) = '$year'";
      }
      
      $sql .= " ORDER BY activity_date DESC";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row['activity_type']) . "</td>";
          echo "<td>" . date('Y-m-d', strtotime($row['activity_date'])) . "</td>";
          echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
          echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
          echo "<td>" . htmlspecialchars($row['person_responsible']) . "</td>";
          echo "<td>" . htmlspecialchars($row['plot_field']) . "</td>";
          echo "<td>" . htmlspecialchars($row['specific_area'] ?? '-') . "</td>";
          echo "<td>" . htmlspecialchars($row['description']) . "</td>";
          echo "<td>" . htmlspecialchars($row['fertilizer1'] ?? '-') . "</td>";
          echo "<td>" . htmlspecialchars($row['fertilizer1_amount'] ?? '0.00') . "</td>";
          echo "<td>" . htmlspecialchars($row['water1'] ?? '-') . "</td>";
          echo "<td>" . htmlspecialchars($row['water1_amount'] ?? '0.00') . "</td>";
          echo "<td>" . htmlspecialchars($row['other_materials'] ?? '-') . "</td>";
          echo "<td>" . htmlspecialchars($row['temperature']) . " °C</td>";
          echo "<td>" . htmlspecialchars($row['humidity']) . " %</td>";
          echo "<td>" . htmlspecialchars($row['condition']) . "</td>";
          echo "<td>";
          echo "<a href='activity_update.php?activity_id=" . htmlspecialchars($row['activity_id']) . "'>Update</a> | ";
          echo "<a href='#' onclick=\"confirmDeletion(" . htmlspecialchars($row['activity_id']) . ")\">Delete</a>";
          echo "</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='17' style='text-align:center;'>No activities recorded.</td></tr>";
      }

      $conn->close();
      ?>
    </tbody>
  </table>
</div>

<!-- Back Button -->
<a href="index.php" class="back-button">Back to Farm Activities System</a>

<script>
function confirmDeletion(activityId) {
    if (confirm("Are you sure you want to delete this activity?")) {
        window.location.href = "activity_delete.php?activity_id=" + activityId;
    }
}
</script>

</body>
</html>
