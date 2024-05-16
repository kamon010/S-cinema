<?php
session_start();

// ติดต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET CHARACTER SET utf8");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginceo'])) {
        if (
            isset($_POST['username'])
            && isset($_POST['password'])
        ) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $sqlCheckUsername = "SELECT * FROM CEO WHERE Username = :username";
            $stmtCheckUsername = $conn->prepare($sqlCheckUsername);
            $stmtCheckUsername->bindParam(':username', $username);
            $stmtCheckUsername->execute();

            if ($stmtCheckUsername->rowCount() > 0) {
                $user = $stmtCheckUsername->fetch(PDO::FETCH_ASSOC);

                if ($password == $user['Password']) {
                    $_SESSION['user_id'] = $user['CeoID'];
                    $_SESSION['user_name'] = $user['Username'];
                    header('Location: DashTotall.php');
                    exit();
                } else {
                    $_SESSION['error'] = "รหัสผ่านไม่ถูกต้อง";
                    header('Location: loginceo.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = "ไม่พบผู้ใช้งานในระบบ";
                header('Location: loginceo.php');
                exit();
            }
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="styleLog.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoginCeo</title>
    <!-- เพิ่มส่วนของ CSS ได้ตามที่คุณต้องการ -->
</head>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<body>
    <div class="container">
        <div class="title">LoginCeo</div>
        <div class="content">
            <form method="POST" action="#">
                <div class="user-details">
                    <div class="input-box">
                        <span for="username" class="details">Username</span>
                        <input type="text" class="form-control" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="input-box">
                        <span for="password" class="details">Password</span>
                        <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" name="loginceo" class="btn btn-primary" value="Login"></input>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
