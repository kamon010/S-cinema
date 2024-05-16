<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ได้เข้าสู่ระบบให้ redirect ไปที่หน้า login
    header("Location: loginContrl.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET CHARACTER SET utf8");

 
    if(isset($_GET['delete'])) {

        $actor_id = $_GET['delete'];

        $sql = "DELETE FROM Actors WHERE ActorsID = :actor_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':actor_id', $actor_id);
        $stmt->execute();

        header("Location: all_actors.php");
        exit();
    }
    $sql = "SELECT * FROM Actors";
    $stmt = $conn->query($sql);
    $actors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- เปลี่ยน Slim เป็นเต็ม -->
    
    <title>Actors Information</title>
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
        th, td {
            color: #333333;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #382628;
            color: white;
        }
        .edit-btn {
            background: linear-gradient(135deg, #382628, #73373E);
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .edit-btn:hover {
            background-color: #333333;
        }
        .actor img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .actor {
            width: 100px;
            height: 100px;
            overflow: hidden;
            margin: 0 auto;
        }
        .actor-name {
            text-align: center;
            margin-top: 10px;
        }
        .btn-add {
            background: linear-gradient(135deg, #382628, #73373E);
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            transition: all 0.3s ease;
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
        .dropdown-menu {
            left: auto;
            right: 0;
        }
        .delete-btn {
        background: linear-gradient(135deg, #382628, #73373E);
        padding: 10px 20px;
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
        <h2>Actors Information</h2>    
        <a href="add_actor.php" class="btn btn-secondary">Add New Actor</a><br><br>
        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            <?php foreach ($actors as $actor): ?>
                <tr>
                    <td class="actor"><img src="<?php echo $actor['ImageURL']; ?>" alt="Actor Image"></td>
                    <td class="actor-name"><?php echo $actor['NameActor']; ?></td>
                    <td class="btn-container"><a href="edit_actor.php?id=<?php echo $actor['ActorsID']; ?>" class="edit-btn">Edit</a></td>
                    <td class="btn-container">
                    <a href="?delete=<?php echo $actor['ActorsID']; ?>" class="delete-btn">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    

    
    <script>
        // ฟังก์ชันที่จะทำงานเมื่อเกิดการโหลดหน้าเว็บ
        window.addEventListener('DOMContentLoaded', (event) => {
            document.querySelector(".btn-add").style.display = "inline-block"; // แสดงปุ่มเมื่อโหลดหน้าเว็บเสร็จสมบูรณ์
        });

        // ฟังก์ชันที่จะทำงานเมื่อเกิดการเลื่อนหน้าจอ
        window.onscroll = function() {scrollFunction()};

        function scrollFunction() {
            var btnAdd = document.querySelector(".btn-add");
            // ถ้าเลื่อนหน้าจอลงมาต่ำกว่า 20px และปุ่มไม่แสดงอยู่
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                if (btnAdd.style.display !== "none") {
                    btnAdd.style.display = "none"; // ซ่อนปุ่ม
                }
            } else { // ถ้าเลื่อนหน้าจอขึ้นมาบนสุดและปุ่มไม่แสดงอยู่
                if (btnAdd.style.display === "none") {
                    btnAdd.style.display = "inline-block"; // แสดงปุ่ม
                }
            }
        }

    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
