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
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // เรียกข้อมูลจากตาราง Genre, Room, Director, และ Actors
    $genres = $conn->query("SELECT * FROM Genre ORDER BY Genre")->fetchAll(PDO::FETCH_ASSOC);
    $rooms = $conn->query("SELECT * FROM Room ORDER BY Roomname")->fetchAll(PDO::FETCH_ASSOC);
    $directors = $conn->query("SELECT * FROM Director ORDER BY NameDirector")->fetchAll(PDO::FETCH_ASSOC);
    $actors = $conn->query("SELECT * FROM Actors ORDER BY NameActor")->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Movie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 800px;
            transform: scale(1);
        }

        .btn-primary,
        .btn-cancel {
            background: linear-gradient(135deg, #382628, #73373E);
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            border: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover,
        .btn-cancel:hover {
            background-color: #23A7C8;
        }

        .actor-list {
            display: none;
        }

        .actor-list.active {
            display: block;
        }

        .actor-list {
            display: none;
            column-count: 4;
        }

        .form-group {
            margin-bottom: 20px;
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
                <a class="nav-link" href="room_status.php">Room statust</a>
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
        <h2>Add New Movie</h2>
        <form action="insert_movie.php" method="POST">
            <div class="form-group">
                <label for="name">Movie Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="genre">Genre:</label>
                <select class="form-control" id="genre" name="genre">
                    <option value="">Select Genre</option>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?php echo $genre['GenreID']; ?>"><?php echo $genre['Genre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="director">Director:</label>
                <select class="form-control" id="director" name="director">
                    <option value="">Select Director</option>
                    <?php foreach ($directors as $director): ?>
                        <option value="<?php echo $director['DirectorID']; ?>"><?php echo $director['NameDirector']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Actor:</label><br>
                <input type="text" id="actorInput" onkeyup="filterActors()" placeholder="Search for actors..">
                <div class="actor-list" id="actorList">
                    <?php foreach ($actors as $actor): ?>
                        <?php if (!empty($actor['NameActor'])): ?>
                            <div>
                                <input type="checkbox" name="actor[]" value="<?php echo $actor['ActorsID']; ?>">
                                <label><?php echo $actor['NameActor']; ?></label>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <script>
            function filterActors() {
                var input, filter, div, labels, txtValue, i;
                input = document.getElementById('actorInput');
                filter = input.value.toUpperCase();
                div = document.getElementById('actorList');
                labels = div.getElementsByTagName('label');
                div.style.display = (filter !== '') ? 'block' : 'none'; // Show/hide the actor list div
                for (i = 0; i < labels.length; i++) {
                    txtValue = labels[i].textContent || labels[i].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        labels[i].style.display = "";
                        labels[i].previousElementSibling.style.display = ""; // Show checkbox
                    } else {
                        labels[i].style.display = "none";
                        labels[i].previousElementSibling.style.display = "none"; // Hide checkbox
                    }
                }
            }
            </script>
            <script>
                function toggleActorList() {
                    var actorList = document.querySelector('.actor-list');
                    actorList.classList.toggle('active');
                }
            </script>
                
            <div class="form-group">
                <label for="duration">Duration:</label>
                <input type="text" class="form-control" id="duration" name="duration" required>
            </div>

            <div class="form-group">
                <label for="room">Room:</label>
                <select class="form-control" id="room" name="room">
                    <option value="">Select Room</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?php echo $room['RoomID']; ?>"><?php echo $room['Roomname'] . " (" . $room['TypeScreen'] . ")"; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="text" class="form-control" id="price" name="price" required>
            </div>

            <div class="form-group">
                <label for="release_date">Release Date:</label>
                <input type="date" class="form-control" id="release_date" name="release_date" required>
            </div>

            <div class="form-group">
                <label for="leaving_date">Leaving Date:</label>
                <input type="date" class="form-control" id="leaving_date" name="leaving_date">
            </div>

            <div class="form-group">
                <label for="poster">Poster URL:</label>
                <input type="text" class="form-control" id="poster" name="poster">
            </div>

            <div class="form-group">
                <label for="video_link">Video Link:</label>
                <input type="text" class="form-control" id="video_link" name="video_link">
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="ShowMovie.php" class="btn btn-cancel">Cancel</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

</body>
</html>