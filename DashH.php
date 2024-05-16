<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>S-Cinima - Naresuann</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- เปลี่ยน Slim เป็นเต็ม -->
    <style>
        /* สไตล์ CSS ที่ใช้ในหน้า moviemore.php */
        .navbar {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 9999;
            margin-bottom: 20px; /* เพิ่มพื้นที่ด้านล่างของ Navbar */
        }

        .navbar-brand {
            position: relative;
            left: 20px;
            margin-right: 60px;
        }
        body {
            background-color: ##f2f2ed;
            color: #212121;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
        }
        .logout-btn {
            color: #FFF;
            background-color: rgba(255, 99, 132, 1);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            float: right; /* จัดให้ปุ่มอยู่ทางขวาสุด */
            margin-top: -43px; /* ให้มีระยะห่างด้านบน */
        }

        .logout-btn:hover {
            background-color: #9E1D5D;
        }
        nav {
            background-color: rgba(255, 99, 132, 1);
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: #FFF;
            text-decoration: none;
            padding: 10px;
            margin: 0 10px;
        }

        section {
            padding: 20px;
            text-align: center;
        }

        footer {
            background-color: rgba(255, 99, 132, 1);
            padding: 10px;
            text-align: center;
        }

        .movie-details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .movie-poster {
            width: 300px;
            margin-right: 20px;
        }

        .movie-details-text {
            flex: 1;
            text-align: left;
        }

        /* เพิ่มสไตล์ Canvas */
        canvas {
            max-width: 800px; /* ปรับขนาดของกราฟตามความเหมาะสม */
            margin: 0 auto;
        }

        /* เพิ่มสไตล์กราฟแท่งและกราฟวงกลม */
        #salesChartsContainer {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 20px; /* ระยะห่างข้างล่างของ Container */
        }

        /* เพิ่มสไตล์กราฟแท่ง */
        #genreSalesChart {
            height: 400px;
            max-width: 5000px; /* ปรับขนาดของกราฟตามความเหมาะสม */
            margin-left: 20px auto; /* จัดกราฟไปที่กลาง */
            margin-top: -180px;
            padding-bottom: 40px;
        }
        /* เพิ่มสไตล์กราฟวงกลม */
        #movieSalesChart {
            max-width: 400px; /* ปรับขนาดของกราฟตามความเหมาะสม */
            margin-left: 20px auto; /* จัดกราฟไปที่กลาง */
        }
        /* เพิ่มสไตล์กราฟแท่งตามเพศ */
        #genderSalesChart {
            max-width: 600px; /* ปรับขนาดของกราฟตามความเหมาะสม */
            margin-left: 20px auto; /* จัดกราฟไปที่กลาง */
        }

        /* เพิ่มสไตล์กราฟแท่งจำนวนผู้ใช้ */
        #genderChart {
            max-width: 600px; /* ปรับขนาดของกราฟตามความเหมาะสม */
            margin-left: 20px auto; /* จัดกราฟไปที่กลาง */
        }
        .sales-summary {
            text-align: center; 
            margin-right: 30px;
            padding-top: 20px;
        }
        .sales-item {
            display: inline-block;
            margin: 0 10px;
            text-align: left; 
        }
        /* เพิ่มสไตล์ปุ่มแสดงรายได้ */
        input[type="submit"] {
            background-color: #212121; /* เปลี่ยนสีเป็นสีแดง */
            color: #FFF; /* เปลี่ยนสีข้อความเป็นสีขาว */
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease; /* เพิ่มการเปลี่ยนสีเมื่อ hover */
        }

        input[type="submit"]:hover {
            background-color: rgba(255, 99, 132, 1); /* เปลี่ยนสีเป็นสีแดงเข้มเมื่อ hover */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-center">
    <a class="navbar-brand" href="#">Scinema</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="Dash.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="DashActor.php">Actors</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="DashDirector.php">Directors </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="DashGenre.php">Genre</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="room_status.php">Movie</a>
            </li>
        </ul>
    </div>
    <div class="dropdown" style="margin-right: 20px;">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<i class="fas fa-user icon" style="margin-right: 10px;"></i>' . $_SESSION['user_name'];
            } else {
                echo 'Login';
            }
            ?>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <?php
        if (isset($_SESSION['user_id'])) {
            echo '<a class="dropdown-item" href="logoutContrl.php">Logout</a>';
            echo '<a class="dropdown-item" href="edit_pass_control.php">Change Credent</a>';
        }
        ?>
    </div>
    </div>
