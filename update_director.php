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

// ตรวจสอบว่ามีการส่งค่าผ่านวิธี POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่ามีค่า ID ของผู้กำกับที่ต้องการแก้ไขหรือไม่
    if (isset($_POST['director_id'])) {
        $director_id = $_POST['director_id'];
        
        // รับค่าจากฟอร์ม
        $name = $_POST['name'];
        $image_url = $_POST['image_url'];

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("SET CHARACTER SET utf8");

            // อัปเดตข้อมูลผู้กำกับ
            $sql = "UPDATE Director SET NameDirector = :name, ImageURL = :image_url WHERE DirectorID = :director_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':image_url', $image_url);
            $stmt->bindParam(':director_id', $director_id);
            $stmt->execute();

            // Redirect กลับไปที่หน้า all_director.php
            header("Location: all_director.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        // ถ้าไม่มี ID ของผู้กำกับที่ต้องการแก้ไข
        echo "Director ID is not provided.";
    }
} else {
    // ถ้าไม่ใช่การส่งข้อมูลผ่านวิธี POST
    echo "Invalid request.";
}
?>
