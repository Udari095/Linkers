<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Colombo Green Explorer</title>
    <style>
        li > ul {
            display: none;
        }
        li:hover > ul {
            display: block;
        }
        .menu-icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <nav class="bg-green-800 text-white p-4 flex justify-between items-center">
        <div class="relative">


        </div>
    </nav>

    <ul>
        <li class="mb-4">
            <a href="home.php" class="text-gray-300 hover:text-white">
                <i class="fas fa-home menu-icon"></i>Home
            </a>
        </li>
        <li class="mb-4">
            <a href="saved_locations.php" class="text-gray-300 hover:text-white">
                <i class="fas fa-map-marker-alt menu-icon"></i>Saved locations
            </a>
        </li>
        <li class="mb-4">
            <a href="map.php" class="text-gray-300 hover:text-white">
                <i class="fas fa-map menu-icon"></i>View on Map
            </a>
        </li>

        <li class="mb-4">
            <a href="profile.php" class="text-gray-300 hover:text-white">
                <i class="fas fa-user menu-icon"></i>Profile
            </a>
        </li>

        <li class="mb-4">
            <a href="settings.php" class="text-gray-300 hover:text-white">
                <i class="fas fa-cog menu-icon"></i>Settings
            </a>
        </li>

        <?php if ($_SESSION['role'] === "user") { ?>
            <li class="mb-4">
                <a href="user_feedbacks.php" class="text-gray-300 hover:text-white">
                    <i class="fas fa-comments menu-icon"></i>Feedbacks
                </a>
            </li>
        <?php } ?>

        <?php if ($_SESSION['role'] === "admin") { ?>
            <li class="mb-4">
                <a href="admin_feedbacks.php" class="text-gray-300 hover:text-white">
                    <i class="fas fa-comments menu-icon"></i>Feedbacks
                </a>
            </li>

            <li class="mb-4">
                <a href="users.php" class="text-gray-300 hover:text-white">
                    <i class="fas fa-user-friends menu-icon"></i>Users
                </a>
            </li>
        <?php } ?>

        <li class="mb-4">
            <a onclick="return confirmLogout()" href="logout.php" class="text-gray-300 hover:text-white">
                <i class="fas fa-sign-out-alt menu-icon"></i>Logout
            </a>
        </li>

    </ul>

</body>
</html>

    <script>

    function confirmLogout() {
        return confirm('Are you sure you want to logout?');
    }
    </script>
