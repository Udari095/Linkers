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

$message = "";

// Fetch user data to prepopulate form fields
$current_email = $_SESSION['email'];
$sql = "SELECT email, first_name, last_name, mobile_phone, address, city, zip_code FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_email);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_details'])) {
        // Retrieve all fields from the form
        $new_email = $_POST['email'];
        $new_first_name = $_POST['first_name'];
        $new_last_name = $_POST['last_name'];
        $new_mobile_phone = $_POST['mobile_phone'];
        $new_address = $_POST['address'];
        $new_city = $_POST['city'];
        $new_zip_code = $_POST['zip_code'];

        // Prepare and execute the update query
        $stmt = $conn->prepare("UPDATE users SET email = ?, first_name = ?, last_name = ?, mobile_phone = ?, address = ?, city = ?, zip_code = ? WHERE email = ?");
        $stmt->bind_param("ssssssss", $new_email, $new_first_name, $new_last_name, $new_mobile_phone, $new_address, $new_city, $new_zip_code, $current_email);

        if ($stmt->execute()) {
            // Update session email if email is updated
            $_SESSION['email'] = $new_email;
            $message = "Details updated successfully";
        } else {
            $message = "Failed to update details";
        }

        $stmt->close();
    }

    if (isset($_POST['update_password'])) {
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $current_email = $_SESSION['email']; // Assuming the current email is stored in the session

        if ($new_password !== $confirm_password) {
            $message = "Passwords do not match";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $current_email);

            if ($stmt->execute()) {
                $message = "Password updated successfully";
            } else {
                $message = "Failed to update password";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
    <div class="flex-1 bg-white p-8">
        <h2 class="text-3xl font-semibold mb-6">Edit Profile</h2>

        <!-- Message -->
        <?php if ($message): ?>
            <div class="mb-4 p-4 text-white bg-green-500 rounded">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Update Details Form -->
        <div class="mb-8">
            <h3 class="text-2xl font-semibold mb-4">Update Details</h3>
            <form action="edit_profile.php" method="post" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                        <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" readonly value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div>
                        <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">First name</label>
                        <input type="text" name="first_name" id="first_name" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div>
                        <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last name</label>
                        <input type="text" name="last_name" id="last_name" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                    <div>
                        <label for="mobile_phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mobile phone</label>
                        <input type="tel" name="mobile_phone" id="mobile_phone" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?php echo htmlspecialchars($user['mobile_phone']); ?>" required>
                    </div>
                    <div>
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                        <input type="text" name="address" id="address" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                    </div>
                    <div>
                        <label for="city" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">City</label>
                        <input type="text" name="city" id="city" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                    </div>
                    <div>
                        <label for="zip_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Zip code</label>
                        <input type="text" name="zip_code" id="zip_code" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?php echo htmlspecialchars($user['zip_code']); ?>" required>
                    </div>
                </div>
                <button type="submit" name="update_details" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Update Details</button>
            </form>
        </div>

        <!-- Update Password Form -->
        <div>
            <h3 class="text-2xl font-semibold mb-4">Update Password</h3>
            <form action="profile.php" method="post" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    </div>
                    <div>
                        <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    </div>
                </div>
                <button type="submit" name="update_password" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Update Password</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
