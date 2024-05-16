<?php
session_start();

// ติดต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลผู้ใช้ที่เข้าระบบอยู่
$user_id = $_SESSION['user_id'];

/// คำสั่ง SQL เพื่อดึงข้อมูลผู้ใช้
$sql_user = "SELECT * FROM Users WHERE UsersID = $user_id";
$result_user = $conn->query($sql_user);

// ตรวจสอบว่ามีข้อมูลผู้ใช้หรือไม่
if ($result_user->num_rows > 0) {
    // ดึงข้อมูลของผู้ใช้
    $row_user = $result_user->fetch_assoc();
    $name = $row_user['Name'];
    $gender = $row_user['Gender'];
    $idCardNumber = $row_user['ID_Card_Number'];
    $email = $row_user['Email'];
    $phoneNumber = $row_user['Phone_Number'];
    $age = $row_user['Age'];
} else {
    echo "User not found";
    // หรือให้ทำการ redirect ไปยังหน้าอื่น หรือทำอย่างอื่นตามที่คุณต้องการ
}

// ตรวจสอบการส่งค่ารหัสผ่านเก่าและการอัปเดตข้อมูล
if (isset($_POST['update'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['password'];

    // ตรวจสอบว่ารหัสผ่านที่กรอกตรงกับรหัสผ่านในฐานข้อมูลหรือไม่
    $sql_check_password = "SELECT Password FROM Users WHERE UsersID = $user_id";
    $result_check_password = $conn->query($sql_check_password);

    if ($result_check_password->num_rows > 0) {
        $row_password = $result_check_password->fetch_assoc();
        $db_password = $row_password['Password'];

        if (password_verify($old_password, $db_password)) {
            // รหัสผ่านตรงกัน
            // ทำการอัปเดตข้อมูลผู้ใช้
            $name = $_POST['Name'];
            $phoneNumber = $_POST['Phone_Number'];

            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update = "UPDATE Users SET Name = '$name', Phone_Number = '$phoneNumber', Password = '$hashed_new_password' WHERE UsersID = $user_id";

            if ($conn->query($sql_update) === TRUE) {
                // แสดงการแจ้งเตือนด้วย JavaScript หลังจากอัปเดตข้อมูลสำเร็จ
                echo '<script>alert("Record updated successfully");</script>';
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } else {
            echo '<script>alert("Old password incorrect");</script>';
        }
    } else {
        echo '<script>alert("Error fetching password from database");</script>';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Include CSS file -->
    <link rel="stylesheet" href="styleEditProfile.css">
    <style>
        body {
            background-image: url('https://resource.nationtv.tv/uploads/images/md/2021/10/G5k44IRqI2iCmZb4XlVR.jpg');
            backdrop-filter: blur(10px);
            background-size: cover;
            background-position: center;
        }

        .container {
            max-width: 537px;
            /* กำหนดความกว้างสูงสุดของ container */
            width: 100%;
            /* กำหนดความกว้างของ container เป็นเปอร์เซ็นต์ของพื้นที่ทั้งหมด */
            margin: 0 auto;
            /* จัดให้ container อยู่กึ่งกลางตามแนวนอน */
            height: 90%;
            /* กำหนดความสูงของ container เต็มหน้าจอ */
            justify-content: center;
            /* จัดเรียง content ให้อยู่กึ่งกลางตามแนวแกน x */
            align-items: center;
            /* จัดเรียง content ให้อยู่กึ่งกลางตามแนวแกน y */
            transform: scale(0.78);
            /* ลดขนาด container ลงครึ่งนึง */
            transform-origin: center center;
            /* ตำแหน่งศูนย์กลางของการ scale */
        }

        .button {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            /* ปรับระยะห่างด้านบน */
        }

        .button a,
        .button input[type="submit"] {
            /* สไตล์เหมือนกับปุ่ม Update Profile */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 8px 20px;
            outline: none;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            letter-spacing: 1px;
            border-radius: 4px;
            background: linear-gradient(135deg, #382628, #73373e);
            text-decoration: none;
            flex: 1;
            /* ทำให้ปุ่มมีขนาดเท่ากัน */
            margin-right: 10px;
            /* ปรับระยะห่างระหว่างปุ่ม */
        }

        .button a:hover,
        .button input[type="submit"]:hover {
            background: linear-gradient(135deg, #73373e, #382628);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="title">Edit Profile</div>
        <div class="content">
            <form method="POST" action="#">
                <div class="user-details">
                    <div class="input-box">
                        <span for="Name" class="details">Full Name</span>
                        <input type="text" class="form-control" name="Name" value="<?php echo $name; ?>">
                    </div>
                    <div class="input-box">
                        <span for="ID_Card_Number" class="details">ID Card Number</span>
                        <input type="text" name="ID_Card_Number" id="ID_Card_Number" value="<?php echo $idCardNumber; ?>" readonly>
                    </div>
                    <div class="input-box">
                        <span for="email" class="details">Email</span>
                        <input type="email" class="form-control" name="email" value="<?php echo $email; ?>" readonly>
                    </div>
                    <div class="input-box">
                        <span for="Phone_Number" class="details">Phone Number</span>
                        <input type="tel" name="Phone_Number" id="Phone_Number" value="<?php echo $phoneNumber; ?>">
                    </div>
                    <div class="input-box">
                        <span for="Age" class="details">Age</span>
                        <input type="text" name="Age" value="<?php echo $age; ?>" readonly>
                    </div>
                    <div class="input-box">
                        <span for="password" class="details">Old Password</span>
                        <input type="password" class="form-control" name="old_password" id="old_password" placeholder="Enter your old password" required>
                    </div>
                    <div class="input-box">
                        <span for="password" class="details">New Password</span>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter your new password" required>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" name="update" class="btn btn-primary" value="Update Profile">
                    <a href="homepage.php" class="btn btn-primary">Home</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>