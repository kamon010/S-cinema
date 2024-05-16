<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: loginContrl.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id']) && isset($_POST['room_status'])) {
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL statement to update room status
        $sql = "UPDATE Room SET Roomstatus = :room_status WHERE RoomID = :room_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':room_status', $_POST['room_status']);
        $stmt->bindParam(':room_id', $_POST['room_id']);
        $stmt->execute();

        // Redirect back to room status page with success message
        header("Location: room_status.php?success=1");
        exit();
    } catch(PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
} else {
    // Redirect back to room status page if no POST data is received
    header("Location: room_status.php");
    exit();
}
?>
