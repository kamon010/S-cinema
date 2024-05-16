<?php
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if query parameter exists
    if(isset($_POST['query'])){
        $query = $_POST['query'];
        
        // Prepare SQL statement to search for directors whose names match the query
        $sql = "SELECT * FROM Director WHERE NameDirector LIKE :query ORDER BY NameDirector";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->execute();
        $directors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If there are matching directors, create HTML list items to display them
        if($directors){
            foreach($directors as $director){
                echo '<li class="list-group-item">' . $director['NameDirector'] . '</li>';
            }
        } else {
            // If no matching directors found, display a message
            echo '<li class="list-group-item">No directors found</li>';
        }
    }
} catch(PDOException $e) {
    // Handle database errors
    echo '<li class="list-group-item">Error: ' . $e->getMessage() . '</li>';
}
?>
