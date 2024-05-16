<?php
// ติดต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// คำสั่ง SQL เพื่อดึงข้อมูล QR จากตาราง Payment
$sql = "SELECT QR FROM Payment";
$result = $conn->query($sql);

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($result->num_rows > 0) {
    echo '<div class="qr-gallery">';

    // วนลูปแสดง QR
    while ($row = $result->fetch_assoc()) {
        $qrData = $row['QR'];

        // แสดง QR ใน HTML
        echo '<div class="qr-container">';
        // ปรับขนาดรูปภาพที่แสดงผล
        echo '<img src="data:image/jpeg;base64,' . base64_encode($qrData) . '" alt="QR Code" style="max-width: 20%; height: auto;" />';
        echo '</div>';
    }

    echo '</div>';
} else {
    echo "ไม่พบข้อมูล QR Code";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
