<?php
// fetch_point.php

// เช็คว่าเป็นการเรียกไฟล์โดยตรงหรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(400);
    exit('Bad Request');
}

// ดึงข้อมูลที่ส่งมาจาก JavaScript
$request_data = json_decode(file_get_contents('php://input'), true);

// ติดต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if ($conn->connect_error) {
    http_response_code(500);
    exit('Database Connection Failed');
}

try {
    // ดึงข้อมูล Point จากฐานข้อมูล
    $user_id = $request_data['user_id'];
    $sql_point = "SELECT Point FROM Users WHERE UsersID = ?";
    $stmt = $conn->prepare($sql_point);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result_point = $stmt->get_result();

    // ตรวจสอบว่ามีข้อมูล Point หรือไม่
    if ($result_point->num_rows > 0) {
        $user_point = $result_point->fetch_assoc()['Point'];
        // ส่งข้อมูล Point กลับไปให้ JavaScript
        echo json_encode(['point' => $user_point]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
exit();
