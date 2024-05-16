<?php
session_start();

// ติดต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
}

$selected_seats_count = $_POST['selected_seats_count'];
if (isset($_POST['movie_name'], $_POST['date'], $_POST['time'], $_POST['selected_seats'], $_POST['totalPrice'], $_POST['room_name'], $_POST['room_id'], $_POST['selected_seats_count'], $_POST['pointsUsed'])) {
    $movie_name = $_POST['movie_name'];
    $movie_id = $_POST['movie_id'];
    $date_parts = explode('-', $_POST['date']);
    $date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
    $time = $_POST['time'];
    $selected_seats = $_POST['selected_seats'];
    $total_price = $_POST['totalPrice'];
    $room_name = $_POST['room_name'];
    $room_id = $_POST['room_id'];
    $booking_date = date('Y-m-d');
    $selectedSeatsCount = $_POST['selected_seats_count'];
    $points_used = $_POST['pointsUsed'];
    $pointsEarned = isset($_POST['pointsEarned']) ? intval($_POST['pointsEarned']) : 0;

    if ($pointsEarned < 0) {
        echo "ค่า Points Earned ไม่ถูกต้อง";
        exit();
    }

    $sql_check_booked_seats = "SELECT SelectedSeats FROM Bookings WHERE MovieID = '$movie_id' AND ShowtimeDate = '$date' AND ShowtimeTime = '$time' AND RoomID = '$room_id'";
    $result_check_booked_seats = $conn->query($sql_check_booked_seats);

    if ($result_check_booked_seats->num_rows > 0) {
        $row = $result_check_booked_seats->fetch_assoc();
        $bookedSeats = explode(',', $row['SelectedSeats']);

        $selectedSeatsArray = explode(',', $selected_seats);
        $overlap = array_intersect($bookedSeats, $selectedSeatsArray);

        if (!empty($overlap)) {
            echo "บางที่นั่งที่คุณเลือกได้ถูกจองแล้ว กรุณาเลือกที่นั่งอื่น";
            exit();
        }
    }

    $sql_update_user_points_earned = "UPDATE Users SET Point = Point + $pointsEarned WHERE UsersID = '$user_id'";

    if ($conn->query($sql_update_user_points_earned) === TRUE) {
        echo "";
    } else {
        echo "เกิดข้อผิดพลาดในการปรับปรุง Points ของผู้ใช้: " . $conn->error;
        exit();
    }

    $sql_get_current_points = "SELECT Point FROM Users WHERE UsersID = '$user_id'";
    $result_get_current_points = $conn->query($sql_get_current_points);

    if ($result_get_current_points->num_rows > 0) {
        $row_current_points = $result_get_current_points->fetch_assoc();
        $current_points = $row_current_points['Point'];

        if ($current_points < $points_used) {
            echo "คุณมีแต้มไม่เพียงพอที่จะใช้งาน";
            exit();
        }

        $new_points = $current_points - $points_used;

        $sql_update_user_points = "UPDATE Users SET Point = '$new_points' WHERE UsersID = '$user_id'";

        if ($conn->query($sql_update_user_points) === TRUE) {
            echo "";
        } else {
            echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล Point ผู้ใช้: " . $conn->error;
            exit();
        }
    } else {
        echo "ไม่สามารถดึงค่า Point ปัจจุบันของผู้ใช้ได้";
        exit();
    }

    $sql_insert_booking = "INSERT INTO Bookings (UsersID, MovieID, NameMovie, ShowtimeDate, ShowtimeTime, SelectedSeats, TotalPrice, RoomID, BookingDate, Quantity, `Points_spent`, Status) 
    VALUES ('$user_id', '$movie_id','$movie_name', '$date', '$time', '$selected_seats', '$total_price', '$room_id', '$booking_date', '$selectedSeatsCount', '$points_used', 'Confirm')";    

    if ($conn->query($sql_insert_booking) === TRUE) {
        $sql_movie_details = "SELECT * FROM Movie WHERE MovieID = $movie_id";
        $result_movie_details = $conn->query($sql_movie_details);
        $row_movie_details = $result_movie_details->fetch_assoc();
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirmation Page</title>
            <style>
                body {
                    background-image: url('https://resource.nationtv.tv/uploads/images/md/2021/10/G5k44IRqI2iCmZb4XlVR.jpg');
                    /* ภาพพื้นหลังหาเปลี่ยนได้ตามต้องการ */
                    backdrop-filter: blur(10px);
                    background-size: cover;
                    font-family: Arial, sans-serif;
                    margin: 0;
                    /* กำหนดให้ไม่มีการเพิ่ม margin ใน body */
                    padding: 0;
                    /* กำหนดให้ไม่มีการเพิ่ม padding ใน body */
                    width: 100vw;
                    /* ความกว้างของ body เท่ากับ viewport width */
                    height: 100vh;
                    /* ความสูงของ body เท่ากับ viewport height */
                    overflow: auto;
                    /* ให้เกิดการเลื่อนหน้าจอเมื่อเนื้อหาเกินขนาด viewport */
                }

                .container {
                    text-align: center;
                    position: center;
                    max-width: 600px;
                    /* Increase container width */
                    margin: 30px auto;
                    padding: 20px;
                    background-color: #FFE4E1;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }

                h2 {
                    font-size: 20px;
                    color: #990000;
                    text-align: center;
                    position: center;
                }

                p {
                    margin: 10px 0;
                    text-align: center;
                    position: center;
                }

                strong {
                    font-weight: bold;
                }

                .download-btn {
                    text-decoration: none;
                    padding: 10px 20px;
                    background-color: #990000;
                    /* เปลี่ยนสีปุ่ม ทั้งสองปุ่ม */
                    color: white;
                    border-radius: 5px;
                    cursor: pointer;
                    display: inline-block;
                    margin-top: 10px;
                    /* Adjust margin */
                }

                .button {
                    text-align: center;
                    margin-top: 20px;
                    cursor: pointer;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <h2>Ticket</h2>
                <p><strong>ชื่อหนัง:</strong> <?= $row_movie_details['NameMovie'] ?></p>
                <p><strong>วันที่:</strong> <?= $date ?> </p>
                <p><strong>เวลา:</strong> <?= $time  ?></p>
                <p><strong>ที่นั่งที่เลือก:</strong> <?= $selected_seats ?></p>
                <p><strong>จำนวนที่นั่ง:</strong> <?= $selectedSeatsCount ?></p>
                <p><strong>ห้อง:</strong> <?= $room_name ?></p>
                <p><strong>ราคาที่ต้องชำระ:</strong> <?= $total_price ?> บาท</p>
            </div>

            <div class="button">
                <a href="homepage.php" class="download-btn">กลับไปที่หน้าหลัก</a>
            </div>

            <div class="button">
                <button onclick="saveTicketImage()" class="download-btn">บันทึกภาพตั๋ว</button>
            </div>

            <script>
                function saveTicketImage() {
                    var canvas = document.createElement('canvas');
                    canvas.width = 650;
                    canvas.height = 400;
                    var ctx = canvas.getContext('2d');

                    ctx.fillStyle = '#FFE4E1';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#571111';
                    ctx.font = 'bold 20px Arial';
                    ctx.fillText('Ticket', 20, 40);
                    ctx.fillText('ชื่อหนัง: <?= $row_movie_details['NameMovie'] ?>', 20, 80);
                    ctx.fillText('วันที่: <?= $date ?> ', 20, 120);
                    ctx.fillText('เวลา: <?= $time ?>', 20, 160);
                    ctx.fillText('ที่นั่งที่เลือก: <?= $selected_seats ?>', 20, 200);
                    ctx.fillText('จำนวนที่นั่ง: <?= $selectedSeatsCount ?>', 20, 240);
                    ctx.fillText('ห้อง: <?= $room_name ?>', 20, 280);
                    ctx.fillText('ราคาที่ต้องชำระ: <?= $total_price ?> บาท', 20, 320);

                    var imageData = canvas.toDataURL('image/jpeg');

                    var downloadLink = document.createElement('a');
                    downloadLink.href = imageData;
                    downloadLink.download = 'ticket.jpg';

                    document.body.appendChild(downloadLink);
                    downloadLink.click();

                    document.body.removeChild(downloadLink);

                }
            </script>
        </body>

        </html>

<?php

    } else {
        echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error;
    }
} else {
    echo "ไม่พบข้อมูลการจองที่ถูกส่งมา";
}

$conn->close();
?>