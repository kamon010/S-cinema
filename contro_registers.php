<?php
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET CHARACTER SET utf8");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        if (
            isset($_POST['new_username'])
            && isset($_POST['new_password'])
            && isset($_POST['user_type'])
        ) {
            $newUsername = $_POST['new_username'];
            $newPassword = $_POST['new_password'];
            $userType = $_POST['user_type'];

            // Check if the username already exists
            $stmtCheckExistingUser = $conn->prepare("SELECT * FROM Control WHERE NameControl = :newUsername");
            $stmtCheckExistingUser->bindParam(':newUsername', $newUsername);
            $stmtCheckExistingUser->execute();

            if ($stmtCheckExistingUser->rowCount() > 0) {
                echo "Username already exists";
            } else {
                // Insert new user into the database
                $stmtInsertUser = $conn->prepare("INSERT INTO Control (NameControl, Password, Type) VALUES (:newUsername, :newPassword, :userType)");
                $stmtInsertUser->bindParam(':newUsername', $newUsername);
                $stmtInsertUser->bindParam(':newPassword', $newPassword);
                $stmtInsertUser->bindParam(':userType', $userType);


                if ($stmtInsertUser->execute()) {
                    echo "<script>alert('Registration successful'); window.location.href = 'loginContrl.php';</script>";
                } else {
                    echo "Registration failed";
                }
            
                if ($stmtInsertUser->execute()) {
                    echo "Registration successful";
                } else {
                    echo "Registration failed";
                }
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
    <title>Registration Form</title>
    <style>
    h2 {
        font-size: 25px;
        font-weight: bold;
        position: relative;
        }
    body {
        font-family: Arial, sans-serif;
        background-color: #382628;
        backdrop-filter: blur(10px);
        background-size: cover;
        background-position: center;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        max-width: 400px;
            /* กำหนดความกว้างสูงสุดของ container */
            width: 100%;
            /* กำหนดความกว้างของ container เป็นเปอร์เซ็นต์ของพื้นที่ทั้งหมด */
        background-color: #fff;
        padding: 25px 30px;
        border-radius: 5px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        transform: scale(1.0);
        }

    h2 {
        text-align: center;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="password"],
    select {
        width: calc(100% - 12px);
        padding: 5px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
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
        flex-grow: 1;
        margin: 0 5px;
        align-items: center;
        line-height: 1; /* เพิ่ม line-height เพื่อจัดให้ข้อความอยู่ตรงกลางในปุ่ม */
    }

    input[type="submit"]:hover {
        background-color: #23A7C8;
    }
    
    .input-box {
        width: 200%;
        margin-bottom: 20px;
    }
    
    .button {
        display: flex;
        justify-content: space-between;
    }

    .btn-secondary {
        background: linear-gradient(135deg, #382628, #73373E);
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        color: #fff;
        border: none;
        border-radius: 5px;
        transition: all 0.3s ease;
        flex-grow: 1;
        margin: 0 5px;
        align-items: center;
        line-height: 1; /* เพิ่ม line-height เพื่อจัดให้ข้อความอยู่ตรงกลางในปุ่ม */
        display: flex; /* ให้ปุ่มเป็น flex container */
        justify-content: center; /* จัดให้ข้อความอยู่ตรงกลางในปุ่ม */
        align-items: center; /* จัดให้ข้อความอยู่ตรงกลางในปุ่ม */
    }

    select {
        width: 100%; /* ขยายกล่อง select ให้เต็มขนาดของพื้นที่ที่ให้ในการแสดง */
        padding: 8px; /* ปรับขนาดของพื้นที่ภายในกล่อง select */
        padding: 8px; /* ปรับขนาดของพื้นที่ภายในกล่อง select */
        border: 1px solid #ccc; /* เพิ่มเส้นขอบสีเทา */
        border-radius: 5px; /* กำหนดมุมของขอบ */
        background-color: white; /* กำหนดพื้นหลังให้เป็นสีขาว */
        cursor: pointer; /* เปลี่ยนรูปแบบ cursor เป็น pointer เมื่อผู้ใช้ชี้ที่ select */
    }

    select:hover {
        border-color: #23A7C8; /* เปลี่ยนสีขอบเป็นสีน้ำเงินเมื่อ hover */
    }

    select:focus {
        outline: none; /* ลบเส้นขอบให้กล่อง select เมื่อมีการคลิกเพื่อเลือก */
        border-color: #23A7C8; /* เปลี่ยนสีขอบเป็นสีน้ำเงินเมื่อโฟกัส */
    }
</style>

</head>
<body>
<div class="container">
    <form action="#" method="POST">
        <h2>Registration Form</h2>
        <label for="new_username">Username:</label>
        <input type="text" id="new_username" name="new_username" required>
        <label for="new_password">Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <label for="user_type">User Type:</label>
        <select id="user_type" name="user_type" required>
            <option value="Ceo">Ceo</option>
            <option value="Manager">Manager</option>
        </select><br><br>
        <div class="button">
            <a href="loginContrl.php" class="btn-secondary" style="float: left;" >Cancel</a>
            <input type="submit" name="register" value="Register" style="float: right;">
        </div>
    </form>
</div>
</body>
</html>
