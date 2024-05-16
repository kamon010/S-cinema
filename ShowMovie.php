<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginContrl.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT Movie.*, Room.TypeScreen,Room.Roomname, Genre.Genre
            FROM Movie
            LEFT JOIN Room ON Movie.RoomID = Room.RoomID
            LEFT JOIN Genre ON Movie.GenreID = Genre.GenreID
            WHERE Movie.LeavingDate > CURDATE() OR Movie.LeavingDate IS NULL";
    $stmt = $conn->query($sql);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Check if movie ID is provided for deletion
if (isset($_GET['movie_id'])) {
    try {
        // Prepare SQL statement to delete movie
        $sql = "DELETE FROM Movie WHERE MovieID = :movie_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':movie_id', $_GET['movie_id']);
        $stmt->execute();

        // Redirect back to movie list page after deletion
        header("Location: ShowMovie.php");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        .navbar {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 9999;
            margin-bottom: 20px; /* เพิ่มพื้นที่ด้านล่างของ Navbar */
        }
        .navbar-brand {
            position: relative;
            left: 20px;
            margin-right: 60px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: bold;
            color: #333333;
            background-color: #382628;
            background-size: cover;
            background-position: center;
            backdrop-filter: blur(10px);
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: bold;
            color: #333333;
            margin: 0 auto;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            transform: scale(0.985);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: none; 
        }
        th{
            color: #333333;

            padding: 8px;
            text-align: center;
        }
        td {
            color: #333333;
            padding: 8px;
            text-align: center;
            word-wrap: break-word;
        }



        th {
            background-color: #382628;
            color: white;
        }
        .edit-btn {
            background: linear-gradient(135deg, #382628, #73373E);
            padding: 5px 10px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .edit-btn:hover {
            background-color: #3972AF;
        }
        img {
            display: block;
            margin: 0 auto;
            max-width: 100px;
            height: auto;
        }

        .btn-container {
            text-align: center;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #382628, #73373E);
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #F0368D;
        }

        .delete-btn {
            background: linear-gradient(135deg, #382628, #73373E);
            padding: 5px 10px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-center">
    <a class="navbar-brand" href="#">Scinema</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="ShowMovie.php">Movie</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all_actors.php">Actors</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all_director.php">Directors </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="promotion_more.php">Promotion</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="room_status.php">Room status</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="poster_more.php">Poster</a>
            </li>
        </ul>
    </div>
    <div class="dropdown" style="margin-right: 20px;">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<i class="fas fa-user icon" style="margin-right: 10px;"></i>' . $_SESSION['user_name'];
            } else {
                echo 'Login';
            }
            ?>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php
        if (isset($_SESSION['user_id'])) {
            echo '<a class="dropdown-item" href="logoutContrl.php">Logout</a>';
            echo '<a class="dropdown-item" href="edit_pass_control.php">Change Credent</a>';
        }
        ?>
    </div>
    </div>
</nav>

<div class="container">
        <h2>Movies</h2>
        <button class="btn-secondary" onclick="location.href='add_movie.php'">Add New Movie</button><br><br>
        <table>
            <tr>
                <th>NameMovie</th>
                <th>Genre</th>
                <th>TypeScreen</th>
                <th>Duration</th>
                <th>Price</th>
                <th>ReleaseDate</th>
                <th>LeavingDate</th>
                <th>Poster</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            <?php foreach ($movies as $movie): ?>
                <tr>
                    <td><?php echo $movie['NameMovie']; ?></td>
                    <td><?php echo $movie['Genre']; ?></td>
                    <td><?php echo $movie['TypeScreen'] . "/" . $movie['Roomname']; ?></td>
                    <td><?php echo $movie['Duration']; ?></td>
                    <td><?php echo $movie['price']; ?></td>
                    <td><?php echo $movie['ReleaseDate']; ?></td>
                    <td><?php echo $movie['LeavingDate']; ?></td>
                    <td><img src="<?php echo $movie['Poster']; ?>" alt="Movie Poster"></td>
                    <td class="btn-container"><button class="edit-btn" data-movieid="<?php echo $movie['MovieID']; ?>">Edit</button></td>
                    <td class="btn-container"><button class="delete-btn" data-movieid="<?php echo $movie['MovieID']; ?>">Delete</button></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- JavaScript code -->
    <script>
        // Add event listeners to all delete buttons
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const movieId = this.getAttribute('data-movieid');
                const confirmDelete = confirm('Are you sure you want to delete this movie?');
                if (confirmDelete) {
                    // Redirect to delete_movie.php with movieId as parameter
                    window.location.href = `ShowMovie.php?movie_id=${movieId}`;
                }
            });
        });
    </script>

    <script>
        // Add event listeners to all edit buttons
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const movieId = this.getAttribute('data-movieid');
                // Redirect to edit_movie.php with movieId as parameter
                window.location.href = `edit_movie.php?movie_id=${movieId}`;
            });
        });
    </script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
</html>