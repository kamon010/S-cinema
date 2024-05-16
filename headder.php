<?php
// เช็คว่ามี session ที่เกิดขึ้นหรือไม่
session_start();

// เช็คว่าผู้ใช้เข้าสู่ระบบหรือยัง
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ได้เข้าสู่ระบบให้ redirect ไปที่หน้า login
    header("Location: login.php");
    exit();
}

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

// ดึงข้อมูลผู้ใช้จาก session
$user_id = $_SESSION['user_id'];

/// คำสั่ง SQL เพื่อดึงข้อมูลผู้ใช้
$sql_user = "SELECT * FROM Users WHERE UsersID = $user_id";
$result_user = $conn->query($sql_user);

// ตรวจสอบว่ามีข้อมูลผู้ใช้หรือไม่
if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    // URL ของหน้าก่อนหน้า
    $previous_page_url = "moviemore.php?user_id=" . $user['UsersID'] . '&name=' . urlencode($user['Name']) . '&movie_id=' . $row_movie_details["MovieID"];
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

// คำสั่ง SQL เพื่อดึงข้อมูลภาพยนตร์
$sql_movies = "SELECT Movie.MovieID, Movie.NameMovie, Movie.ReleaseDate, Genre.Genre, Movie.Poster, Movie.LinkVDO, Movie.LeavingDate 
                FROM Movie
                JOIN Genre ON Movie.GenreID = Genre.GenreID
                WHERE Movie.ReleaseDate > CURDATE()";
$result_movies = $conn->query($sql_movies);

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
            background-color: #8D003B;
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
            background-color: #FF8EBD;
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
    </style>
</head>

<body>

    <nav>
        <div class="user-icon" id="userIcon" onclick="toggleMenu()"></div>
        <div class="menu-container" id="menuContainer">
            <div class="menu-item" id="editInfo">Edit personal</div>
            <div class="menu-item" id="userHis">Booking history</div>
            <div class="menu-item" id="userPoint">Point : <?php echo $points; ?></div>
            <div class="menu-item" id="logOut">Log out</div>
        </div>
        <a href="headder.php" class="nav-link"><i class="fas fa-home"></i> Homepage</a>
        <a href="homepage.php" class="nav-link"><i class="fas fa-film"></i> Movie</a>
        <a href="promotion.php" class="nav-link"><i class="fas fa-percent"></i> Promotion</a>
    </nav>

    <div class="secondary-nav">
        <a href="homepage.php">Movies shown</a> <!-- เพิ่มปุ่มนี้ -->
        <a href="headder.php">Next program</a> <!-- เพิ่มปุ่มนี้ -->
    </div>

    <div class="email-container">
        <?php echo $email_link; ?>
    </div>

    <section>
        <div class="container">
            <section>
                <div class="image-container" id="slideshow-container">
                    <?php
                    // Query to fetch images with specific PosterID from database
                    $posterIds = ['0000001', '0000002','0000003','0000004','0000005']; // เพิ่ม PosterID ที่ต้องการ
                    foreach ($posterIds as $posterId) {
                        $sql = "SELECT Poster FROM Poster_Promotion WHERE PosterID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('s', $posterId);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Display images
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<img src="data:image/png;base64,' . base64_encode($row['Poster']) . '" />';
                            }
                        }

                        // Close statement
                        $stmt->close();
                    }

                    // Close connection
                    $conn->close();
                    ?>
                </div>
            </section>
        </div>
        <h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;About to enter the movie theater</h2>
        <div class="movie-container">
            <?php
            // แสดงผลภาพยนตร์
            if ($result_movies->num_rows > 0) {
                $count = 0; // ตัวแปรนับจำนวนหนัง
                while ($row_movies = $result_movies->fetch_assoc()) {
                    if ($count % 5 == 0) {
                        echo '</div>'; // ปิดแถวก่อนหนังที่ 6, 11, ...
                        echo '<div class="movie-container">';
                    }
                    echo '<div class="movie-card">';
                    echo '<img src="' . $row_movies["Poster"] . '" alt="' . $row_movies["NameMovie"] . '" style="width: 100%;">';
                    echo '<h3>' . $row_movies["NameMovie"] . '</h3>';
                    echo '<p>วันที่เข้าฉาย: ' . $row_movies["ReleaseDate"] . '</p>';
                    echo '<p>ประเภท: ' . $row_movies["Genre"] . '</p>';
                    echo '<a class="btn-more" href="moviemore.php?user_id=' . $user_id . '&name=' . urlencode($user['Name']) . '&movie_id=' . $row_movies["MovieID"] . '">View details</a>';
                    echo '</div>';
                    $count++;
                }
                // ปิดแถวสุดท้าย (กรณีที่ไม่ได้ปิดแถวในการวน loop)
                echo '</div>';
            } else {
                echo "No movies available";
            }
            ?>
        </div>
    </section>

    <script>
        // JavaScript for automatic slideshow
        const slideshowContainer = document.getElementById('slideshow-container');
        const images = document.querySelectorAll('.image-container img');
        let currentImageIndex = 0;

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            slideshowContainer.scrollLeft = images[currentImageIndex].offsetLeft;
        }

        // Set timeout to wait for 5 seconds before starting slideshow
        setTimeout(function() {
            slideshowContainer.style.marginTop = '-40px'; /* หรือใช้ค่าติดลบที่เหมาะสม */
            setInterval(nextImage, 1000); // เลื่อนภาพทุก 1 วินาที
        }, 1000); // 1 seconds timeout before starting
    </script>

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

<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>