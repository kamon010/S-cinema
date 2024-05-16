<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a file is uploaded
    if(isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        // Connect to the database
        $conn = new mysqli($host, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get the poster ID from the form
        $poster_id = $_POST['poster_id'];

        // Prepare a SQL statement to delete the old poster image
        $sql_delete = "UPDATE poster_promotion SET Poster = NULL WHERE PosterID = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param('i', $poster_id);

        // Execute the delete statement
        if ($stmt_delete->execute()) {
            // Close the delete statement
            $stmt_delete->close();

            // Upload the new poster image
            $poster_tmp_name = $_FILES['poster']['tmp_name'];
            $poster_name = $_FILES['poster']['name'];

            // Read the file data
            $poster_data = file_get_contents($poster_tmp_name);

            // Prepare a SQL statement to update the poster image
            $sql_update = "UPDATE poster_promotion SET Poster = ? WHERE PosterID = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param('si', $poster_data, $poster_id);

            // Execute the update statement
            if ($stmt_update->execute()) {
                echo "<script>alert('Poster updated successfully');</script>";
                echo "<script>window.location.href='poster_more.php';</script>";
            } else {
                echo "Error updating poster: " . $conn->error;
            }

            // Close the update statement
            $stmt_update->close();
        } else {
            echo "Error deleting old poster: " . $conn->error;
        }

        // Close the database connection
        $conn->close();
    } else {
        echo "No file uploaded";
    }
} else {
    echo "Invalid request";
}
?>
