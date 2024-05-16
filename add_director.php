<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

// เช็คการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ได้เข้าสู่ระบบให้ redirect ไปที่หน้า login
    header("Location: loginContrl.php");
    exit();
}

// เมื่อมีการกดปุ่ม Submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่าชื่อผู้กำกับและ URL ของรูปถูกกรอกหรือไม่
    if (empty($_POST["name"]) || empty($_POST["image_url"])) {
        $error = "กรุณากรอกชื่อผู้กำกับและ URL ของรูป";
    } else {
        try {
            // ทำการเชื่อมต่อฐานข้อมูล
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("SET CHARACTER SET utf8");

            // เตรียมคำสั่ง SQL สำหรับการเพิ่มข้อมูลผู้กำกับใหม่
            $sql = "INSERT INTO Director (NameDirector, ImageURL) VALUES (:name, :image_url)";
            $stmt = $conn->prepare($sql);

            // ผู้กำกับและ URL ของรูปที่รับมาจากฟอร์ม
            $name = $_POST["name"];
            $image_url = $_POST["image_url"];

            // Bind parameters
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':image_url', $image_url);

            // Execute the query
            $stmt->execute();

            // Redirect to all_director.php after adding director successfully
            header("Location: all_director.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add director</title>
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
            padding: 30px;
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

        input[type="submit"] {
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

        input[type="submit"]:hover {
            background-color: #23A7C8;
        }

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
        .btn-primary {
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

<body>
    <div class="container">
        <h2>Add Director</h2>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="image_url">Image URL:</label>
                <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo isset($_POST['image_url']) ? $_POST['image_url'] : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <br><a href="all_director.php" class="btn btn-cancel">Cancel</a>
        </form>
    </div>
</body>
</html>
