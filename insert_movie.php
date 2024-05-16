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

try {
    // Establish database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve form data
    $name = $_POST['name'];
    $genre = $_POST['genre'];
    $director = $_POST['director'];
    $actor_id = $_POST['actor'];
    $duration = $_POST['duration'];
    $room = $_POST['room'];
    $price = $_POST['price'];
    $release_date = $_POST['release_date'];
    $leaving_date = $_POST['leaving_date'];
    $poster = $_POST['poster'];
    $video_link = $_POST['video_link'];

    // Prepare SQL statement to insert movie details
    $sql = "INSERT INTO Movie (NameMovie, GenreID, DirectorID, Duration, RoomID, price, ReleaseDate, LeavingDate, Poster, LinkVDO) VALUES (:name, :genre, :director, :duration, :room, :price, :release_date, :leaving_date, :poster, :video_link)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':director', $director);
    $stmt->bindParam(':duration', $duration);
    $stmt->bindParam(':room', $room);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':release_date', $release_date);
    $stmt->bindParam(':leaving_date', $leaving_date);
    $stmt->bindParam(':poster', $poster);
    $stmt->bindParam(':video_link', $video_link);
    $stmt->execute();

    // Retrieve the ID of the last inserted movie
    $movie_id = $conn->lastInsertId();


        $sql = "INSERT INTO Movie_Actors (MovieID, ActorsID) VALUES ";
        $values = array();

        // Loop through selected actors and construct the values part of the SQL query
// Loop through selected actors and construct the values part of the SQL query
foreach ($_POST['actor'] as $actor_id) {
    if (!empty($actor_id)) {
        $values[] = "(:movie_id, :actor_id_$actor_id)";
    }
}

// If there are values to insert, execute the SQL query
if (!empty($values)) {
    $sql .= implode(',', $values);
    $stmt = $conn->prepare($sql);

    // Loop through selected actors again to bind parameters and execute the query
    foreach ($_POST['actor'] as $actor_id) {
        if (!empty($actor_id)) {
            $stmt->bindValue(':movie_id', $movie_id);
            $stmt->bindValue(":actor_id_$actor_id", $actor_id);
        }
    }

    $stmt->execute();
}



    // Redirect back to the form with success message
    header("Location: ShowMovie.php?success=1");
    exit();
} catch(PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
}
echo "<script>alert('เพิ่มหนังเสร็จแล้วจ้า');</script>";
?>
