<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colombo Green Explorer</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <style>
        /* Additional CSS for the image slider */
        .slider {
            width: 100%;
            max-width: 1000px;
            overflow: hidden;
            position: relative;
            margin: 0 auto;
        }
        
        .slides {
            display: flex;
            transition: transform 1s ease;
        }
        
        .slides img {
            width: 100%;
            height: auto;
            flex: 1 0 100%;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="flex flex-col min-h-screen">
    <!-- Top Navigation Bar -->
        <?php include("./includes/navbar.php"); ?>


    <div class="flex flex-1">
        <!-- Sidebar -->
        <div class="w-1/5 bg-green-800 text-white p-4">
            <?php include("./includes/sidebar.php"); ?>
        </div>

        <div class="flex-1 bg-gray-300 p-8">
            <h2 class="text-3xl font-semibold mb-6">Welcome to Colombo Green Explorer</h2>

            <div class="bg-gray-100 rounded-lg shadow-lg cursor-pointer w-3/5 mx-auto">
        <!-- Image Slider -->
        <div class="slider bg-gray-100 rounded-lg shadow-lg cursor-pointer w-3/5 mx-auto">
            <div class="slides">
                        <img src="./images/image1.jpeg" alt="Image 1" />
                        <img src="./images/image2.jpg" alt="Image 2" />
                        <img src="./images/image3.jpg" alt="Image 3" />
                        <img src="./images/image4.jpg" alt="Image 4" />
            </div>
        </div>                <h3 class="text-xl px-3 font-semibold mt-4">Welcome to COLOMBO GREEN EXPLORER</h3>
                <p class="mt-2 px-3 text-gray-600"; style="text-align:justify;">            
                    Explore the health and livability of Colombo urban areas with Colombo Green Explorer.

                    Whether you're a resident or just visiting, easily identify the best places to live and explore based on environmental quality and livability. 
                    Discover the healthiest spots in Colombo, save your favourite locations, and contribute to community feedback to help others make informed decisions.

                    Start your journey to a greener, healthier Colombo today
                </p>

            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript for Image Slider
    const slides = document.querySelector('.slides');
    const images = document.querySelectorAll('.slides img');
    let currentIndex = 0;
    const totalImages = images.length;

    function showNextImage() {
        // If currentIndex is the last image, reset to the first image
        if (currentIndex === totalImages - 1) {
            // Temporarily disable transition for instant jump to the first image
            slides.style.transition = 'none';
            currentIndex = 0;
            slides.style.transform = `translateX(0)`;
            
            // Force a reflow to apply the transform instantly
            slides.offsetHeight; // This is to force a reflow, necessary for transition to work correctly

            // Re-enable the transition for the next move
            setTimeout(() => {
                slides.style.transition = 'transform 1s ease';
            }, 50); // Short delay to ensure it applies for the next move
        } else {
            // Move to the next image
            currentIndex++;
            slides.style.transform = `translateX(${-currentIndex * 100}%)`;
        }
    }

    // Change the image every 10 seconds
    setInterval(showNextImage, 5000);
</script>

</body>
</html>