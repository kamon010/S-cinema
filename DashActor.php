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

// ตรวจสอบว่ามี session ของผู้ใช้ที่เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    // หากไม่มี session ของผู้ใช้ที่เข้าสู่ระบบ ให้ Redirect ไปยังหน้า login
    header('Location: loginContrl.php');
    exit();
}

// ดึง user_id และ user_name จาก session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// คำสั่ง SQL เพื่อดึงข้อมูล Actors จากตาราง Actors
$sql_actors = "SELECT DISTINCT a.ActorsID, a.NameActor
FROM Actors a";
$result_actors = $conn->query($sql_actors);

// ตรวจสอบว่ามีการเลือก Actor หรือไม่
if (isset($_POST['actors']) && !empty($_POST['actors'])) {
    $selected_actor_id = $_POST['actors'];

    // คำสั่ง SQL เพื่อดึงชื่อของ Actor ที่เลือก
    $sql_actor_info = "SELECT NameActor
FROM Actors
WHERE ActorsID = '$selected_actor_id' LIMIT 1";
    $result_actor_info = $conn->query($sql_actor_info);

    // ตรวจสอบว่าพบข้อมูลหรือไม่
    if ($result_actor_info && $result_actor_info->num_rows > 0) {
        $row_actor_info = $result_actor_info->fetch_assoc();
        $selected_actor = $row_actor_info['NameActor'];

        // คำสั่ง SQL เพื่อดึงหนังที่มีนักแสดงที่ผู้ใช้เลือก
        $sql_movies = "SELECT m.MovieID, m.NameMovie
FROM Movie_Actors ma
INNER JOIN Movie m ON ma.MovieID = m.MovieID
WHERE ma.ActorsID = '$selected_actor_id' AND m.MovieID IN (SELECT DISTINCT MovieID FROM Bookings)";
        $result_movies = $conn->query($sql_movies);

        // สร้างตัวแปรเก็บข้อมูลของยอดขายแต่ละหนัง
        $movie_sales_data = array();
        while ($row_movies = $result_movies->fetch_assoc()) {
            $movie_id = $row_movies['MovieID'];
            $movie_name = $row_movies['NameMovie'];

            // คำสั่ง SQL เพื่อดึงยอดขายของหนังนี้
            $sql_movie_sales = "SELECT DATE(BookingDate) AS BookingDay, SUM(TotalPrice) AS TotalSales
FROM Bookings
WHERE MovieID = '$movie_id'
GROUP BY DATE(BookingDate)
ORDER BY DATE(BookingDate)";
            $result_movie_sales = $conn->query($sql_movie_sales);

            // เก็บข้อมูลยอดขายของหนังนี้ลงในตัวแปร $movie_sales_data เฉพาะหนังที่มีข้อมูลยอดขาย
            $movie_sales = array();
            while ($row_movie_sales = $result_movie_sales->fetch_assoc()) {
                $booking_day = $row_movie_sales['BookingDay'];
                $total_sales = $row_movie_sales['TotalSales'];
                $movie_sales[$booking_day] = $total_sales;
            }
            // เพิ่มข้อมูลยอดขายของหนังนี้ในตัวแปร $movie_sales_data
            if (!empty($movie_sales)) {
                $movie_sales_data[$movie_name] = $movie_sales;
            }
        }
    }
} else {
    // หากยังไม่มีการเลือก Actor ใด ๆ ให้ดึงข้อมูลนักแสดงที่ทำรายได้มากที่สุด
    $sql_top_actor = "SELECT a.NameActor, SUM(b.TotalPrice) AS TotalSales
FROM Actors a
INNER JOIN Movie_Actors ma ON a.ActorsID = ma.ActorsID
INNER JOIN Movie m ON ma.MovieID = m.MovieID
INNER JOIN Bookings b ON m.MovieID = b.MovieID
GROUP BY a.NameActor
ORDER BY TotalSales DESC
LIMIT 1";
    $result_top_actor = $conn->query($sql_top_actor);

    if ($result_top_actor && $result_top_actor->num_rows > 0) {
        $row_top_actor = $result_top_actor->fetch_assoc();
        $selected_actor = $row_top_actor['NameActor'];
        $top_actor_sales = $row_top_actor['TotalSales'];

        // คำสั่ง SQL เพื่อดึงหนังที่นักแสดงที่ทำรายได้มากที่สุดเป็นผู้แสดง
        $sql_top_actor_movies = "SELECT m.MovieID, m.NameMovie
FROM Movie_Actors ma
INNER JOIN Movie m ON ma.MovieID = m.MovieID
INNER JOIN Bookings b ON m.MovieID = b.MovieID
WHERE ma.ActorsID IN (SELECT ActorsID FROM Actors WHERE NameActor = '$selected_actor')";
        $result_top_actor_movies = $conn->query($sql_top_actor_movies);

        // สร้างตัวแปรเก็บข้อมูลของยอดขายแต่ละหนัง
        $movie_sales_data = array();
        while ($row_top_actor_movies = $result_top_actor_movies->fetch_assoc()) {
            $movie_id = $row_top_actor_movies['MovieID'];
            $movie_name = $row_top_actor_movies['NameMovie'];

            // คำสั่ง SQL เพื่อดึงยอดขายของหนังนี้
            $sql_movie_sales = "SELECT DATE(BookingDate) AS BookingDay, SUM(TotalPrice) AS TotalSales
FROM Bookings
WHERE MovieID = '$movie_id'
GROUP BY DATE(BookingDate)
ORDER BY DATE(BookingDate)";
            $result_movie_sales = $conn->query($sql_movie_sales);

            // เก็บข้อมูลยอดขายของหนังนี้ลงในตัวแปร $movie_sales_data เฉพาะหนังที่มีข้อมูลยอดขาย
            $movie_sales = array();
            while ($row_movie_sales = $result_movie_sales->fetch_assoc()) {
                $booking_day = $row_movie_sales['BookingDay'];
                $total_sales = $row_movie_sales['TotalSales'];
                $movie_sales[$booking_day] = $total_sales;
            }
            // เพิ่มข้อมูลยอดขายของหนังนี้ในตัวแปร $movie_sales_data
            if (!empty($movie_sales)) {
                if(isset($movie_sales_data[$movie_name])){
                    $movie_sales_data[$movie_name] += $movie_sales;
                } else {
                    $movie_sales_data[$movie_name] = $movie_sales;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="widtxh=device-width, initial-scale=1.0">
    <title>Movie Sales by Actor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Booking.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .column {
            flex: 33.33%;
            padding: 0 15px;
            box-sizing: border-box;
            /* เพิ่มเพื่อป้องกันการเกินขอบเขตของคอลัมน์ */
        }
    </style>
</head>

<body>
    <nav>
        <div class="user-icon" id="userIcon" onclick="toggleMenu()"></div>
        <div class="menu-container" id="menuContainer">
            <div class="menu-item" id="editInfo">Edit personal</div>
            <div class="menu-item" id="logOut">Log out</div>
        </div>
        <?php
        echo '<form method="post" action="">';
        echo '<select name="actors" id="actors" class="nav-link" style="background-color: #424242; color: #FFFFFF; width: 150px;">';
        echo '<option value="" disabled selected>Actor</option>'; // Add the first option
        while ($row_actors = $result_actors->fetch_assoc()) {
            echo '<option value="' . $row_actors['ActorsID'] . '">' . $row_actors['NameActor'] . '</option>';
        }
        echo '</select>';
        echo '<input type="submit" value="Show" class="nav-link" style="background-color: #D594C5; color: #FFFFFF;">';
        echo '</form>';
        ?>
    </nav>
    <div class="email-container" style="margin-top: -20px;">
        <h4>Welcome <?php echo $user_name; ?>!</h4>
    </div>

    <?php if (isset($selected_actor) && isset($movie_sales_data)) { ?>
        <div>
            <h4>Total Sales of Movies with Actor <?php echo $selected_actor; ?></h4>
            <?php $count = 0; ?>
            <div class="row"> <!-- เพิ่ม div เริ่มต้นแถว -->
                <?php foreach ($movie_sales_data as $movie_name => $movie_sales) { ?>
                    <?php if ($count % 3 == 0 && $count != 0) { ?>
            </div>
            <div class="row"> <!-- ปิดแถวปัจจุบันและเริ่มต้นแถวใหม่ -->
            <?php } ?>
            <div class="column">
                <h5><?php echo $movie_name; ?></h5>
                <canvas id="<?php echo str_replace(' ', '', $movie_name) . 'Chart'; ?>" width="400" height="200"></canvas>
                <p>Total Sales: $<?php echo number_format(array_sum($movie_sales), 2); ?></p>
            </div>
            <?php $count++; ?>
        <?php } ?>
            </div> <!-- ปิด div แถวสุดท้าย -->
        </div>

        <script>
            <?php foreach ($movie_sales_data as $movie_name => $movie_sales) { ?>
                var sales_data_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?> = <?php echo json_encode($movie_sales); ?>;
                var bookingDays_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?> = Object.keys(sales_data_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?>);
                var sales_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?> = Object.values(sales_data_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?>);

                var ctx_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?> = document.getElementById('<?php echo str_replace([' ', '-'], ['', '_'], $movie_name) . 'Chart'; ?>').getContext('2d');
                var gradient_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?> = ctx_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?>.createLinearGradient(0, 0, 0, 200);
                gradient_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?>.addColorStop(0, '#FF3187');
                gradient_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?>.addColorStop(1, '#670015');

                var myChart_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?> = new Chart(ctx_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?>, {
                    type: 'line',
                    data: {
                        labels: bookingDays_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?>,
                        datasets: [{
                            label: 'Total Sales',
                            data: sales_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?>,
                            backgroundColor: gradient_<?php echo str_replace([' ', '-'], ['', '_'], $movie_name); ?>,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';

                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toLocaleString();
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            <?php } ?>
        </script>
    <?php } ?>

    <script>
        // คำสั่งเชื่อมต่อ DOMContentLoaded เพื่อให้หน้าเว็บโหลดเสร็จสมบูรณ์ก่อน
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
                window.location.href = "logoutContrl.php";
            });
            document.getElementById("editInfo").addEventListener("click", function() {
                // ตรวจสอบว่าตัวแปร $user_id มีค่าหรือไม่
                var user_id = "<?php echo $user_id; ?>";
                window.location.href = "edit_profile.php?user_id=" + user_id;
            });
        });
    </script>
</body>

</html>
