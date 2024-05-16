<?php
// Check if the user is logged in
session_start();

if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];
?>
<?php
// Database connection parameters
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';
// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// URL ของหน้าก่อนหน้า
$previous_page_url = "homepage.php?user_id=" . $user['UsersID'] . '&name=' . urlencode($user['Name']);

// ดึงข้อมูลผู้ใช้จาก session
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql_user = "SELECT Point FROM Users WHERE UsersID = $user_id";
$result_user = $conn->query($sql_user);

// ตรวจสอบว่ามีผลลัพธ์ที่ถูกต้องหรือไม่
if ($result_user->num_rows > 0) {
    $row_user = $result_user->fetch_assoc();
    // เก็บค่า Point ไว้ในตัวแปร $points
    $points = $row_user['Point'];
} else {
    // หากไม่พบข้อมูลผู้ใช้
    // สามารถกำหนดค่า $points เป็นค่าเริ่มต้นหรือค่าที่ต้องการได้
    $points = 0;
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

// Get current date
$currentDate = date('Y-m-d');

// Fetch promotions from the database where StartDate and EndDate are in the future or StartDate is in the future and EndDate is in the future
$sql_promotions = "SELECT PromotionName, DiscountAmount, StartDate, EndDate 
                                FROM Promotion 
                                WHERE (StartDate <= '$currentDate' AND EndDate >= '$currentDate') 
                                OR (StartDate >= '$currentDate' AND EndDate >= '$currentDate')";
$result_promotions = $conn->query($sql_promotions);

// Check if promotions are available
if ($result_promotions->num_rows > 0) {
    // Display promotions
    while ($row_promotion = $result_promotions->fetch_assoc()) {
        echo '<div class="promotion-card">'; // เพิ่ม margin-bottom ที่นี่
        echo '<h2>' . $row_promotion['PromotionName'] . '</h2>';
        echo '<p>Discount Amount: ' . $row_promotion['DiscountAmount'] . '</p>';
        echo '<p>Start Date: ' . date('d F Y', strtotime($row_promotion['StartDate'])) . '</p>';
        echo '<p>End Date: ' . date('d F Y', strtotime($row_promotion['EndDate'])) . '</p>';

        // Check if the promotion is still not available (StartDate in the future)
        if (strtotime($row_promotion['StartDate']) > strtotime($currentDate)) {
            echo '<a class="btn-more" style="background-color: #FF7C25; cursor: not-allowed;">Still not available</a>';
        } else {
            echo '<a class="btn-more" href="homepage.php?promotion_id=' . $row_promotion['PromotionID'] . '">Click to go to the movie selection page</a>';
        }

        echo '</div>';
    }
} else {
    echo "No promotions available.";
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* สไตล์ CSS ที่ใช้ในหน้า homepage.php */
        body {
            margin: 0;
            background-color: #000;
            color: #FFF;
            font-family: Arial, sans-serif;
            margin-top: 70px;
            /* เพิ่ม margin-top เพื่อห่างจากบาร์ด้านบน */
        }

        nav {
            background-color: #212121;
            padding-top: 25px;
            padding-bottom: 25px;
            text-align: center;
            top: 0;
            width: 100%;
            z-index: 1000;
            position: fixed;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
            margin: 0 10px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            position: relative;
            transition: color 0.3s ease-in-out;
        }

        .nav-link:hover {
            color: #FF3187;
        }

        .nav-link:hover .user-icon {
            bottom: 10px;
        }

        .nav-link:hover i {
            transform: translateY(-5px);
        }

        i {
            transition: transform 0.3s ease-in-out;
        }

        section {
            padding: 20px;
        }

        footer {
            background-color: #212121;
            padding: 10px;
            text-align: center;
        }

        .movie-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .movie-card {
            background-color: #333;
            color: #FFF;
            padding: 10px;
            margin: 10px;
            text-align: center;
            width: 200px;
        }

        .secondary-nav {
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 90px;
            /* ปรับตำแหน่งลงมา 20px */
        }

        .secondary-nav a {
            display: inline-block;
            padding: 15px 30px;
            background-color: #FF8EBD;
            /* สีเทาๆ */
            color: #FFF;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .secondary-nav a:hover {
            background-color: #722B48;
            /* เปลี่ยนสีเมื่อนำเมาส์ hover */
        }

        .secondary-nav a:nth-child(2) {
            background-color: #8D003B;
            /* เปลี่ยนสีตามที่คุณต้องการ */
        }


        section {
            padding: 20px;
            margin-top: 10px;
            /* เพิ่ม margin-top เพื่อห่างจากบาร์ด้านบน */
        }

        footer {
            background-color: #212121;
            padding: 10px;
            text-align: center;
        }

        .movie-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .movie-card {
            background-color: #333;
            color: #FFF;
            padding: 10px;
            margin: 10px;
            text-align: center;
            width: 200px;
        }

        /* เพิ่มสไตล์ปุ่ม */
        .btn-more {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FF3187;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        /* เมื่อนำเมาส์ไปวัตถุแล้วปรากฏเอฟเฟกต์ */
        .btn-more:hover {
            background-color: #8D003B;
        }

        .image-container {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            white-space: nowrap;
        }

        .image-container img {
            width: 100%;
            /* ปรับขนาดภาพให้เต็ม container */
            max-width: 100%;
            /* ปรับขนาดสูงสุดให้ภาพไม่ขยายเกินขนาดต้นฉบับ */
            max-height: 300px;
            /* ปรับความสูงสูงสุดของภาพ */
            margin-right: 10px;
            scroll-snap-align: start;
        }

        #slideshow-container {
            margin-top: -40px;
            /* หรือใช้ค่าติดลบที่เหมาะสม */
        }

        .user-icon {
            right: 10px;
            width: 50px;
            height: 50px;
            background: url('https://www.majorcineplex.com/public/mobile_new_assets/img/avatar.png') no-repeat center center;
            background-size: cover;
            border-radius: 50%;
            cursor: pointer;
            transition: bottom 0.5s ease-in-out;
            position: absolute;
            /* Position the user icon absolutely within the .nav-link */
            bottom: 10px;
            /* Initially set to 10px from the bottom */
        }

        /* สไตล์ CSS สำหรับแถบข้อความที่ยื่นออกมา */
        .menu-container {
            display: none;
            position: fixed;
            top: 70px;
            right: 10px;
            background-color: #333;
            color: #FFF;
            padding: 10px;
            border-radius: 5px;
            flex-direction: column;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        .menu-item {
            text-align: left;
            padding: 10px;
            /* เพิ่ม Padding เพื่อให้มีพื้นที่รอบๆ ข้อความ */
            transition: background-color 0.3s ease;
            /* เพิ่มเอฟเฟกต์ transition สำหรับเปลี่ยนสีพื้นหลัง */
        }

        .menu-item:hover {
            background-color: #555;
            /* เปลี่ยนสีพื้นหลังเมื่อนำเมาส์ hover */
        }

        .menu-item::before {
            content: '\2022';
            /* รหัส Unicode สำหรับเครื่องหมายดอกจัน (•) */
            color: #FF3187;
            /* สีไอคอน */
            margin-right: 5px;
            /* ระยะห่างระหว่างไอคอนกับข้อความ */
        }

        body {
            margin-top: 70px;
        }

        .menu-container {
            text-align: left;
        }

        .email-container {
            position: fixed;
            top: 15px;
            right: 60px;
            padding: 10px;
            z-index: 1001;
        }

        .email-link {
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #333;
            /* เปลี่ยนสีพื้นหลังเป็นสีเทาเข้ม */
            margin-top: 0px;
            /* ปรับตำแหน่งของ email-link ขึ้น */
        }

        .email-link:hover {
            background-color: #444;
            /* เปลี่ยนสีพื้นหลังเมื่อโฮเวอร์เป็นสีเทาเข้มขึ้น */
        }

        .promotion-card {
            background-color: #444;
            /* เปลี่ยนสีพื้นหลัง */
            color: #FFF;
            padding: 20px;
            margin: 20px auto;
            /* กำหนดให้ promotion-card อยู่ตรงกลาง */
            max-width: 600px;
            /* กำหนดความกว้างสูงสุดของ promotion-card */
            border-radius: 10px;
            /* กำหนดรูปร่างของ promotion-card */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            /* เพิ่มเงาให้ promotion-card */
            text-align: center;
            /* จัดข้อความให้อยู่ตรงกลาง */
        }
    </style>
</head>

<body>
    <nav>
        <div class="user-icon" id="userIcon" onclick="toggleMenu()"></div>
        <div class="menu-container" id="menuContainer">
            <div class="menu-item" id="editInfo">Edit personal</div>
            <div class="menu-item" id="userPoint">Point : <?php echo $points; ?></div>
            <div class="menu-item" id="logOut">Log out</div>
        </div>
        <a href="headder.php" class="nav-link"><i class="fas fa-home"></i> Homepage</a>
        <a href="homepage.php" class="nav-link"><i class="fas fa-film"></i> Movie</a>
        <a href="homepage.php" class="nav-link"><i class="fas fa-percent"></i> Promotion</a>
    </nav>

    <div class="email-container">
        <?php echo $email_link; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ปิดเมนูเมื่อหน้าเว็บโหลด
            var menuContainer = document.getElementById('menuContainer');
            menuContainer.style.display = 'none';

            // เพิ่ม event listener เมื่อคลิกที่ไอคอนผู้ใช้
            var userIcon = document.getElementById('userIcon');
            userIcon.addEventListener('click', function() {
                // สลับการแสดงผลของเมนู
                menuContainer.style.display = (menuContainer.style.display === 'none' || menuContainer.style.display === '') ? 'block' : 'none';
            });

            // Redirect to logout page on logout button click
            document.getElementById("logOut").addEventListener("click", function() {
                window.location.href = "logout.php";
            });

            // Redirect to edit_profile.php page on editInfo button click
            document.getElementById("editInfo").addEventListener("click", function() {
                var user_id = "<?php echo $user_id; ?>";
                window.location.href = "edit_profile.php?user_id=" + user_id;
            });

            // Redirect to bookinghistory.php page on userHis button click
            document.getElementById("userHis").addEventListener("click", function() {
                var user_id = "<?php echo $user_id; ?>";
                window.location.href = "bookinghistory.php?user_id=" + user_id;
            });
        });
    </script>

    <footer>
        <p>&copy; S-Cinima - Naresuann</p>
    </footer>

</body>

</html>