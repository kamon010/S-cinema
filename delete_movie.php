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

// Check if movie_id is set
if (isset($_GET['movie_id'])) {
    $movie_id = $_GET['movie_id'];

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare a DELETE statement
        $sql = "DELETE FROM Movie WHERE MovieID = :movie_id";
        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
        
        // Execute the statement
        $stmt->execute();

        // Redirect back to the movies page
        header("Location: ShowMovie.php");
        exit();
    } catch(PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
} else {
    // If movie_id is not set, redirect to the movies page
    header("Location: ShowMovie.php");
    exit();
}
?>
