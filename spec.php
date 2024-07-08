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


$title = htmlspecialchars($_GET['spec']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colombo Green Explorer</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        #map {
            height: 100%;
            width: 100%;
        }
        .pac-container {
            z-index: 10000 !important;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="flex flex-col min-h-screen">
        <?php include("./includes/navbar.php"); ?>

            <div class="flex flex-1">

    <!-- Sidebar -->
    <div class="w-1/5 bg-green-800 text-white p-4">
        <?php include("./includes/sidebar.php"); ?>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-gray-200 p-8">
        <h2 class="text-3xl font-semibold mb-6"><?php echo $title; ?></h2>

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
                <button onclick="generateRandomShapes()"  class="w-1/5 p-2 bg-blue-500 text-white rounded mt-4">Process</button>
            </div>
        </div>
        


        
        <!-- Map Container -->
<div class="relative w-full h-screen">
  <div id="map" class="w-full h-full"></div>
  <div id="top-layer" class="absolute inset-0 w-full flex items-center justify-center  opacity-50">
  </div>
</div>



        <!-- Form for Saving Location -->
        <form id="save-location-form" method="POST" action="map.php" class="hidden">
            <input type="hidden" id="location-text" name="spec" value="<?php echo htmlspecialchars($_GET['spec']); ?>">
            <input type="hidden" id="location-text" name="location_text">
            <input type="hidden" id="location-coordinates" name="location_coordinates">
        </form>

        <!-- Feedback Modal -->


    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $mapApiKey; ?>&libraries=places&callback=initMap" async defer></script>
<script>


    // Function to generate random number between min and max
    function getRandomNumber(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

function getRandomColor() {
    const colors = ["lightgreen", "darkgreen", "red", "yellow", "orange"];
    const randomIndex = Math.floor(Math.random() * colors.length);
    return colors[randomIndex];
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
        maxZoom: 16
      });

      // Prevent zoom out
      map.addListener('zoom_changed', function() {
        if (map.getZoom() < 12) map.setZoom(12);
      });

      // Show feedback modal
      document.getElementById('give-feedback').addEventListener('click', function() {
        document.getElementById('feedback-modal').classList.remove('hidden');
      });

      // Hide feedback modal
      document.getElementById('cancel-feedback').addEventListener('click', function() {
        document.getElementById('feedback-modal').classList.add('hidden');
      });

      // Filter places based on selected type
      document.querySelectorAll('.filter-button').forEach(function(button) {
        button.addEventListener('click', function() {
          var type = this.getAttribute('data-type');
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

      if (selectedLocation !== 'null') {
        var selectedCoords = locations[selectedLocation];
        var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname +'?spec=<?php echo htmlspecialchars($_GET['spec']); ?>' + '&location='+ selectedLocation +'&location_coordinates=' + selectedCoords.lat + ',' + selectedCoords.lng;
        window.location.href = newUrl;
      }
    }



  </script>


</body>
</html>
