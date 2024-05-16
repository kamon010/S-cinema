<?php
session_start();


$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

// เช็คว่าผู้ใช้เข้าสู่ระบบหรือยัง
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ได้เข้าสู่ระบบให้ redirect ไปที่หน้า login
    header("Location: loginContrl.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ตรวจสอบว่ามีการส่งค่า MovieID มาหรือไม่
    if (isset($_GET['movie_id'])) {
        $movieId = $_GET['movie_id'];
        
        // ดึงข้อมูลหนังจากฐานข้อมูล
        $stmt = $conn->prepare("SELECT * FROM Movie WHERE MovieID = :movieId");
        $stmt->bindParam(':movieId', $movieId);
        $stmt->execute();
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlGenres = "SELECT * FROM Genre";
        $stmtGenres = $conn->query($sqlGenres);
        $genres = $stmtGenres->fetchAll(PDO::FETCH_ASSOC);

        $sqlRoom = "SELECT * FROM Room";
        $stmtRoom = $conn->query($sqlRoom);
        $rooms = $stmtRoom->fetchAll(PDO::FETCH_ASSOC);

        if ($movie) {
            // ถ้าพบข้อมูลหนังที่ต้องการแก้ไข
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
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

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="date"],
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="submit"],
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

        input[type="submit"]:hover,
        .btn-cancel:hover {
            background-color: #23A7C8;
        }

        .btn-secondary {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            background-color: #6c757d;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-container {
            text-align: center;
        }

        .btn-secondary:hover {
            background-color: #F0368D;
        }

        .dropdown-menu {
            left: auto;
            right: 0;
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
    <h2>Edit Movie</h2>
    <form action="update_movie.php" method="post">
        <input type="hidden" name="movie_id" value="<?php echo $movie['MovieID']; ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $movie['NameMovie']; ?>">

        <label for="genre">Genre:</label>
        <select id="genre" name="genre">
            <?php foreach ($genres as $genre): ?>
                <option value="<?php echo $genre['GenreID']; ?>" <?php if ($genre['GenreID'] == $movie['GenreID']) echo 'selected'; ?>><?php echo $genre['Genre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="room">Room:</label>
        <select id="room" name="room">
            <?php foreach ($rooms as $room): ?>
                <option value="<?php echo $room['RoomID']; ?>" <?php if ($room['RoomID'] == $movie['RoomID']) echo 'selected'; ?>><?php echo $room['Roomname'] . " (" . $room['TypeScreen'] . ")"; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="duration">Duration:</label>
        <input type="text" id="duration" name="duration" value="<?php echo $movie['Duration']; ?>">

        <label for="price">Price:</label>
        <input type="text" id="price" name="price" value="<?php echo $movie['price']; ?>">

        <label for="release_date">Release Date:</label>
        <input type="date" id="release_date" name="release_date" value="<?php echo isset($movie['ReleaseDate']) ? $movie['ReleaseDate'] : ''; ?>">

        <label for="leaving_date">Leaving Date:</label>
        <input type="date" id="leaving_date" name="leaving_date" value="<?php echo isset($movie['LeavingDate']) ? $movie['LeavingDate'] : ''; ?>">

        <label for="poster">Poster:</label>
        <input type="text" id="poster" name="poster" value="<?php echo $movie['Poster']; ?>">

        <label for="link">Link VDO:</label>
        <input type="text" id="link" name="link" value="<?php echo $movie['LinkVDO']; ?>">

        <input type="submit" value="Submit"><br>
        <a href="ShowMovie.php" class="btn btn-cancel">Cancel</a>

    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


<?php
        } else {
            echo "Movie not found.";
        }
    } else {
        echo "Movie ID not provided.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
