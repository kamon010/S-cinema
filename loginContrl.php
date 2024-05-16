<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET CHARACTER SET utf8");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        if (
            isset($_POST['username'])
            && isset($_POST['password'])
        ) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $sqlCheckUser = "SELECT * FROM Control WHERE NameControl = :username";
            $stmtCheckUser = $conn->prepare($sqlCheckUser);
            $stmtCheckUser->bindParam(':username', $username);
            $stmtCheckUser->execute();

            if ($stmtCheckUser->rowCount() > 0) {
                $user = $stmtCheckUser->fetch(PDO::FETCH_ASSOC);

                if ($password == $user['Password']) {
                    if ($user['Type'] == 'Ceo') {
                        $_SESSION['user_id'] = $user['ControlID'];
                        $_SESSION['user_name'] = $user['NameControl'];
                        header('Location: DashH.php');
                        exit();
                    } elseif ($user['Type'] == 'Manager') {
                        $_SESSION['user_id'] = $user['ControlID'];
                        $_SESSION['user_name'] = $user['NameControl'];
                        header('Location: ShowMovie.php');
                        exit();
                    } else {
                        echo "Invalid user type";
                    }
                } else {
                    echo "Invalid password";
                }
            } else {
                echo "User not found";
            }
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleLog.css">
    <title>Login Control</title>
    <style>
        body {
            background-image: url('https://resource.nationtv.tv/uploads/images/md/2021/10/G5k44IRqI2iCmZb4XlVR.jpg');
            backdrop-filter: blur(10px);
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Login Control</div>
        <div class="content">
            <form action="#" method="POST">
                <div class="user-details">
                    <div class="input-box">
                        <span for="username" class="details">Username</span>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="input-box">
                        <span for="password" class="details">Password</span>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" name="login" value="Login">
                </div>
                <div class="gender-details">Don't have an account? <br><a href="contro_registers.php" id="registercon">Register now</a></div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById("registercon").addEventListener("click", function(e) {
            e.preventDefault();
            window.location.href = "contro_registers.php";
        });
    </script>
</body>
</html>
