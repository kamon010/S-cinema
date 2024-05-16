<h2>Purchase history</h2>
<?php
session_start();
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
// ดึงข้อมูลผู้ใช้จาก session
$user_id = $_SESSION['user_id'];

/// คำสั่ง SQL เพื่อดึงข้อมูลผู้ใช้
$sql_user = "SELECT * FROM Users WHERE UsersID = $user_id";
$result_user = $conn->query($sql_user);

// ตรวจสอบว่ามีข้อมูลผู้ใช้หรือไม่
if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    // URL ของหน้าก่อนหน้า
    $previous_page_url = "homepage.php?user_id=" . $user['UsersID'] . '&name=' . urlencode($user['Name']);

    // เข้าถึงค่า Point จากผู้ใช้
    $points = $user['Point'];
}

// ประกาศตัวแปรสำหรับเก็บอีเมล
$email_link = '';
// เช็คว่าผู้ใช้เข้าสู่ระบบหรือยัง
if (isset($_SESSION['user_id'])) {
    // ดึง user_id จาก session
    $user_id = $_SESSION['user_id'];

    // ดึงอีเมลของผู้ใช้ที่ล็อกอินอยู่
    $sql_email = "SELECT Email FROM Users WHERE UsersID = $user_id";
    $result_email = $conn->query($sql_email);

    if ($result_email->num_rows > 0) {
        $row_email = $result_email->fetch_assoc();
        // กำหนดลิงก์อีเมลล์
        $email_link = '<a href="#" class="email-link">Email: ' . $row_email['Email'] . '</a>';
    }
}

