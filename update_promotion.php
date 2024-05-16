<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);

// ตรวจสอบว่ามีข้อผิดพลาดในการเชื่อมต่อหรือไม่
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งค่า ID ของโปรโมชั่นที่ต้องการแก้ไขหรือไม่
if (isset($_GET['id'])) {
    $promotion_id = $_GET['id'];

    // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลโปรโมชั่นที่ต้องการแก้ไข
    $sql = "SELECT * FROM promotion WHERE PromotionID = $promotion_id";

    // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
    $result = $conn->query($sql);

    // ตรวจสอบว่ามีข้อมูลที่ได้รับหรือไม่
    if ($result->num_rows > 0) {
        // ดึงข้อมูลโปรโมชั่นที่ต้องการแก้ไข
        $row = $result->fetch_assoc();
    } else {
        echo "<p class='text-danger'>ไม่พบข้อมูลโปรโมชั่น</p>";
    }
}

// เตรียมคำสั่ง SQL เพื่ออัปเดตข้อมูลโปรโมชั่น
$sql_update = "UPDATE promotion SET PromotionName = ?, StartDate = ?, EndDate = ?, DiscountAmount = ? WHERE PromotionID = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param('sssii', $_POST['promotion_name'], $_POST['start_date'], $_POST['end_date'], $_POST['discount_amount'], $_POST['promotion_id']);

// execute statement
if($stmt->execute()) {
    echo "<script>alert('อัปเดตโปรโมชั่นเรียบร้อยแล้ว');</script>";
    echo "<script>window.location.href='promotion_more.php';</script>";
} else {
    echo "เกิดข้อผิดพลาดในการอัปเดต: " . $stmt->error;
}

// close statement
$stmt->close();

$conn->close();
?>
