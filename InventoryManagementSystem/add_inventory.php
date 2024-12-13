<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grade = $_POST['grade'];
    $quantity = $_POST['quantity'];
    $price_per_kg = $_POST['price_per_kg'];
    
    // Remove total_cost from the INSERT since it's a generated column
    $sql = "INSERT INTO inventory (grade, quantity, price_per_kg, recorded_date) 
            VALUES (?, ?, ?, NOW())";
            
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sid", $grade, $quantity, $price_per_kg);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Inventory added successfully!');
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();

// Fetch the external div content
function fetchExternalDiv() {
    $url = 'https://manamurah.com/barang/limaukasturi-1928';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $html = curl_exec($ch);
    curl_close($ch);

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $divs = $xpath->query("//div[contains(@class, 'rounded-xl border bg-card text-card-foreground shadow-sm flex-1')]");

    if ($divs->length > 0) {
        return $dom->saveHTML($divs[0]);
    } else {
        return '<p>External content not available.</p>';
    }
}

$externalDiv = fetchExternalDiv();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Inventory</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        h2 {
            text-align: center;
            font-size: 1.8em;
            margin-top: 20px;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-size: 1em;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        button {
            background-color: #28a745; /* Green button color */
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838; /* Darker green on hover */
        }
        .view-button {
            display: inline-block;
            width: auto;
            text-align: center;
            background-color: #28a745; /* Green button color */
            color: white;
            text-decoration: none;
            padding: 8px 20px; /* Smaller button size */
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }
        .view-button:hover {
            background-color: #218838; /* Darker green on hover */
        }
        /* Add these new styles */
        .content-container {
            display: flex;
            flex-direction: row-reverse; /* Reverses the order of flex items */
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            gap: 30px;
        }

        .form-section {
            flex: 1;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .external-content {
            flex: 1;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .external-content h2 {
            color: #28a745;
            margin-bottom: 20px;
            font-size: 1.5em;
            text-align: center;
        }

        /* Keep existing styles */
        /* Add these styles */
        .price-container {
            text-align: center;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 20px;
            background: #f8f9fa;
        }

        .current-price {
            font-size: 2.5rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 15px;
        }

        .price-details {
            color: #6c757d;
            line-height: 1.5;
        }

        .price-details p {
            margin: 5px 0;
        }

        .external-content {
            flex: 1;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .external-content h2 {
            color: #28a745;
            margin-bottom: 20px;
            font-size: 1.5em;
            text-align: center;
        }

        /* Style for the graph */
        svg.plot-d6a7b5 {
            max-width: 100%;
            height: auto;
            margin: 20px auto;
            display: block;
        }

        /* Update the button container styles */
        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 30px auto;
            text-align: center;
            width: 100%;
        }

        .view-button {
            display: inline-block;
            width: auto;
            text-align: center;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            margin: 0;
            transition: background-color 0.3s, transform 0.2s;
        }

        .view-button:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <h2>Add Limau Kasturi Inventory</h2>
    
    <div class="content-container">
        <!-- Form Section -->
        <div class="form-section">
            <form action="add_inventory.php" method="POST">
                <label for="grade">Grade:</label>
                <select name="grade" id="grade" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>

                <label for="quantity">Quantity (kg):</label>
                <input type="number" name="quantity" id="quantity" required>

                <label for="price_per_kg">Price per kg (RM):</label>
                <input type="number" name="price_per_kg" id="price_per_kg" step="0.50" min="0.50" required>

                <button type="submit">Add Inventory</button>
            </form>
        </div>

        <!-- External Content Section -->
        <div class="external-content">
            <h2>Market Price Reference</h2>
            <div class="price-container">
                <h3 class="current-price">RM7.16</h3>
                <div class="price-details">
                    <p>Harga Purata 1kg LIMAU KASTURI</p>
                    <p>Seluruh Negara untuk Minggu Ini<</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Centered buttons -->
    <div class="button-container">
        <a href="index.php" class="view-button">Go Back to Main Page</a>
        <a href="view_inventory.php" class="view-button">Go to View Inventory</a>
    </div>
</body>
</html>
