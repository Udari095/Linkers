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

require './vendors/phpmailer/Exception.php';
require './vendors/phpmailer/PHPMailer.php';
require './vendors/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli($DBservername, $DBusername, $DBpassword, $DBname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'], $_POST['delete'])) {
        $user_id = $_POST['user_id'];

        // Get user email before deleting
        $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();

        // Delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $EmailUser;
            $mail->Password = $EmailPassword;

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                )
            );

            // $mail->SMTPDebug = 2; // Enable detailed debug output
            // $mail->Debugoutput = 'html'; // Output debug information in HTML format

            //Recipients
            $mail->setFrom('noreply@yourwebsite.com', 'Your Website');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Account deactivation';
            $mail->Body = "Your account has been deactivated successfully!";

            $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } 
}

// Fetch all user data
$sql = "SELECT id, email, first_name, last_name, mobile_phone, role, status, deactivation_requested FROM users";
$result = $conn->query($sql);

$deactivation_requests = [];
$normal_users = [];

while ($row = $result->fetch_assoc()) {
    if ($row['deactivation_requested']) {
        $deactivation_requests[] = $row;
    }
    $normal_users[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users</title>
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
    <div class="flex-1 bg-gray-200 p-8">
        <h2 class="text-3xl font-semibold mb-6">All Users</h2>

        <!-- Deactivation Requests -->
        <?php if (!empty($deactivation_requests)): ?>
            <div class="mb-6">
                <h3 class="text-2xl font-semibold mb-4 text-red-600">Deactivation Requests</h3>
                <?php foreach ($deactivation_requests as $request): ?>
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 rounded">
                        <p class="text-red-600">User with email <?php echo htmlspecialchars($request['email']); ?> requested to deactivate the account.</p>
                        <form method="POST" action="">
                            <input type="hidden" name="user_id" value="<?php echo $request['id']; ?>">
                            <input type="hidden" name="delete" value="true">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">Deactivate</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Normal User Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">Email</th>
                        <th class="px-4 py-2 border">First Name</th>
                        <th class="px-4 py-2 border">Last Name</th>
                        <th class="px-4 py-2 border">Mobile Phone</th>
                        <th class="px-4 py-2 border">Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($normal_users as $user): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($user['first_name']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($user['last_name']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($user['mobile_phone']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($user['role']); ?></td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>