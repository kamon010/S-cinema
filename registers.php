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
    $conn->exec("SET CHARACTER SET utf8");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        if (
            isset($_POST['Name'])
            && isset($_POST['gender'])
            && isset($_POST['password'])
            && isset($_POST['confirmpassword'])
            && isset($_POST['ID_Card_Number'])
            && isset($_POST['email'])
            && isset($_POST['Phone_Number'])
            && isset($_POST['Age'])
        ) {
            $name = $_POST['Name'];
            $gender = $_POST['gender'];
            $password = $_POST['password'];
            $confirmpassword = $_POST['confirmpassword'];
            $idCardNumber = $_POST['ID_Card_Number'];
            $email = $_POST['email'];
            $phoneNumber = $_POST['Phone_Number'];
            $age = $_POST['Age'];

            // ตรวจสอบความถูกต้องของ ID Card Number
            if (!preg_match('/^\d{13}$/', $idCardNumber)) {
                $_SESSION['error'] = "กรุณากรอก ID Card Number เป็นตัวเลข 13 ตัว";
                header('Location: registers.php');
                exit();
            }

            $sqlCheckIDCard = "SELECT * FROM users WHERE ID_Card_Number = :idCardNumber";
            $stmtCheckIDCard = $conn->prepare($sqlCheckIDCard);
            $stmtCheckIDCard->bindParam(':idCardNumber', $idCardNumber);
            $stmtCheckIDCard->execute();

            if ($stmtCheckIDCard->rowCount() > 0) {
                $_SESSION['error'] = "ID_Card_Number นี้มีคนใช้แล้ว";
                header('Location: registers.php');
                exit();
            }

            if ($password !== $confirmpassword) {
                $_SESSION['error'] = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน";
                header('Location: registers.php');
                exit();
            }

            if (strlen($phoneNumber) !== 10) {
                $_SESSION['error'] = "กรุณากรอก Phone_Number ให้ครบ 10 ตัว";
                header('Location: registers.php');
                exit();
            }

            if (!is_numeric($age) || strlen($age) < 1 || strlen($age) > 3) {
                $_SESSION['error'] = "กรุณากรอก Age เป็นตัวเลข 1-3 ตัว";
                header('Location: registers.php');
                exit();
            }

            $sqlCheckEmail = "SELECT * FROM Users WHERE Email = :email";
            $stmtCheckEmail = $conn->prepare($sqlCheckEmail);
            $stmtCheckEmail->bindParam(':email', $email);
            $stmtCheckEmail->execute();

            if ($stmtCheckEmail->rowCount() > 0) {
                $_SESSION['error'] = "Email นี้มีคนใช้แล้ว";
                header('Location: registers.php');
                exit();
            }

            $sqlCheckPhone = "SELECT * FROM Users WHERE Phone_Number = :phoneNumber";
            $stmtCheckPhone = $conn->prepare($sqlCheckPhone);
            $stmtCheckPhone->bindParam(':phoneNumber', $phoneNumber);
            $stmtCheckPhone->execute();

            if ($stmtCheckPhone->rowCount() > 0) {
                $_SESSION['error'] = "Phone_Number นี้มีคนใช้แล้ว";
                header('Location: registers.php');
                exit();
            }

            $sql = "INSERT INTO Users (Name, Gender, ID_Card_Number, Email, Phone_Number, Age, Password) 
            VALUES (:name, :gender, :idCardNumber, :email, :phoneNumber, :age, :password)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':idCardNumber', $idCardNumber);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phoneNumber', $phoneNumber);
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
            $stmt->execute();

            echo "<script>alert('User registered successfully!');</script>";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>
<!-- ตรวจสอบ session error และแสดง alert -->
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var confirmPasswordField = document.getElementById("confirmpassword");
            var showPassword = document.getElementById("showPassword");

            if (showPassword.checked) {
                passwordField.type = "text";
                confirmPasswordField.type = "text";
            } else {
                passwordField.type = "password";
                confirmPasswordField.type = "password";
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#loginNow").click(function() {
                window.location.href = "login.php";
            });
        });
    </script>
    <script>
        var errorMessage = "<?php echo isset($_SESSION['error']) ? $_SESSION['error'] : ''; ?>";
        if (errorMessage) {
            alert(errorMessage);
        }
    </script>
</head>

