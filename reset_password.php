<?php
include("./includes/config.php");
$message = "";

if (!isset($_GET['token'])) {
header('Location: index.php');
exit(); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Create connection
        $conn = new mysqli($DBservername, $DBusername, $DBpassword, $DBname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if token is valid
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires >= ? LIMIT 1");
        $current_time = date("U");
        $stmt->bind_param("si", $token, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update user's password
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();

            // Delete the used token
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();

            $message = "Password reset successful.";
            header('Location: login.php?message='.$message);

        } else {
            $message = "Invalid or expired token.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .password-toggle {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Reset Password
                    </h1>
                    <form class="space-y-4 md:space-y-6" action="reset_password.php?token=<?php echo htmlspecialchars($_GET['token']); ?>" method="post">
                        <label for="message" class="block mb-2 text-sm font-medium text-center text-red-500">
                            <?php echo htmlspecialchars($message); ?>
                        </label>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">New Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center password-toggle">
                                    <svg id="password-eye" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12A3 3 0 119 12a3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-.826 2.907-2.956 5.345-5.5 6.472m-3.598 1.515c-.955.317-1.98.518-3.072.518a9.71 9.71 0 01-3.072-.518m-3.598-1.515C4.044 17.345 2.914 14.907 2.458 12z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div>
                            <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm Password</label>
                            <div class="relative">
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center password-toggle">
                                    <svg id="confirm-password-eye" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12A3 3 0 119 12a3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-.826 2.907-2.956 5.345-5.5 6.472m-3.598 1.515c-.955.317-1.98.518-3.072.518a9.71 9.71 0 01-3.072-.518m-3.598-1.515C4.044 17.345 2.914 14.907 2.458 12z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                        <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.querySelectorAll('.password-toggle').forEach(item => {
            item.addEventListener('click', event => {
                const input = event.target.closest('.relative').querySelector('input');
                const eyeIcon = event.target.closest('.password-toggle').querySelector('svg');
                if (input.type === 'password') {
                    input.type = 'text';
                    eyeIcon.setAttribute('stroke', 'red'); // Change eye color when showing password
                } else {
                    input.type = 'password';
                    eyeIcon.setAttribute('stroke', 'currentColor'); // Reset eye color when hiding password
                }
            });
        });
    </script>
</body>
</html>