// ตรวจสอบว่ามีค่า user_id ที่รับมาจาก URL หรือไม่
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // คำสั่ง SQL สำหรับดึงข้อมูล Booking โดยกรองด้วย user_id และ Status เป็น Confirm และเรียงจากใหม่ไปเก่า โดย BookingID
    $sql = "SELECT * FROM Bookings WHERE UsersID='$user_id' ORDER BY BookingID DESC";


    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // แสดงข้อมูลในรูปแบบตาราง
        echo "<table border='1'>
        <tr>
        <th>BookingID</th>
        <th>Namemovie</th>
        <th>SelectedSeats</th>
        <th>ShowtimeDate</th>
        <th>Showtimetime</th>
        <th>TotalPrice</th>
        <th>BookingDate</th>
        <th>Quantity</th>
        <th>Status</th>
        <th>Action</th>
        </tr>";
        while ($row = $result->fetch_assoc()) {
            // ตรวจสอบสถานะก่อนที่จะสร้างปุ่ม "Cancel"
            if ($row['Status'] !== 'Cancel' && $row['Status'] === 'Confirm') {
                // ตรวจสอบว่า BookingDate เป็นวันที่ปัจจุบันหรือไม่
                $today = date("Y-m-d");
                if ($row['BookingDate'] === $today) {
                    // แสดงปุ่มยกเลิกการจอง
                    $cancelButton = "<button onclick='cancelBooking(\"" . $row['BookingID'] . "\")'>Cancel</button>";
                } else {
                    // ไม่แสดงปุ่ม "Cancel" สำหรับรายการที่ BookingDate ไม่ใช่วันนี้
                    $cancelButton = "";
                }
            } else {
                // ไม่แสดงปุ่ม "Cancel" สำหรับรายการที่มีสถานะเป็น "Cancel" หรือไม่ใช่ 'Confirm'
                $cancelButton = "";
            }

            // แปลงข้อมูล SelectedSeats เป็น array โดยใช้ explode แยกตัวแบ่งด้วยเครื่องหมาย ','
            $seats = explode(',', $row['SelectedSeats']);
            // แสดงข้อมูลเฉพาะ 5 ที่นั่งแรก หากมีมากกว่า 5 ที่นั่งให้แสดงเป็น ',...'
            $display_seats = implode(',', array_slice($seats, 0, 5));
            if (count($seats) > 5) {
                $display_seats .= ',...';
            }

            echo "<tr>";
            echo "<td>" . $row['BookingID'] . "</td>";
            echo "<td>" . $row['Namemovie'] . "</td>";
            echo "<td>" . $display_seats . "</td>";
            echo "<td>" . $row['ShowtimeDate'] . "</td>";
            echo "<td>" . $row['Showtimetime'] . "</td>";
            echo "<td>" . $row['TotalPrice'] . "</td>";
            echo "<td>" . $row['BookingDate'] . "</td>";
            echo "<td>" . $row['Quantity'] . "</td>";
            echo "<td>" . $row['Status'] . "</td>";
            // แสดงปุ่มยกเลิกการจองหรือไม่ตามเงื่อนไข
            echo "<td>" . $cancelButton . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No bookings found.";
    }
} else {
    echo "User ID is not provided.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<style>
    /* เพิ่มสไตล์ CSS เพื่อสลับสีของแถวในตาราง */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid black;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    /* สลับสีของแถวที่เป็นเลขคู่ */
    tr:nth-child(even) {
        background-color: #2B2B2B;
    }

    /* สลับสีของแถวที่เป็นเลขคี่เป็นสีเขียว */
    tr:nth-child(odd) {
        background-color: #5D5D5D;
        /* เปลี่ยนสีเป็นสีเขียว */
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Booking.css">
    <style>
    </style>
</head>

<body>

    <nav>
        <div class="user-icon" id="userIcon" onclick="toggleMenu()"></div>
        <div class="menu-container" id="menuContainer">
            <div class="menu-item" id="editInfo">Edit personal</div>
            <div class="menu-item" id="userPoint">Point : <?php echo $points ?></div>
            <div class="menu-item" id="logOut">Log out</div>
        </div>
        <a href="headder.php" class="nav-link"><i class="fas fa-home"></i> Homepage</a>
        <a href="homepage.php" class="nav-link"><i class="fas fa-film"></i> Movie</a>
        <a href="promotion.php" class="nav-link"><i class="fas fa-percent"></i> Promotion</a>
    </nav>

    <div class="email-container">
        <?php echo $email_link; ?>
    </div>
    <script>
        // เรียกใช้งาน DOMContentLoaded เพื่อรอให้หน้าเว็บโหลดเสร็จสมบูรณ์ก่อน
        document.addEventListener('DOMContentLoaded', function() {
            // ปิดเมนูเมื่อหน้าเว็บโหลด
            menuContainer.style.display = 'none';

            // เพิ่ม event listener เมื่อคลิกที่ไอคอนผู้ใช้
            userIcon.addEventListener('click', function() {
                // สลับการแสดงผลของเมนู
                menuContainer.style.display = (menuContainer.style.display === 'none' || menuContainer.style.display === '') ? 'block' : 'none';
            });

            // Redirect to logout page on logout button click
            document.getElementById("logOut").addEventListener("click", function() {
                window.location.href = "logout.php";
            });
            document.getElementById("editInfo").addEventListener("click", function() {
                // ตรวจสอบว่าตัวแปร $ruser_id['Users'] มีค่าหรือไม่
                var user_id = "<?php echo $user_id['UsersID']; ?>";
                window.location.href = "edit_profile.php?user_id=" + user_id;
            });
        });
    </script>

    <script>
        function cancelBooking(bookingID) {
            alert('Booking ID: ' + bookingID); // เพิ่ม alert เพื่อแสดงค่า booking_id
            // ส่งคำขอ HTTP ไปยังไฟล์ cancel_booking.php โดยส่งค่า bookingID ไปด้วย
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // ตรวจสอบการตอบกลับจากเซิร์ฟเวอร์
                    if (xhr.responseText === 'success') {
                        // กระทำหรือแสดงข้อความเมื่อยกเลิกการจองสำเร็จ
                        alert('Booking canceled successfully.');
                        // รีโหลดหน้าเว็บหลังจากยกเลิกการจอง
                        window.location.reload();
                    } else {
                        // แสดงข้อความเมื่อมีปัญหาในการยกเลิกการจอง
                        alert('Failed to cancel booking.');
                    }
                }
            };
            // ส่งคำขอ HTTP GET ไปยัง cancel_booking.php โดยระบุค่า bookingID
            xhr.open("GET", "cancel_booking.php?booking_id=" + bookingID, true);
            xhr.send();
        }
    </script>