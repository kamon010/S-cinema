<?php
// Start the session
session_start();
// ติดต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
}

// ตรวจสอบว่ามีการส่งข้อมูลมาในรูปแบบที่คาดหวังหรือไม่
if (
    isset(
        $_POST['selected_seats'],
        $_POST['totalPrice'],
        $_POST['movie_name'],
        $_POST['movie_id'],
        $_POST['date'],
        $_POST['time'],
        $_POST['room_name'],
        $_POST['room_id'],
        $_POST['selected_seats_count'],
        $_POST['pointsUsed'] // เพิ่มการรับค่าแต้มที่ใช้
    )
) {
    // ดึงข้อมูลจาก $_POST
    $selected_seats = $_POST['selected_seats'];
    $total_price = $_POST['totalPrice'];
    $movie_name = $_POST['movie_name'];
    $movie_id = $_POST['movie_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $room_name = $_POST['room_name'];
    $room_id = $_POST['room_id'];
    $selected_seats_count = $_POST['selected_seats_count'];
    $pointsUsed = $_POST['pointsUsed']; // รับค่าแต้มที่ใช้

    // ตรวจสอบราคาที่ถูกส่งมา
    if (!is_numeric($total_price) || $total_price <= 0) {
        echo "ราคาที่ถูกส่งมาไม่ถูกต้อง";
        exit();
    }

    // ตรวจสอบ session
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // ดึงข้อมูลผู้ใช้จาก session
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];

    // ทำอย่างไรกับ $pointsUsed ที่ได้จากหน้าที่แล้วขึ้นกับว่าคุณต้องการนำมันไปใช้ยังไง
    $pointsUsed = isset($_POST['pointsUsed']) ? intval($_POST['pointsUsed']) : 0;

    // ตรวจสอบความถูกต้องของ $pointsUsed
    if ($pointsUsed < 0) {
        echo "ค่า Points ที่ใช้ไม่ถูกต้อง";
        exit();
    }
} else {
    // ถ้าไม่พบข้อมูลการจองที่ถูกส่งมา
    echo "ไม่พบข้อมูลการจองที่ถูกส่งมา";
}

?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        updateTotalPrice();
    });

    function updateTotalPrice() {
        const pricePerSeat = <?php echo $total_price; ?>;
        const selectedSeats = '<?php echo $selected_seats; ?>'.split(','); // รายการที่นั่งที่เลือก
        const numSeats = selectedSeats.length; // จำนวนที่นั่งที่เลือก
        const totalPrice = pricePerSeat * numSeats; // คำนวณราคารวมตามจำนวนที่นั่งที่เลือก
        document.getElementById('totalPriceSpan').textContent = totalPrice > 0 ? totalPrice + ' บาท' : '-';
        document.getElementById('totalPriceInput').value = totalPrice;
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // เรียกใช้งาน updatePoints() เมื่อหน้าเว็บโหลดเสร็จสมบูรณ์
        updatePoints();
    });

    function updatePoints() {
        const pricePerSeat = <?php echo $total_price; ?>;
        const pointsEarned = Math.floor(pricePerSeat / 50);
        document.getElementById('pointsEarnedSpan').textContent = pointsEarned + ' points';
    }
</script>

<script>
    // ในส่วนของ JavaScript ที่มีการคำนวณ Points Earned
    document.addEventListener("DOMContentLoaded", function() {
        // เรียกใช้งาน updatePoints() เมื่อหน้าเว็บโหลดเสร็จสมบูรณ์
        updatePoints();
    });

    function updatePoints() {
        const pricePerSeat = <?php echo $total_price; ?>;
        const pointsEarned = Math.floor(pricePerSeat / 50);

        // ตั้งค่าค่า Points Earned ใน input field
        document.getElementById('pointsEarnedInput').value = pointsEarned;

        // แสดงค่า Points Earned ใน span
        document.getElementById('pointsEarnedSpan').textContent = pointsEarned + ' points';
    }