</nav>
<body>
    <div class="container">
        <h1>Dashboard - ข้อมูลยอดขาย</h1>
        <div class="date-filter">
            
            <form method="post">
                <label for="start-date">วันที่เริ่มต้น:</label>
                <input type="date" id="start-date" name="start_date">

                <label for="end-date">วันที่สิ้นสุด:</label>
                <input type="date" id="end-date" name="end_date">

                <input type="submit" value="แสดงรายได้">
            </form>
            <?php
            // ตรวจสอบว่ามีการส่งค่าวันที่เริ่มต้นและสิ้นสุดมาหรือไม่
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $start_date = $_POST["start_date"];
                $end_date = $_POST["end_date"];

                // แสดงข้อความเลือกวันที่
                echo "<p>เลือกวันที่: " . $start_date . " ถึง " . $end_date . "</p>";
            } else {
                // ถ้ายังไม่ได้เลือกวันที่ ให้แสดงข้อความเลือกวันที่
                echo "<p>กรุณาเลือกวันที่เริ่มต้นและสิ้นสุด</p>";
            }
            ?>
        </div>
        
        <div class="sales-summary">
            <h2>ยอดขายต่อ ประเภท ของหนัง</h2>
            <div class="sales-item">
                <span>ยอดขายรวม:</span>
                <span class="total-sales">
                    <?php
                        // เชื่อมต่อกับฐานข้อมูล
                        $host = 'localhost';
                        $dbname = 'Scinema';
                        $username = 'root';
                        $password = 'root';
    
                        $conn = new mysqli($host, $username, $password, $dbname);
    
                        
                        if ($conn->connect_error) {
                            die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
                        }
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $start_date = $_POST["start_date"];
                            $end_date = $_POST["end_date"];

                            // คำสั่ง SQL เพื่อดึงข้อมูลยอดขายรวมในช่วงวันที่ที่ผู้ใช้เลือก
                            $sql_total_sales = "SELECT SUM(TotalPrice) AS total_sales FROM bookings WHERE BookingDate BETWEEN '$start_date' AND '$end_date'";
                            $result_total_sales = $conn->query($sql_total_sales);
                            $row_total_sales = $result_total_sales->fetch_assoc();
                            echo $row_total_sales['total_sales'];

                        } else {
                            echo "กรุณาเลือกวันที่เริ่มต้นและสิ้นสุด";
                        }
                        // คำสั่ง SQL เพื่อดึงข้อมูลยอดขายรวม
                    ?> Bath
                </span>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <canvas id="salesChart" width="400" height="200"></canvas>

        <?php
            $sql = "SELECT MovieID FROM bookings";
            $result = $conn->query($sql);
            $sql_genre_sales = "SELECT g.Genre, SUM(b.TotalPrice) AS total_sales
                                FROM bookings b
                                INNER JOIN movie m ON b.MovieID = m.MovieID
                                INNER JOIN genre g ON m.GenreID = g.GenreID
                                GROUP BY g.Genre";

            $result_genre_sales = $conn->query($sql_genre_sales);

            $genres = array();
            $genreSalesData = array();

            if ($result_genre_sales->num_rows > 0) {
                while($row = $result_genre_sales->fetch_assoc()) {
                    $genres[] = $row["Genre"];
                    $genreSalesData[] = $row["total_sales"];
                }
            }
        ?>

        <!-- สร้าง canvas element สำหรับแสดงกราฟแท่ง -->
        <canvas id="genreSalesChart"></canvas>

        <script>
            <?php
                // คำสั่ง SQL เพื่อดึงข้อมูลยอดขายแต่ละประเภทหนัง ในช่วงวันที่ที่ผู้ใช้เลือก
                $sql_genre_sales = "SELECT g.Genre, SUM(b.TotalPrice) AS total_sales
                                    FROM bookings b
                                    INNER JOIN movie m ON b.MovieID = m.MovieID
                                    INNER JOIN genre g ON m.GenreID = g.GenreID
                                    WHERE b.BookingDate BETWEEN '$start_date' AND '$end_date'
                                    GROUP BY g.Genre";

                $result_genre_sales = $conn->query($sql_genre_sales);

                $genres = array();
                $genreSalesData = array();

                if ($result_genre_sales->num_rows > 0) {
                    while($row = $result_genre_sales->fetch_assoc()) {
                        $genres[] = $row["Genre"];
                        $genreSalesData[] = $row["total_sales"];
                    }
                }
            ?>

            //สร้างกราฟแท่ง
            var ctxGenre = document.getElementById('genreSalesChart').getContext('2d');
            var genreSalesChart = new Chart(ctxGenre, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($genres); ?>,
                    datasets: [{
                        label: 'ยอดขายตามประเภทหนัง (บาท)',
                        data: <?php echo json_encode($genreSalesData); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        </script>

        <div class="sales-summary">
            <h2>ยอดขายต่อ เรื่อง</h2>
        <canvas id="movieSalesChart" width="400" height="200"></canvas>
   
        <script>
            // คำสั่ง SQL เพื่อดึงข้อมูลยอดขายแต่ละหนัง
            <?php
                $sql_movie_sales = "SELECT Namemovie, SUM(TotalPrice) AS total_sales
                    FROM bookings
                    WHERE BookingDate BETWEEN '$start_date' AND '$end_date'
                    GROUP BY Namemovie";
                $result_movie_sales = $conn->query($sql_movie_sales);

                $movieNames = array();
                $movieSalesData = array();

                if ($result_movie_sales->num_rows > 0) {
                    while($row = $result_movie_sales->fetch_assoc()) {
                        $movieNames[] = $row["Namemovie"];
                        $movieSalesData[] = $row["total_sales"];
                    }
                }
            ?>

            // สร้างกราฟวงกลม
            var ctxMovie = document.getElementById('movieSalesChart').getContext('2d');
            var movieSalesChart = new Chart(ctxMovie, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($movieNames); ?>,
                    datasets: [{
                        label: 'ยอดขาย (บาท)',
                        data: <?php echo json_encode($movieSalesData); ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        </script>
    

            <table class="sales-item">
                <thead>
                    <tr>
                        <th>หนัง</th>
                        <th>ยอดขาย (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // คำสั่ง SQL เพื่อดึงข้อมูลยอดขายแต่ละรายการ
                    $sql_individual_sales = "SELECT Namemovie, SUM(TotalPrice) AS total_sales
                                            FROM bookings
                                            WHERE BookingDate BETWEEN '$start_date' AND '$end_date'
                                            GROUP BY Namemovie
                                            ORDER BY total_sales DESC";
                                                                    
                    $result_individual_sales = $conn->query($sql_individual_sales);

                    if ($result_individual_sales->num_rows > 0) {
                        // แสดงรายการยอดขายแต่ละรายการ
                        while ($row = $result_individual_sales->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["Namemovie"] . "</td>";
                            echo "<td>" . $row["total_sales"] . " Bath</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>ไม่พบข้อมูลยอดขายแบบเจาะจง</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <?php
        // โค้ด SQL สำหรับดึงข้อมูลยอดขายตามเพศ
        $sql_gender_sales = "SELECT u.Gender, SUM(b.TotalPrice) AS total_sales
                            FROM bookings b
                            INNER JOIN users u ON b.UsersID = u.UsersID
                            WHERE b.BookingDate BETWEEN '$start_date' AND '$end_date'
                            GROUP BY u.Gender
                            ORDER BY total_sales";

        $result_gender_sales = $conn->query($sql_gender_sales);

        $genders = array();
        $genderSalesData = array();

        if ($result_gender_sales->num_rows > 0) {
            while($row = $result_gender_sales->fetch_assoc()) {
                $genders[] = $row["Gender"];
                $genderSalesData[] = $row["total_sales"];
            }
        }
        ?>
        

        <div class="sales-summary" id="genreSalesSection">
            <h2>ยอดขายต่อ เพศ ของผู้ใช้</h2>
        </div>
        <canvas id="genderSalesChart" width="400" height="200"></canvas>
        <!-- โค้ดสร้างกราฟแท่งตามเพศ -->
        <script>
            var ctxGender = document.getElementById('genderSalesChart').getContext('2d');
            var genderSalesChart = new Chart(ctxGender, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($genders); ?>,
                    datasets: [{
                        label: 'ยอดขายตามเพศ (บาท)',
                        data: <?php echo json_encode($genderSalesData); ?>,
                        backgroundColor: [
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)'
                                ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        </script>

        <div class="sales-summary" id="genreSalesSection">
            <h2>จำนวนของผู้ใช้</h2>
        </div>
        <canvas id="genderChart" width="400" height="200"></canvas>
        
            <script>
                <?php
                        // คิวรี่ข้อมูล gender จากตาราง users
                        $sql = "SELECT gender, COUNT(*) AS count FROM users GROUP BY gender";
                        $result = $conn->query($sql);
                
                        // สร้างข้อมูลสำหรับกราฟ
                        $labels = array();
                        $data = array();
                
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $labels[] = $row['gender'];
                                $data[] = $row['count'];
                            }
                        }
                    ?>
                
                    // สร้างกราฟแท่ง
                    var ctx = document.getElementById('genderChart').getContext('2d');
                    var genderChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($labels); ?>,
                            datasets: [{
                                
                                label: 'จำนวนผู้ใช้',
                                data: <?php echo json_encode($data); ?>,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        stepSize: 1,
                                        beginAtZero: true,
                                        // ไม่ต้องระบุ max หรือปล่อยให้กราฟปรับตามข้อมูล
                                    }
                                }]
                            }
                        }
                    });
                </script>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

