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

$current_email = $_SESSION['email'];

// Fetch all user data
$sql = "SELECT * FROM saved_locations WHERE email = '$current_email' ORDER BY created_at DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        .min-w-full.bg-white.border {
            width: 100%;
            background-color: white;
            border-collapse: collapse;
            border: 1px solid #003366; /* Dark Blue Border */
        }

        .min-w-full.bg-white.border th, 
        .min-w-full.bg-white.border td {
            border: 1px solid #003366; /* Dark Blue Border */
            padding: 8px;
        }

        .min-w-full.bg-white.border th {
            background-color: #003366; /* Dark Blue */
            color: white;
        }

        .min-w-full.bg-white.border tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .min-w-full.bg-white.border tr:hover {
            background-color: #ddd;
        }

        .min-w-full.bg-white.border td {
            color: #003366; /* Dark Blue for text */
        }

        .table-container {
            margin: 20px;
        }
    </style>

</head>
<body class="bg-gray-100">

<div class="flex flex-col min-h-screen">
    <?php include("./includes/navbar.php"); ?>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-1/5 bg-green-800 text-white p-4">
            <?php include("./includes/sidebar.php"); ?>
        </div>

        <!-- Main Content -->
        <div class="flex-1 bg-gray-200 p-8">
            <h2 class="text-3xl font-semibold mb-6">Save Locations</h2>

            <!-- Normal User Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border">Location</th>
                            <th class="px-4 py-2 border">Coordinate</th>
                            <th class="px-4 py-2 border">Date</th>
                            <th class="px-4 py-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['location_text']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['location_coordinates']); ?></td>
                            <td class="px-4 py-2 border"><?php echo date('Y-m-d'); ?></td>
                            <td class="px-4 py-2 border">
                                <form method="GET" action="map.php">
                                    <input type="hidden" name="location_coordinates" value="<?php echo $row['location_coordinates']; ?>">
                                    <input type="hidden" name="location" value="<?php echo $row['location_text']; ?>">
                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                                        View
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>