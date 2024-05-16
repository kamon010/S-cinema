<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';
 
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->exec("SET CHARACTER SET utf8");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_credentials'])) {
    if (
        isset($_POST['username'])
        && isset($_POST['password'])
        && isset($_POST['new_username'])
        && isset($_POST['new_password'])
        && isset($_POST['user_type']) // เพิ่มการตรวจสอบฟิลด์ 'user_type'
    ) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $newUsername = $_POST['new_username'];
        $newPassword = $_POST['new_password'];
        $userType = $_POST['user_type']; // รับค่าฟิลด์ 'user_type' จากฟอร์ม

        // ตรวจสอบ Username และ Password ในฐานข้อมูล
        $sqlCheckUser = "SELECT * FROM Control WHERE NameControl = :username AND Password = :password";
        $stmtCheckUser = $conn->prepare($sqlCheckUser);
        $stmtCheckUser->bindParam(':username', $username);
        $stmtCheckUser->bindParam(':password', $password);
        $stmtCheckUser->execute();

        if ($stmtCheckUser->rowCount() > 0) {
            // อัปเดตชื่อผู้ใช้และรหัสผ่านใหม่ในฐานข้อมูล
            $sqlUpdateCredentials = "UPDATE Control SET NameControl = :new_username, Password = :new_password, Type = :user_type WHERE NameControl = :username";
            $stmtUpdateCredentials = $conn->prepare($sqlUpdateCredentials);
            $stmtUpdateCredentials->bindParam(':new_username', $newUsername);
            $stmtUpdateCredentials->bindParam(':new_password', $newPassword);
            $stmtUpdateCredentials->bindParam(':user_type', $userType); // ผูกค่าฟิลด์ 'user_type'
            $stmtUpdateCredentials->bindParam(':username', $username);
            $stmtUpdateCredentials->execute();

            echo '<script>alert("Credentials updated successfully."); window.location.href = "loginContrl.php";</script>';
            exit;
        } else {
            echo '<script>alert("Invalid username or password.");</script>';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Credentials</title>
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
            width: 400px;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: auto; /* เพิ่มให้ฟอร์มอยู่กึ่งกลาง */
            margin-top: 50px; /* ย้ายฟอร์มไปด้านบนให้ห่างจาก Navbar */
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
        <h2>Change Credentials</h2>
        <form action="#" method="POST">
            <div class="input-box">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''); ?>" required>
            </div>
            <div class="input-box">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-box">
                <label for="new_username">New Username</label>
                <input type="text" id="new_username" name="new_username" required>
            </div>
            <div class="input-box">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <!-- เพิ่มฟิลด์ 'user_type' -->
            <div class="input-box">
                <label for="user_type">User Type</label>
                <select id="user_type" name="user_type" required>
                    <option value="Manager">Manager</option>
                    <option value="Ceo">Ceo</option>
                </select>
            </div>
            <div class="button">
                <input type="submit" name="change_credentials" value="Change Credentials">
            </div>
        </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>