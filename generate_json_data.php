<?php
// การเชื่อมต่อกับฐานข้อมูล MySQL ใน XAMPP
// ติดต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

// สร้างการเชื่อมต่อ MySQL
$mysqli = new mysqli($host, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// สร้าง SQL query เพื่อดึงชื่อตารางทั้งหมดในฐานข้อมูล
$tables_query = "SHOW TABLES";
$tables_result = $mysqli->query($tables_query);

// เข้ารหัส JSON เพื่อเก็บข้อมูลจากทุกตาราง
$data = array();

// วนลูปผ่านผลลัพธ์เพื่อดึงข้อมูลจากทุกตาราง
while ($table_row = $tables_result->fetch_row()) {
    $table_name = $table_row[0];

    // สร้าง SQL query เพื่อดึงข้อมูลจากตารางปัจจุบัน
    $query = "SELECT * FROM $table_name";
    $result = $mysqli->query($query);

    // แปลงข้อมูลเป็นรูปแบบ JSON
    $table_data = array();
    while ($row = $result->fetch_assoc()) {
        $table_data[] = $row;
    }

    // เพิ่มข้อมูลของตารางลงในข้อมูลที่ต้องการเก็บ
    $data[$table_name] = $table_data;
}

// แปลงข้อมูลทั้งหมดเป็นรูปแบบ JSON
$json_data = json_encode($data);

// ตรวจสอบว่าการเขียน JSON สำเร็จหรือไม่
if ($json_data === false) {
    die("Error encoding data to JSON format.");
}

// เขียนข้อมูล JSON ลงในไฟล์
$file = __DIR__ . '/data.json';
$bytes_written = file_put_contents($file, $json_data);

// ตรวจสอบว่าการเขียนลงในไฟล์สำเร็จหรือไม่
if ($bytes_written === false) {
    die("Error writing data to file.");
} else {
    echo "File data.json created successfully.";
}

// ปิดการเชื่อมต่อกับ MySQL
$mysqli->close();