<head>
    <meta charset="UTF-8">
    <title> Responsive Registration Form | CodingLab </title>
    <link rel="stylesheet" href="styleRe.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            background-image: url('https://resource.nationtv.tv/uploads/images/md/2021/10/G5k44IRqI2iCmZb4XlVR.jpg');
            /* ลิงก์ไปยังภาพพื้นหลังที่คุณต้องการใช้ */
            backdrop-filter: blur(10px);
            /* ทำให้พื้นหลังเบลอ */
            background-size: cover;
            background-position: center;
            width: 100%;
            /* กำหนดให้ body ครอบคลุมหน้าจอทั้งหมด */
            height: 105vh;
            /* กำหนดความสูงให้เต็มหน้าจอ */
            margin: 0;
            /* ลบ margin เพื่อป้องกันการเกิดพื้นที่เพิ่มขึ้นเพื่อความสวยงาม */
            padding: 0;
            /* ลบ padding เพื่อป้องกันการเกิดพื้นที่เพิ่มขึ้นเพื่อความสวยงาม */
        }


        .container {
            max-width: 500px;
            /* กำหนดความกว้างสูงสุดของ container */
            width: 80%;
            /* กำหนดความกว้างของ container เป็นเปอร์เซ็นต์ของพื้นที่ทั้งหมด */
            margin: 0 auto;
            /* จัดให้ container อยู่กึ่งกลางตามแนวนอน */
            height: 100%;
            /* กำหนดความสูงของ container เต็มหน้าจอ */
            justify-content: center;
            /* จัดเรียง content ให้อยู่กึ่งกลางตามแนวแกน x */
            align-items: center;
            /* จัดเรียง content ให้อยู่กึ่งกลางตามแนวแกน y */
            transform: scale(0.7);
            /* ลดขนาด container ลงครึ่งนึง */
            transform-origin: center center;
            /* ตำแหน่งศูนย์กลางของการ scale */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="title">Registration</div>
        <div class="content">
            <form method="POST" action="#">
                <div class="user-details">
                    <div class="input-box">
                        <span for="Name" class="details">Full Name</span>
                        <input type="text" class="form-control" name="Name" placeholder="Enter your name" required>
                    </div>
                    <div class="input-box">
                        <span for="email" class="details">Email</span>
                        <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="input-box">
                        <span for="Phone_Number" class="details">Phone Number</span>
                        <input type="tel" name="Phone_Number" id="Phone_Number" placeholder="Enter your number" required minlength="10" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="input-box">
                        <span for="ID_Card_Number" class="details">ID Card Number</span>
                        <input type="text" name="ID_Card_Number" id="ID_Card_Number" placeholder="Enter your ID Card Number" required minlength="13" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="input-box">
                        <span for="Age" class="details">Age</span>
                        <input type="text" name="Age" placeholder="Enter your Age" required>
                    </div>
                    <div class="input-box">
                        <span for="confirmpassword" class="details">Confirm Password</span>
                        <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" placeholder="Confirm your password" required>
                    </div>
                    <div class="input-box">
                        <span for="password" class="details">Password</span>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
                    </div>
                    <div class="gender-details">
                        <input type="checkbox" class="form-check-input" id="showPassword" onchange="togglePassword()">
                        <label class="form-check-label" for="showPassword">Show password</label>
                    </div>
                </div>
                <div class="gender-details">
                    <input class="form-check-input" type="radio" name="gender" id="dot-1" value="Male" required>
                    <input class="form-check-input" type="radio" name="gender" id="dot-2" value="Female" required>
                    <input class="form-check-input" type="radio" name="gender" id="dot-3" value="LGBTQ+" required>
                    <span class="details">Gender</span>
                    <div class="category">
                        <label for="dot-1" class="form-check-label">
                            <span class="dot one"></span>
                            <span class="gender">Male</span>
                        </label>
                        <label for="dot-2" class="form-check-label">
                            <span class="dot two"></span>
                            <span class="gender">Female</span>
                        </label>
                        <label for="dot-3" class="form-check-label">
                            <span class="dot three"></span>
                            <span class="gender">LGBTQ+</span>
                        </label>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" name="register" class="btn btn-primary" value="Register"></input>
                    <br><br>
                    <input type="reset" class="btn btn-secondary reset-button" value="Reset"></input>
                </div><br><br>
                <div class="gender-details">Have an account? <br><label for="flip" id="loginNow">Login now</label></div>
            </form>
        </div>
    </div>
</body>

</html>