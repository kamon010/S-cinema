<?php
// เริ่ม session
session_start();

// ลบตัวแปร session ทั้งหมด
session_unset();

// ทำลาย session
session_destroy();

// ให้เปลี่ยนเส้นทางไปยังหน้า login
header("Location: login.php");
exit();
?>
