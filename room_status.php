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

    // Fetch rooms data
    $sql_room = "SELECT * FROM Room ORDER BY Roomname";
    $stmt_room = $conn->query($sql_room);
    $rooms = $stmt_room->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // Handle database errors
    echo '<li class="list-group-item">Error: ' . $e->getMessage() . '</li>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Status</title>
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
            width: 700px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 30px; /* ปรับตามความสูงของ Navbar ของคุณ */
            margin-bottom: 50px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .input-box {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .button {
            text-align: center;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            margin-top: 10px;
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
        .button input[type="submit"] {
            background-color: #382628; /* สีเดียวกับสีพื้นหลังของ dropdown */
            color: #fff; /* สีข้อความ */
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .button input[type="submit"]:hover {
            background-color: #73373E; /* สีเมื่อชี้เม้าส์ hover */
        }
        .save-button {
        float: right;
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
                    echo '<a class="dropdown-item" href="edit_pass_control.php">Change Credentials</a>';
                }
                ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Room Status</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Type Screen</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($rooms as $room): ?>
        <tr>
            <td><?php echo $room['Roomname']; ?></td>
            <td><?php echo $room['TypeScreen']; ?></td>
            <td>
                <form class="d-flex justify-content-between align-items-center" action="update_room_status.php" method="POST">
                    <input type="hidden" name="room_id" value="<?php echo $room['RoomID']; ?>">
                    <select name="room_status" onchange="this.form.submit()">
                        <option value="Ready to use" <?php echo ($room['Roomstatus'] == 'Ready to use') ? 'selected' : ''; ?>>Ready to use</option>
                        <option value="Not available" <?php echo ($room['Roomstatus'] == 'Not available') ? 'selected' : ''; ?>>Not available</option>
                    </select>
                    <button type="submit" class="btn btn-secondary ms-2">Save</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

        </table>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

</html>
