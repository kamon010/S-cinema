<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

// เช็คว่าผู้ใช้เข้าสู่ระบบหรือยัง
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ได้เข้าสู่ระบบให้ redirect ไปที่หน้า login
    header("Location: loginContrl.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่ามีข้อมูลที่ส่งมาหรือไม่
    if (isset($_POST['movie_id'], $_POST['name'], $_POST['genre'], $_POST['room'], $_POST['duration'], $_POST['price'], $_POST['release_date'], $_POST['leaving_date'], $_POST['poster'], $_POST['link'])) {
        // เชื่อมต่อฐานข้อมูล
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // รับข้อมูลจากฟอร์ม
            $movieId = $_POST['movie_id'];
            $name = $_POST['name'];
            $genreId = $_POST['genre'];
            $roomId = $_POST['room'];
            $duration = $_POST['duration'];
            $price = $_POST['price'];
            $releaseDate = $_POST['release_date'];
            $leavingDate = $_POST['leaving_date'];
            $poster = $_POST['poster'];
            $link = $_POST['link'];

            // คำสั่ง SQL สำหรับอัปเดตข้อมูลหนัง
            $sql = "UPDATE Movie SET NameMovie = :name, GenreID = :genreId, RoomID = :roomId, Duration = :duration, price = :price, ReleaseDate = :releaseDate, LeavingDate = :leavingDate, Poster = :poster, LinkVDO = :link WHERE MovieID = :movieId";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':genreId', $genreId);
            $stmt->bindParam(':roomId', $roomId);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':releaseDate', $releaseDate);
            $stmt->bindParam(':leavingDate', $leavingDate);
            $stmt->bindParam(':poster', $poster);
            $stmt->bindParam(':link', $link);
            $stmt->bindParam(':movieId', $movieId);

            // ทำการ execute คำสั่ง SQL
            $stmt->execute();

            // แจ้งเตือนการอัปเดตข้อมูลสำเร็จและ redirect กลับไปยังหน้า ShowMovie.php
            echo '<script>alert("Movie updated successfully."); window.location.href = "ShowMovie.php";</script>';
            exit();
        } catch (PDOException $e) {
            // กรณีเกิดข้อผิดพลาดในการเชื่อมต่อกับฐานข้อมูล
            echo "Error: " . $e->getMessage();
            exit();
        }
    } else {
        // กรณีไม่มีข้อมูลที่ส่งมา
        echo "Data is missing.";
        exit();
    }
} else {
    // กรณีไม่ใช่การเรียกผ่านวิธี POST
    echo "Invalid request.";
    exit();
}
?>
