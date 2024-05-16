<?php
// เริ่ม session
session_start();

// ทำลาย session ทั้งหมด
session_destroy();

// ส่งกลับไปยังหน้า loginceo.php
header("Location: loginceo.php");
exit;
?>
