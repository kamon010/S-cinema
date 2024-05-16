<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = 'root';
$password = 'root';
$dbname = 'Scinema';

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีค่า booking_id ที่รับมาจาก URL หรือไม่
if(isset($_GET['booking_id'])){
    $booking_id = $_GET['booking_id'];

    // คำสั่ง SQL สำหรับการยกเลิกการจอง
    $sql = "UPDATE Bookings SET Status='Cancel' WHERE BookingID='$booking_id'";

    if ($conn->query($sql) === TRUE) {
        echo "success"; // ส่งกลับค่า 'success' เมื่อยกเลิกการจองสำเร็จ
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Booking ID is not provided.";
}

$conn->close();
?>
