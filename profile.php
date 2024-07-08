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
$sql = "SELECT email, first_name, last_name, mobile_phone, address, city, zip_code, profile_picture FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_email);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <script>
        function redirectToIndex(event) {
            event.preventDefault();
            window.location.href = 'edit_profile.php';
        }
    </script>
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
            <h2 class="text-3xl font-semibold mb-6">Profile</h2>
            
            <!-- Profile Details -->
            <div class="mb-8 bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-6">
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="w-24 h-24 rounded-full mr-4 border-2 border-blue-500">
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></h3>
                        <p class="text-green-500">Active</p>
                    </div>
                </div>
                
                <form action="upload_profile_picture.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="profile_picture" accept="image/*" required>
                    <button type="submit" class="px-6 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Upload New Profile Picture</button>
                </form>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-700">Email:</p>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-700">First Name:</p>
                        <p><?php echo htmlspecialchars($user['first_name']); ?></p>
                    </div>
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-700">Last Name:</p>
                        <p><?php echo htmlspecialchars($user['last_name']); ?></p>
                    </div>
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-700">Mobile Phone:</p>
                        <p><?php echo htmlspecialchars($user['mobile_phone']); ?></p>
                    </div>
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-700">Address:</p>
                        <p><?php echo htmlspecialchars($user['address']); ?></p>
                    </div>
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-700">City:</p>
                        <p><?php echo htmlspecialchars($user['city']); ?></p>
                    </div>
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-700">Zip Code:</p>
                        <p><?php echo htmlspecialchars($user['zip_code']); ?></p>
                    </div>
                </div>
                
            </div>
            <button onclick="redirectToIndex(event)" class="px-6 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Edit profile</button>
        </div>
    </div>
</div>

</body>
</html>

