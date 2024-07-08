<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit();
}

include("./includes/config.php");

$message = "";

// Create connection
$conn = new mysqli($DBservername, $DBusername, $DBpassword, $DBname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['location_text']) && isset($_GET['location_coordinates'])) {
    $location_text = $conn->real_escape_string($_GET['location_text'] ?? '');
    $location_coordinates = $conn->real_escape_string($_GET['location_coordinates'] ?? '');
    $current_email = $_SESSION['email'];

    if (!empty($location_text) && !empty($location_coordinates)) {
        $sql = "INSERT INTO saved_locations (email, location_text, location_coordinates) VALUES ('$current_email', '$location_text', '$location_coordinates')";
        if ($conn->query($sql) === TRUE) {
            $message = "Location saved successfully!";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $message = "Both location text and coordinates are required!";
    }
}

 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_text'])) {
    $feedback_text = $conn->real_escape_string($_POST['feedback_text'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    $current_email = $_SESSION['email'];

    if (!empty($feedback_text) && $rating > 0) {
        $sql = "INSERT INTO feedback (email, feedback_text, rating) VALUES ('$current_email', '$feedback_text', '$rating')";
        if ($conn->query($sql) === TRUE) {
            $message = "Feedback submitted successfully!";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $message = "Feedback and rating are required!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colombo Green Explorer</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        #map {
            height: 100%;
            width: 100%;
        }
        .pac-container {
            z-index: 10000 !important;
        }

        /* Modal styling */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 10px;
    position: relative;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.feedback-section {
    margin-bottom: 20px;
}

.rating {
    display: flex;
    justify-content: flex-start;
    gap: 5px; /* Space between stars */
}

.star {
    font-size: 3rem;
    color: #ccc;
    cursor: pointer;
    transition: color 0.2s;
}

.star.active{
    color: #FFD700;
}

    </style>
</head>
<body class="bg-gray-100">

<div id="feedbackModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h1 class="text-1xl font-semibold mb-6">Feedback Form</h1>
        <form id="feedbackForm" method="post" action="feedback.php">
            <div class="feedback-section">
                <h3>How do you feel about the experience of the app?</h3>
                <select name="experience" required class="controls w-4/5 p-2 border rounded">
                    <option value="" disabled selected>Select an option</option>
                    <option value="I am very satisfied, the app meets my needs well">I am very satisfied, the app meets my needs well.</option>
                    <option value="I am satisfied, the app functions as expected">I am satisfied, the app functions as expected.</option>
                    <option value="I feel neutral about the app, it’s just okay">I feel neutral about the app, it’s just okay.</option>
                    <option value="I am somewhat dissatisfied, the app needs improvements">I am somewhat dissatisfied, the app needs improvements.</option>
                    <option value="I am very dissatisfied, the app often frustrates me">I am very dissatisfied, the app often frustrates me.</option>
                </select>
            </div>

            <div class="feedback-section">
            <h3>Do you have any additional comments or sugesstions for the app?</h3>
                <textarea class="controls w-4/5 p-2 border rounded" name="comment" rows="4" cols="50" placeholder="Write your comments here..." required class="controls w-4/5 p-2 border rounded"></textarea>
            </div>

            <div class="feedback-section">
                <h3>What is your selected area?</h3>
                <select name="area_text" required class="controls w-4/5 p-2 border rounded">
                    <option value="" disabled selected>Select an area</option>
                    <option value="Aluthkade_East">Aluthkade East</option>
                    <option value="Aluthkade_West">Aluthkade West</option>
                    <option value="Bambalapitiya">Bambalapitiya</option>
                    <option value="Baththaramulla">Baththaramulla</option>
                    <option value="Bloemendhal">Bloemendhal</option>
                    <option value="Borella_North">Borella North</option>
                    <option value="Borella_South">Borella South</option>
                    <option value="Colombo_Fort">Colombo Fort</option>
                    <option value="Dehiwala">Dehiwala</option>
                    <option value="Dematagoda">Dematagoda</option>
                    <option value="Grandpass_North">Grandpass North</option>
                    <option value="Grandpass_South">Grandpass South</option>
                    <option value="Havelock_Town">Havelock Town</option>
                    <option value="Homagama">Homagama</option>
                    <option value="Kaduwela">Kaduwela</option>
                    <option value="Kalubovila">Kalubovila</option>
                    <option value="Kirulapone">Kirulapone</option>
                    <option value="Kohuwala">Kohuwala</option>
                    <option value="Kollupitiya">Kollupitiya</option>
                    <option value="Kolonnawa">Kolonnawa</option>
                    <option value="Kotahena_East">Kotahena East</option>
                    <option value="Kotahena_West">Kotahena West</option>
                    <option value="Kottawa">Kottawa</option>
                    <option value="Kurunduwatta">Kurunduwatta</option>
                    <option value="Madampitiya">Madampitiya</option>
                    <option value="Maharagama">Maharagama</option>
                    <option value="Malabe">Malabe</option>
                    <option value="Maligawatta_East">Maligawatta East</option>
                    <option value="Maligawatta_West">Maligawatta West</option>
                    <option value="Maradana">Maradana</option>
                    <option value="Mattakkuliya">Mattakkuliya</option>
                    <option value="Modara">Modara</option>
                    <option value="Moratuwa">Moratuwa</option>
                    <option value="Mount_Lavinea">Mount Lavinea</option>
                    <option value="Narahenpita">Narahenpita</option>
                    <option value="Nawala">Nawala</option>
                    <option value="Nugegoda">Nugegoda</option>
                    <option value="Oruwala">Oruwala</option>
                    <option value="Pamankada_East">Pamankada East</option>
                    <option value="Pamankada_West">Pamankada West</option>
                    <option value="Panchikawatta">Panchikawatta</option>
                    <option value="Pannipitiya">Pannipitiya</option>
                    <option value="Pettah">Pettah</option>
                    <option value="Piliyandala">Piliyandala</option>
                    <option value="Ragagiriya">Ragagiriya</option>
                    <option value="Rathmalana">Rathmalana</option>
                    <option value="Slave_island">Slave Island</option>
                    <option value="Thalawathugoda">Thalawathugoda</option>
                    <option value="Union_Place">Union Place</option>
                    <option value="Walikada">Walikada</option>
                    <option value="Wellawatta_North">Wellawatta North</option>
                    <option value="Wellawatta_South">Wellawatta South</option>
                </select>
            </div>

            <div class="feedback-section">
                <h3>How do you feel about area?</h3>
                <textarea class="controls w-4/5 p-2 border rounded" name="feedback_text" rows="4" cols="50" placeholder="Write your comments here..." required class="controls w-4/5 p-2 border rounded"></textarea>
            </div>

            <div class="feedback-section">
                <h3>How you rate the area:</h3>
                <div class="rating" id="starRating">
                    <span class="star" onclick="updateRating(1)">★</span>
                    <span class="star" onclick="updateRating(2)">★</span>
                    <span class="star" onclick="updateRating(3)">★</span>
                    <span class="star" onclick="updateRating(4)">★</span>
                    <span class="star" onclick="updateRating(5)">★</span> 
                </div>
                <input required type="hidden" name="rating" id="ratingInput" value="0">
                <p id="output"></p>
            </div>

            <button  class="bg-green-400 px-4 py-3" type="submit">Submit Feedback</button>
        </form>
    </div>
</div>

<div class="flex flex-col min-h-screen">
        <?php include("./includes/navbar.php"); ?>

            <div class="flex flex-1">

    <div class="w-1/5 bg-green-800 text-white p-4">
        <?php include("./includes/sidebar.php"); ?>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-white p-8">
        <h2 class="text-3xl font-semibold mb-6">View on Map</h2>

        <img class="mb-4" src="./images/dd.png" style="height:40px; width:500px">

        <!-- Search Box and Save Button -->
        <div class="mb-4">
<select id="selLoc"  class="controls w-4/5 p-2 border rounded" onchange="updateLocation(this.value)">
  <option value="null">Selct an area</option>
  <option value="Aluthkade_East">Aluthkade East</option>
  <option value="Aluthkade_West">Aluthkade West</option>
  <option value="Bambalapitiya">Bambalapitiya</option>
  <option value="Baththaramulla">Baththaramulla</option>
  <option value="Bloemendhal">Bloemendhal</option>
  <option value="Borella_North">Borella North</option>
  <option value="Borella_South">Borella South</option>
  <option value="Colombo_Fort">Colombo Fort</option>
  <option value="Dehiwala">Dehiwala</option>
  <option value="Dematagoda">Dematagoda</option>
  <option value="Grandpass_North">Grandpass North</option>
  <option value="Grandpass_South">Grandpass South</option>
  <option value="Havelock_Town">Havelock Town</option>
  <option value="Homagama">Homagama</option>
  <option value="Kaduwela">Kaduwela</option>
  <option value="Kalubovila">Kalubovila</option>
  <option value="Kirulapone">Kirulapone</option>
  <option value="Kohuwala">Kohuwala</option>
  <option value="Kollupitiya">Kollupitiya</option>
  <option value="Kolonnawa">Kolonnawa</option>
  <option value="Kotahena_East">Kotahena East</option>
  <option value="Kotahena_West">Kotahena West</option>
  <option value="Kottawa">Kottawa</option>
  <option value="Kurunduwatta">Kurunduwatta</option>
  <option value="Madampitiya">Madampitiya</option>
  <option value="Maharagama">Maharagama</option>
  <option value="Malabe">Malabe</option>
  <option value="Maligawatta_East">Maligawatta East</option>
  <option value="Maligawatta_West">Maligawatta West</option>
  <option value="Maradana">Maradana</option>
  <option value="Mattakkuliya">Mattakkuliya</option>
  <option value="Modara">Modara</option>
  <option value="Moratuwa">Moratuwa</option>
  <option value="Mount_Lavinea">Mount Lavinea</option>
  <option value="Narahenpita">Narahenpita</option>
  <option value="Nawala">Nawala</option>
  <option value="Nugegoda">Nugegoda</option>
  <option value="Oruwala">Oruwala</option>
  <option value="Pamankada_East">Pamankada East</option>
  <option value="Pamankada_West">Pamankada West</option>
  <option value="Panchikawatta">Panchikawatta</option>
  <option value="Pannipitiya">Pannipitiya</option>
  <option value="Pettah">Pettah</option>
  <option value="Piliyandala">Piliyandala</option>
  <option value="Ragagiriya">Ragagiriya</option>
  <option value="Rathmalana">Rathmalana</option>
  <option value="Slave_island">Slave Island</option>
  <option value="Thalawathugoda">Thalawathugoda</option>
  <option value="Union_Place">Union Place</option>
  <option value="Walikada">Walikada</option>
  <option value="Wellawatta_North">Wellawatta North</option>
  <option value="Wellawatta_South">Wellawatta South</option>
</select>            <div class="flex gap-4">
                <button onclick="saveLocation()" class="w-1/5 p-2 bg-blue-500 text-white rounded mt-4">Save Location</button>
                <button onclick="generateRandomShapes()" class="w-1/5 p-2 bg-blue-500 text-white rounded mt-4">Process</button>
            </div>
        </div>
        
        <!-- Filter Buttons -->
        <div class="mb-4 flex gap-4">
            <button class="filter-button p-2 bg-green-500 text-white rounded" data-type="walking_paths">Walking Paths</button>
            <button class="filter-button p-2 bg-green-500 text-white rounded" data-type="play_areas">Play Areas</button>
            <button class="filter-button p-2 bg-green-500 text-white rounded" data-type="green_parks">Green Parks</button>
            <button class="filter-button p-2 bg-green-500 text-white rounded" data-type="water_features">Water features</button>
            <button class="filter-button p-2 bg-green-500 text-white rounded" data-type="natural_trails">Natural trails</button>
        </div>
        <div class="mb-4 flex gap-4">
            <a href="spec.php?spec=Vegetation cover&location=<?php if(isset($_GET['location'])){echo htmlspecialchars($_GET['location']);}  ?>&location_coordinates=<?php if(isset($_GET['location_coordinates'])){echo htmlspecialchars($_GET['location_coordinates']);}  ?>" class="spec-button p-2 bg-red-700 text-white rounded" data-type="walking_paths">Vegetation cover</a>
            <a href="spec.php?spec=Air quaility&location=<?php if(isset($_GET['location'])){echo htmlspecialchars($_GET['location']);}  ?>&location_coordinates=<?php if(isset($_GET['location_coordinates'])){echo htmlspecialchars($_GET['location_coordinates']);}  ?>" class="spec-button p-2 bg-red-700 text-white rounded" data-type="play_areas">Air quaility</a>
            <a href="spec.php?spec=Temperature&location=<?php if(isset($_GET['location'])){echo htmlspecialchars($_GET['location']);}  ?>&location_coordinates=<?php if(isset($_GET['location_coordinates'])){echo htmlspecialchars($_GET['location_coordinates']);}  ?>" class="spec-button p-2 bg-red-700 text-white rounded" data-type="green_parks">Temperature</a>
            <a href="spec.php?spec=Population&location=<?php if(isset($_GET['location'])){echo htmlspecialchars($_GET['location']);}  ?>&location_coordinates=<?php if(isset($_GET['location_coordinates'])){echo htmlspecialchars($_GET['location_coordinates']);}  ?>" class="spec-button p-2 bg-red-700 text-white rounded" data-type="water_features">Population</a>
        </div>
        
        <!-- Display Message -->
        <?php if ($message): ?>
            <div class="p-4 mb-4 text-white bg-<?php echo ($message == "Location saved successfully!") ? 'green' : 'red'; ?>-500 rounded">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Map Container -->
<div class="relative w-full h-screen">
  <div id="map" class="w-full h-full"></div>
  <div id="top-layer" class="absolute inset-0 w-full flex items-center justify-center  opacity-50  pointer-events-none">
  </div>
</div>

<br>
<br>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livability Report</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0px;
            text-align: left;
        }

        /* Flex container for heading and button */
        .heading-container {
            display: flex;
            align-items: left;
            justify-content: left;
            margin-bottom: 20px;
        }

        /* Styling for the button with increased margin */
        .heading-container button {
            margin-left: 20px; /* Increased margin-left for more space */
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .report {

            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #93c47d;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: none; /* Initially hidden */
        }

        .score {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .color-box {
            display: inline-block;
            width: 30px;
            height: 30px;
            margin-right: 10px;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .category {
            margin-top: 10px;
            text-align: left; /* Left-align category names */
            padding-left: 20px; /* Add padding for better alignment */
        }

        .highlight {
            border: 2px solid #333;
            padding: 10px;
            margin-top: 20px;
            font-weight: bold; /* Bold font */
            font-size: 1.2em; /* Slightly larger font */
        }
    </style>
</head>
<body>

    <!-- Flex container for heading and button -->
    <div class="heading-container">
        <h2 style="margin: 0;">Health Condition and Livability</h2>
        <button onclick="processReport()">Get Here</button>
    </div>

    <div class="report" id="report">
        <p class="score">Weighted Score: <span id="weighted-score">0.00</span></p>
        <div class="category" id="air-quality">
            <div class="color-box" id="color-air-quality"></div> <span id="name-air-quality">Air Quality</span>
        </div>
        <div class="category" id="temperature">
            <div class="color-box" id="color-temperature"></div> <span id="name-temperature">Temperature</span>
        </div>
        <div class="category" id="population">
            <div class="color-box" id="color-population"></div> <span id="name-population">Population</span>
        </div>
        <div class="category" id="vegetation">
            <div class="color-box" id="color-vegetation"></div> <span id="name-vegetation">Vegetation</span>
        </div>
        <div class="highlight" id="weighted">
            <div class="color-box" id="color-weighted"></div> <span id="name-weighted">Overall Livability for the Selected Location</span>
        </div>
    </div>

    <script>
        // Sample reports (replace with actual data)
        var reports = [
            { weightedScore: 3.8, airQualityScore: 'Good', temperatureScore: 'Moderate', populationScore: 'Bad', vegetationScore: 'Excellent' },
            { weightedScore: 4.6, airQualityScore: 'Excellent', temperatureScore: 'Good', populationScore: 'Moderate', vegetationScore: 'Good' },
            { weightedScore: 2.7, airQualityScore: 'Moderate', temperatureScore: 'Bad', populationScore: 'Worst', vegetationScore: 'Moderate' },
            { weightedScore: 1.9, airQualityScore: 'Bad', temperatureScore: 'Worst', populationScore: 'Bad', vegetationScore: 'Good' },
            { weightedScore: 4.2, airQualityScore: 'Good', temperatureScore: 'Good', populationScore: 'Moderate', vegetationScore: 'Excellent' }
        ];

        // Function to map score to color and label
        function mapToColor(score, dataType) {
            if (dataType === 'Weighted') {
                if (score >= 4.5) {
                    return { color: 'rgb(109, 254, 0)', label: 'Excellent' }; // Excellent
                } else if (score >= 3.5) {
                    return { color: 'rgb(214, 255, 0)', label: 'Good' }; // Good
                } else if (score >= 2.5) {
                    return { color: 'rgb(255, 254, 11)', label: 'Moderate' }; // Moderate
                } else if (score >= 1.5) {
                    return { color: 'rgb(255, 154, 0)', label: 'Bad' }; // Bad
                } else {
                    return { color: 'rgb(236, 4, 6)', label: 'Worst' }; // Worst
                }

            } else {
                switch (dataType) {
                    case 'Air Quality':
                    case 'Temperature':
                    case 'Population':
                    case 'Vegetation':

                        switch (score) {
                            case 'Excellent': return { color: 'rgb(109, 254, 0)', label: 'Excellent' };
                            case 'Good': return { color: 'rgb(214, 255, 0)', label: 'Good' };
                            case 'Moderate': return { color: 'rgb(255, 254, 11)', label: 'Moderate' };
                            case 'Bad': return { color: 'rgb(255, 154, 0)', label: 'Bad' };
                            case 'Worst': return { color: 'rgb(236, 4, 6)', label: 'Worst' };
                        }

                        break;
                }
            }
        }

        // Display weighted score and corresponding colors
        function displayReport(report) {
            var overall = mapToColor(report.weightedScore, 'Weighted');
            document.getElementById('weighted-score').textContent = report.weightedScore.toFixed(2);
            document.getElementById('color-weighted').style.backgroundColor = overall.color;
            document.getElementById('name-weighted').textContent = 'Overall Livability for the Selected Location (' + overall.label + ')';

            // Air Quality
            var airQuality = mapToColor(report.airQualityScore, 'Air Quality');
            document.getElementById('color-air-quality').style.backgroundColor = airQuality.color;
            document.getElementById('name-air-quality').textContent = 'Air Quality (' + airQuality.label + ')';

            // Temperature
            var temperature = mapToColor(report.temperatureScore, 'Temperature');
            document.getElementById('color-temperature').style.backgroundColor = temperature.color;
            document.getElementById('name-temperature').textContent = 'Temperature (' + temperature.label + ')';

            // Population
            var population = mapToColor(report.populationScore, 'Population');
            document.getElementById('color-population').style.backgroundColor = population.color;
            document.getElementById('name-population').textContent = 'Population (' + population.label + ')';

            // Vegetation
            var vegetation = mapToColor(report.vegetationScore, 'Vegetation');
            document.getElementById('color-vegetation').style.backgroundColor = vegetation.color;
            document.getElementById('name-vegetation').textContent = 'Vegetation (' + vegetation.label + ')';
        }

        // Function to process report
        function processReport() {
            var randomReport = reports[Math.floor(Math.random() * reports.length)];
            displayReport(randomReport);
            document.getElementById('report').style.display = 'block';
        }

    </script>
</body>
</html>

        <!-- Form for Saving Location -->
        <form id="save-location-form" method="POST" action="map.php" class="hidden">
            <input type="hidden" id="location-text" name="location_text">
            <input type="hidden" id="location-coordinates" name="location_coordinates">
        </form>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $mapApiKey; ?>&libraries=places&callback=initMap" async defer></script>
<script>

      var locations = {
      'Aluthkade_East': { lat: 6.9406, lng: 79.8536 },
      'Aluthkade_West': { lat: 6.9373, lng: 79.8545 },
      'Bloemendhal': { lat: 6.9590, lng: 79.8680 },
      'Colombo_Fort': { lat: 6.9344, lng: 79.8438 },
      'Grandpass_North': { lat: 6.9551, lng: 79.8745 },
      'Grandpass_South': { lat: 6.9492, lng: 79.8740 },
      'Kotahena_East': { lat: 6.9422, lng: 79.8622 },
      'Kotahena_West': { lat: 6.9435, lng: 79.8600 },
      'Madampitiya': { lat: 6.9600, lng: 79.8755 },
      'Maligawatta_East': { lat: 6.9326, lng: 79.8731 },
      'Maligawatta_West': { lat: 6.9325, lng: 79.8699 },
      'Maradana': { lat: 6.9286, lng: 79.8691 },
      'Mattakkuliya': { lat: 6.9680, lng: 79.8782 },
      'Modara': { lat: 6.9644, lng: 79.8657 },
      'Panchikawatta': { lat: 6.9266, lng: 79.8706 },
      'Pettah': { lat: 6.9375, lng: 79.8489 },
      'Slave_Island': { lat: 6.9242, lng: 79.8506 },
      'Bambalapitiya': { lat: 6.8881, lng: 79.8534 },
      'Borella_North': { lat: 6.9274, lng: 79.8732 },
      'Borella_South': { lat: 6.9244, lng: 79.8742 },
      'Dematagoda': { lat: 6.9364, lng: 79.8786 },
      'Havelock_Town': { lat: 6.8888, lng: 79.8652 },
      'Kirulapone': { lat: 6.8909, lng: 79.8703 },
      'Kollupitiya': { lat: 6.9204, lng: 79.8470 },
      'Kurunduwatta': { lat: 6.9261, lng: 79.8618 },
      'Narahenpita': { lat: 6.8895, lng: 79.8723 },
      'Pamankada_East': { lat: 6.8813, lng: 79.8707 },
      'Pamankada_West': { lat: 6.8812, lng: 79.8671 },
      'Wellawatta_North': { lat: 6.8721, lng: 79.8613 },
      'Wellawatta_South': { lat: 6.8722, lng: 79.8581 },
      'Baththaramulla': { lat: 6.9276, lng: 79.9635 },
      'Dehiwala': { lat: 6.8549, lng: 79.8650 },
      'Homagama': { lat: 6.8424, lng: 80.0026 },
      'Kaduwela': { lat: 6.9308, lng: 79.9691 },
      'Kalubovila': { lat: 6.8522, lng: 79.8666 },
      'Kohuwala': { lat: 6.8608, lng: 79.8686 },
      'Kolonnawa': { lat: 6.9305, lng: 79.8908 },
      'Kottawa': { lat: 6.8406, lng: 79.9650 },
      'Maharagama': { lat: 6.8463, lng: 79.9276 },
      'Malabe': { lat: 6.9148, lng: 79.9578 },
      'Moratuwa': { lat: 6.7723, lng: 79.8829 },
      'Mount_Lavinea': { lat: 6.8333, lng: 79.8643 },
      'Nawala': { lat: 6.9039, lng: 79.8820 },
      'Nugegoda': { lat: 6.8649, lng: 79.9016 },
      'Oruwala': { lat: 6.9105, lng: 79.9815 },
      'Pannipitiya': { lat: 6.8490, lng: 79.9614 },
      'Piliyandala': { lat: 6.8010, lng: 79.9478 },
      'Ragagiriya': { lat: 6.9124, lng: 79.8917 },
      'Rathmalana': { lat: 6.8274, lng: 79.8718 },
      'Thalawathugoda': { lat: 6.8730, lng: 79.9480 },
      'Union_Place': { lat: 6.9167, lng: 79.8521 },
      'Walikada': { lat: 6.9120, lng: 79.8921 }
    };



    // Function to generate random number between min and max
    function getRandomNumber(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

function getRandomColor() {
    const colors = ["lightgreen", "darkgreen", "red", "yellow", "orange"];
    const randomIndex = Math.floor(Math.random() * colors.length);
    return colors[randomIndex];
}


function saveLocation(){
  const loc = $('#selLoc').val();

  var selectedCoords = locations[loc];
  var newUrl = 'map.php?location_text='+ loc +'&location_coordinates=' + selectedCoords.lat + ',' + selectedCoords.lng;
  window.location.href = newUrl;
}


    // Function to generate random shapes filling the container
    function generateRandomShapes() {
        var container = document.getElementById('top-layer');
        container.innerHTML = ''; // Clear previous shapes

        var totalArea = container.offsetWidth * container.offsetHeight; // Total area of the container
        var coveredArea = 0;

        while (coveredArea < totalArea) {
            var shapeType = getRandomNumber(1, 3); // Random shape type: 1 = circle, 2 = rectangle, 3 = triangle

            var shape = document.createElement('div');
            shape.style.position = 'absolute';
            shape.style.backgroundColor = getRandomColor();
            shape.style.opacity = '0.7'; // Optional: Set opacity for transparency effect

            switch (shapeType) {
                case 1:
                    // Rectangle
                    var width = getRandomNumber(50, 150);
                    var height = getRandomNumber(50, 150);
                    shape.style.width = width + 'px';
                    shape.style.height = height + 'px';
                    break;
                case 2:
                    // Rectangle
                    var width = getRandomNumber(50, 150);
                    var height = getRandomNumber(50, 150);
                    shape.style.width = width + 'px';
                    shape.style.height = height + 'px';
                    break;
                case 3:
                    // Triangle (using CSS border technique)
                    var width = getRandomNumber(50, 150);
                    var height = getRandomNumber(50, 150);
                    shape.style.width = width + 'px';
                    shape.style.height = height + 'px';
                    break;
                default:
                    break;
            }

            // Set random position within the container
            var posX = getRandomNumber(0, container.offsetWidth - parseInt(shape.style.width));
            var posY = getRandomNumber(0, container.offsetHeight - parseInt(shape.style.height));
            shape.style.left = posX + 'px';
            shape.style.top = posY + 'px';

            // Calculate covered area by the shape and update total covered area
            var shapeArea = parseInt(shape.style.width) * parseInt(shape.style.height);
            coveredArea += shapeArea;

            container.appendChild(shape);
        }
    }



// document.getElementById('feedbackForm').addEventListener('submit', function(event) {
//     event.preventDefault(); // Prevents the default form submission behavior
//     submitFeedback(event);
// });

// function submitFeedback(event) {
//     // Your feedback submission logic here
//     console.log("Feedback submitted");
// }


    function getQueryParam(param) {
      let params = new URLSearchParams(window.location.search);
      return params.get(param);
    }

    function initMap() {
      // Coordinates for Colombo district boundary
      var colomboBounds = {
        north: 7.0000,
        south: 6.7800,
        east: 80.0300,
        west: 79.8000
      };

    var defaultLocation = { lat: 6.9271, lng: 79.8612 };
    var locationParam = getQueryParam('location_coordinates');
    var mapCenter;


    if (locationParam) {
      var coords = locationParam.split(',');
      if (coords.length === 2) {
        var lat = parseFloat(coords[0]);
        var lng = parseFloat(coords[1]);
        if (!isNaN(lat) && !isNaN(lng)) {
          mapCenter = { lat: lat, lng: lng };
        } else {
          mapCenter = defaultLocation;
        }
      } else {
        mapCenter = defaultLocation;
      }
    } else {
      mapCenter = defaultLocation;
    }

    var loc = getQueryParam('location');

    var selectElement = document.getElementById('selLoc');
        for (var i = 0; i < selectElement.options.length; i++) {
          var option = selectElement.options[i];
          if (option.value === loc) {
            option.selected = true;
            break;
          }
        }


      var map = new google.maps.Map(document.getElementById('map'), {
        center: mapCenter,
        zoom: 16,
        restriction: {
          latLngBounds: colomboBounds,
          strictBounds: true
        },
        minZoom: 12,
        maxZoom: 20
      });

      // Prevent zoom out
      map.addListener('zoom_changed', function() {
        if (map.getZoom() < 12) map.setZoom(12);
      });



      // Filter places based on selected type
      document.querySelectorAll('.filter-button').forEach(function(button) {
        button.addEventListener('click', function() {
          var type = this.getAttribute('data-type');
          console.log("SDfsd");
          filterPlaces(type);
        });
      });

          var markers = [];


      function filterPlaces(type) {
        // Clear out the old markers.
        markers.forEach(function(marker) {
          marker.setMap(null);
        });
        markers = [];

        // Define place types and their corresponding keywords
        var placeTypes = {
          'walking_paths': 'walking path',
          'play_areas': 'play area',
          'green_parks': 'green park',
          'water_features': 'water feature',
          'natural_trails': 'natural trail'
        };

        var service = new google.maps.places.PlacesService(map);
        service.textSearch({
          location: defaultLocation,
          radius: 5000,
          query: placeTypes[type]
        }, function(results, status) {
          if (status === google.maps.places.PlacesServiceStatus.OK) {
            var bounds = new google.maps.LatLngBounds();
            results.forEach(function(place) {
              if (!place.geometry || !place.geometry.location) {
                console.log("Returned place contains no geometry");
                return;
              }

              var marker = new google.maps.Marker({
                map: map,
                title: place.name,
                position: place.geometry.location
              });
              markers.push(marker);

              if (place.geometry.viewport) {
                bounds.union(place.geometry.viewport);
              } else {
                bounds.extend(place.geometry.location);
              }
            });
            map.fitBounds(bounds);
          }
        });
      }
    }

    // Initialize the map
    google.maps.event.addDomListener(window, 'load', initMap);

        function updateLocation(selectedLocation) {


      if (selectedLocation !== 'null') {
        var selectedCoords = locations[selectedLocation];
        var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?location='+ selectedLocation +'&location_coordinates=' + selectedCoords.lat + ',' + selectedCoords.lng;
        window.location.href = newUrl;
      }
    }



  </script>



<script>

  let feedbackVar = true;

            document.addEventListener("DOMContentLoaded", function() {
            // Get all anchor tags on the page
            var allLinks = document.getElementsByTagName("a");

            // Iterate through each link
            for (var i = 0; i < allLinks.length; i++) {
                // Add event listener to each link
                allLinks[i].addEventListener("click", function(event) {
                     event.preventDefault(); // Prevent the default action (e.g., following the link)

                    // Example: Log the href of the clicked link
                    console.log("Clicked link:", this.href);

                    var href = this.getAttribute("href");


                    if (feedbackVar && href && href.indexOf("spec.php") === -1) {

                      $('#feedbackModal').show();
                      feedbackVar = false;


                    }else{
                        window.location.href = href;

                    }

                });
            }
        });
</script>

<script>
    let stars = document.getElementsByClassName("star");
    let output = document.getElementById("output");
    let ratingInput = document.getElementById("ratingInput");

    // Function to update rating display
    function updateRating(n) {
        ratingInput.value = n;
        for (let i = 0; i < 5; i++) {
            if (i < n) {
                stars[i].classList.add("active");
            } else {
                stars[i].classList.remove("active");
            }
        }
        output.innerText = "Rating is: " + n + "/5";
    }

    // Modal functionality
    const modal = document.getElementById("feedbackModal");
    const span = document.getElementsByClassName("close")[0];

    // Close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }


</script>

</body>
</html>
