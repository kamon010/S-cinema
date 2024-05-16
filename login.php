<?php
session_start();

// ติดต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $sqlCheckEmail = "SELECT * FROM Users WHERE Email = :email";
            $stmtCheckEmail = $conn->prepare($sqlCheckEmail);
            $stmtCheckEmail->bindParam(':email', $email);
            $stmtCheckEmail->execute();

            if ($stmtCheckEmail->rowCount() > 0) {
                $user = $stmtCheckEmail->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['Password'])) {
                    $_SESSION['user_id'] = $user['UsersID'];
                    $_SESSION['user_name'] = $user['Name'];

                    // Redirect to homepage with UserID in the URL
                    header('Location: homepage.php?user_id=' . $user['UsersID'] . '&name=' . urlencode($user['Name']));
                    exit();
                } else {
                    $_SESSION['error'] = "รหัสผ่านไม่ถูกต้อง";
                    header('Location: login.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = "ไม่พบอีเมล์ในระบบ";
                header('Location: login.php');
                exit();
            }
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!-- Check session error and display alert -->
<?php if (isset($_SESSION['error'])) { ?>
    <script>
        alert("<?php echo $_SESSION['error']; ?>");
    </script>
<?php
    unset($_SESSION['error']);
} ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="styleLog.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- เพิ่มส่วนของ CSS ได้ตามที่คุณต้องการ -->
    <style>
        body {
            background-image: url('https://resource.nationtv.tv/uploads/images/md/2021/10/G5k44IRqI2iCmZb4XlVR.jpg');
            /* ลิงก์ไปยังภาพพื้นหลังที่คุณต้องการใช้ */
            backdrop-filter: blur(10px);
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        // ให้ link กับ id registerNow
        $("#registerNow").click(function(e) {
            e.preventDefault(); // ป้องกันการโหลดหน้าใหม่
            window.location.href = "registers.php";
        });
    });
</script>

<body>
    <div class="container">
        <div class="title">Login</div>
        <div class="content">
            <form method="POST" action="#">
                <div class="user-details">
                    <div class="input-box">
                        <span for="email" class="details">Email</span>
                        <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="input-box">
                        <span for="password" class="details">Password</span>
                        <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" name="login" class="btn btn-primary" value="Login"></input>
                </div>
                <br><br>
                <div class="gender-details">Don't have an account? <br><label for="flip" id="registerNow">Register now</label></div>
            </form>
        </div>
    </div>
</body>

</html>