</script>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>รายละเอียดการจอง</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* CSS Styles */
        body {
            background-image: url('https://resource.nationtv.tv/uploads/images/md/2021/10/G5k44IRqI2iCmZb4XlVR.jpg');
            /* ลิงก์ไปยังภาพพื้นหลังที่คุณต้องการใช้ */
            backdrop-filter: blur(10px);
            background-size: cover;
            background-position: center;
            background-color: #000;
            color: #FFF;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            background-color: #F8F8FF;
            /* สีพื้นหลังของ container */
            border: 2px solid #000;
            /* เส้นขอบของ container */
            padding: 20px;
            /* ระยะห่างของข้อมูลภายใน container */
            width: 30%;
            /* กำหนดความกว้างของ container */
            margin: 20px auto;
            /* กำหนดระยะห่างรอบ container และจัดครึ่งกลางตามแนวนอน */
            position: center;
        }

        section {
            padding: 20px;
            text-align: center;
            position: center;
            /* Limiting the width of the content */
        }

        h1 {
            text-align: center;
            /* Adding top margin to h1 */
            position: center;
        }

        h2 {
            text-align: center;
            /* Centering the h2 */
            position: center;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .button {
            text-align: center;
            /* จัดเนื้อหาตรงกลาง */
        }

        .text-color {
            color: #000000;
            /* เปลี่ยนสีตัวอักษรที่อยู่ใน Class นี้ทั้งหมด */
        }

        img {
            max-width: 50%;
            height: auto;
        }

        .button {
            text-align: center;
            margin-top: 20px;
            /* เพิ่มระยะห่างด้านบนระหว่างปุ่ม */
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 20px;
            /* ระยะห่างด้านบนระหว่างปุ่ม */
        }

        .btn-primary,
        .btn-cancel {
            flex: 1;
            /* ทำให้ปุ่มเต็มพื้นที่ */
            background-color: #670B0B;
            border-color: #670B0B;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 5px;
            /* ระยะห่างด้านขวาของปุ่ม .btn-primary */
        }

        .btn-primary:hover,
        .btn-cancel:hover {
            background-color: #990000;
        }

        .btn-cancel {
            order: -1;
            /* จัดปุ่ม "ย้อนกลับ" ไปอยู่ด้านซ้าย */
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-color" style="margin-bottom: 10px;">Booking details</h1>
        <section>
            <p class="text-color"><strong>Movie name :</strong> <?php echo htmlspecialchars($movie_name, ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="text-color"><strong>Selected date :</strong> <?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="text-color"><strong>Projected room :</strong> <?php echo htmlspecialchars($room_name, ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="text-color"><strong>Showtime :</strong> <?php echo htmlspecialchars($time, ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="text-color"><strong>Selected seat :</strong> <?php echo htmlspecialchars($selected_seats, ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="text-color"><strong>Price to pay :</strong> <?php echo htmlspecialchars($total_price, ENT_QUOTES, 'UTF-8'); ?> Bath</p>
            <p class="text-color"><strong>Points Earned :</strong> <span id="pointsEarnedSpan"></span></p>
            <p class="text-color"><strong>Number of seats selected :</strong> <?php echo count(explode(',', $selected_seats)); ?> Seat</p>
            <p class="text-color"><strong>Points Used :</strong> <?php echo htmlspecialchars($pointsUsed, ENT_QUOTES, 'UTF-8'); ?> points</p>

            <?php
            // คำสั่ง SQL เพื่อดึงข้อมูล QR code
            $sql = "SELECT QR FROM Payment";

            $result = $conn->query($sql);

            // แสดงภาพ QR code ถ้ามีข้อมูล
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<img src="data:image/png;base64,' . base64_encode($row['QR']) . '" alt="QR Code for Payment">';
                }
            } else {
                echo "ไม่พบข้อมูล QR Code";
            }
            ?>
        </section>
        <form id="bookingForm" method="post" action="process_booking.php">
            <input type="hidden" name="selected_seats" value="<?php echo htmlspecialchars($selected_seats, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="totalPrice" value="<?php echo htmlspecialchars($total_price, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="movie_name" value="<?php echo htmlspecialchars($movie_name, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="movie_id" value="<?php echo htmlspecialchars($movie_id, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="time" value="<?php echo htmlspecialchars($time, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="room_name" value="<?php echo htmlspecialchars($room_name, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room_id, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="selected_seats_count" value="<?php echo count(explode(',', $selected_seats)); ?>">
            <input type="hidden" name="pointsUsed" value="<?php echo htmlspecialchars($pointsUsed, ENT_QUOTES, 'UTF-8'); ?>">
            <!-- Add this input field to include Points Earned in the form -->
            <!-- ในส่วนของ form ที่มี id="bookingForm" -->
            <input type="hidden" name="pointsEarned" id="pointsEarnedInput" value="">

            <<div class="button">
                <input type="submit" name="confirm" class="btn btn-primary" value="Confirm payment">

        </form>
        <!-- Add back button here -->
        <div class="button">
            <button class="btn btn-primary btn-cancel" id="goBack">Retrospective</button>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // When the "ย้อนกลับ" button is clicked, go back to the previous page
                document.querySelector("#goBack").addEventListener("click", function() {
                    window.history.back();
                });
            });
        </script>
        </form>
    </div>
    </div>
</body>

</html>