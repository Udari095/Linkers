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

$conn = new mysqli($DBservername, $DBusername, $DBpassword, $DBname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$current_email = $_SESSION['email'];

// Fetch user details
$sql = "SELECT id, email, first_name, last_name, mobile_phone, address, city, zip_code, deactivation_requested FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_email);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

// Handle deactivation request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_deactivation'])) {
    $user_id = $user['id'];

    $stmt = $conn->prepare("UPDATE users SET deactivation_requested = TRUE WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Refresh user data after update
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $current_email);
    $stmt->execute();
    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

<div class="flex flex-col min-h-screen">
        <?php include("./includes/navbar.php"); ?>


    <div class="flex flex-1">

    <div class="w-1/5 bg-green-800 text-white p-4">
        <?php include("./includes/sidebar.php"); ?>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-gray-200 p-8">
        <h2 class="text-3xl font-semibold mb-6">Close user account</h2>
        
        <?php if ($user['deactivation_requested']): ?>
            <p class="mb-4 px-8 text-red-600">Deactivation request already submitted. Your request is being processed.</p>
        <?php else: ?>
            <p class="mb-4 px-8">You can request to close your user account by raising a request with simple steps. Deleting your account will permanently remove all your data and contributions from Colombo Explore. Are you sure you want to proceed? Your action cannot be undone.</p>
            <form method="POST" action="">
                <button type="submit" name="request_deactivation" class="ml-6 px-6 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Raise request</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
