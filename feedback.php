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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_text'])) {
    $current_email = $_SESSION['email'];
    $experience = $_POST['experience'];
    $comment = $_POST['comment'];
    $area_text = $_POST['area_text'];
    $feedback_text = $_POST['feedback_text'];
    $rating = intval($_POST['rating']);

    // Validate inputs
    if (empty($experience) || empty($comment) || empty($area_text) || empty($feedback_text) || $rating <= 0) {
        $message = "Experience, comment, area_text, feedback_text, and rating are required!";
    } else {
        // Prepare and bind SQL statement
        $sql = "INSERT INTO feedback (email, experience, comment, area_text, feedback_text, rating) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $current_email, $experience, $comment, $area_text, $feedback_text, $rating);

        // Execute the statement
        if ($stmt->execute()) {
            $message = "Feedback submitted successfully!";
            header('Location: home.php');
            exit();
        } else {
            $message = "Error submitting feedback: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback</title>
</head>
<body>
    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post" action="">
        Experience: <input type="text" name="experience"><br>
        Comment: <input type="text" name="comment"><br>
        Area: <input type="text" name="area_text"><br>
        Feedback: <textarea name="feedback_text"></textarea><br>
        Rating: <input type="number" name="rating" min="1" max="5"><br>
        <input type="submit" value="Submit Feedback">
    </form>
</body>
</html>
