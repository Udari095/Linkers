<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Colombo Green Explorer</title>
</head>
<body>
    <nav class="bg-green-700 text-white p-4 flex justify-between items-center">
        <img src="./images/rr.png" style="height:35px; width:35px">
        <div class="text-2xl font-semibold" style="text-align:left">Colombo Green Explorer</div>
        <div class="relative">
            <button id="userMenuButton" class="focus:outline-none flex items-center">
                <span class="mr-3 mb-2"> Hi, <?php echo $_SESSION['first_name']; ?></span>
                <?php if (isset($_SESSION['profile_picture'])): ?>
                    <img src="<?php echo $_SESSION['profile_picture']; ?>" alt="Profile Picture" class="w-8 h-8 rounded-full">
                <?php else: ?>
                    <i class="fas fa-user-circle text-2xl"></i>
                <?php endif; ?>
            </button>
            <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                <a href="profile.php" class="block px-4 py-2 text-green-600 hover:bg-gray-200">Profile</a>
                <a href="logout.php" class="block px-4 py-2 text-green-600 hover:bg-gray-200" onclick="return confirmLogout()">Logout</a>
            </div>
        </div>
    </nav>

    <script>
    document.getElementById('userMenuButton').addEventListener('click', function() {
        var menu = document.getElementById('userMenu');
        menu.classList.toggle('hidden');
    });

    function confirmLogout() {
        return confirm('Are you sure you want to logout?');
    }
    </script>
</body>
</html>
