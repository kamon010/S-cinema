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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่ามีข้อมูลที่ส่งมาและไม่ว่างเปล่าหรือไม่
    if (isset($_POST['actors_id']) && !empty($_POST['actors_id']) && isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['image_url']) && !empty($_POST['image_url'])) {
        $actor_id = $_POST['actors_id'];
        $name = $_POST['name'];
        $image_url = $_POST['image_url'];

        try {
            // เชื่อมต่อกับฐานข้อมูล
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("SET CHARACTER SET utf8");

            // เตรียมคำสั่ง SQL สำหรับการอัปเดตข้อมูลนักแสดง
            $sql = "UPDATE Actors SET NameActor = :name, ImageURL = :image_url WHERE ActorsID = :actors_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':image_url', $image_url);
            $stmt->bindParam(':actors_id', $actor_id);
            
            // ทำการ execute คำสั่ง SQL
            $stmt->execute();

            // หลังจากอัปเดตข้อมูลเสร็จสิ้น ให้ redirect ไปที่หน้า all_actors.php
            header("Location: all_actors.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        // หากไม่มีข้อมูลที่ส่งมาหรือข้อมูลไม่ครบถ้วน ให้ redirect กลับไปที่หน้า edit_actor.php
        header("Location: edit_actor.php");
        exit();
    }
} else {
    // หากไม่ใช่การร้องขอแบบ POST ให้ redirect กลับไปที่หน้า edit_actor.php
    header("Location: edit_actor.php");
    exit();
}
?>
