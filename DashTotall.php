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

// คำสั่ง SQL เพื่อดึงข้อมูล Namemovie จากตาราง Bookings
$sql_movies = "SELECT DISTINCT Namemovie FROM Bookings";
$result_movies = $conn->query($sql_movies);

// ตรวจสอบว่ามีการเลือกหนังหรือไม่
if (isset($_POST['movies']) && !empty($_POST['movies'])) {
    $selected_movie = $_POST['movies'];

    // คำสั่ง SQL เพื่อดึงยอดขายของหนังที่เลือก
    $sql_total_sales = "SELECT DATE(BookingDate) AS BookingDay, SUM(TotalPrice) AS TotalSales FROM Bookings WHERE Namemovie = '$selected_movie' GROUP BY DATE(BookingDate) ORDER BY DATE(BookingDate)";
    $result_total_sales = $conn->query($sql_total_sales);

    // สร้างตัวแปรเก็บข้อมูลของยอดขายแต่ละวัน
    $sales_data = array();
    while ($row_total_sales = $result_total_sales->fetch_assoc()) {
        $booking_day = $row_total_sales['BookingDay'];
        $total_sales = $row_total_sales['TotalSales'];
        $sales_data[$booking_day] = $total_sales;
    }

    // คำสั่ง SQL เพื่อดึงยอดขายรวมของหนังที่เลือก
    $sql_total_sales_all = "SELECT SUM(TotalPrice) AS TotalSales FROM Bookings WHERE Namemovie = '$selected_movie'";
    $result_total_sales_all = $conn->query($sql_total_sales_all);

    // ตรวจสอบว่ามีผลลัพธ์ที่ถูกต้องหรือไม่
    if ($result_total_sales_all && $result_total_sales_all->num_rows > 0) {
        // ดึงข้อมูลยอดขายรวม
        $row_total_sales_all = $result_total_sales_all->fetch_assoc();
        $total_sales_all = $row_total_sales_all['TotalSales'];
    } else {
        // หากไม่พบข้อมูลยอดขายรวม
        $total_sales_all = 0;
    }

    // คำสั่ง SQL เพื่อดึงข้อมูลเพศและรายได้รวมของแต่ละเพศสำหรับหนังที่เลือก
    $sql_gender_sales_selected = "SELECT u.Gender, SUM(b.TotalPrice) AS TotalSales
                    FROM Bookings b
                    INNER JOIN Users u ON b.UsersID = u.UsersID
                    WHERE b.Namemovie = '$selected_movie'
                    GROUP BY u.Gender";
    $result_gender_sales_selected = $conn->query($sql_gender_sales_selected);

    // สร้างตัวแปรเก็บข้อมูลของเพศและรายได้รวมของแต่ละเพศ
    $gender_sales_data_selected = array();
    while ($row_gender_sales_selected = $result_gender_sales_selected->fetch_assoc()) {
        $gender_selected = $row_gender_sales_selected['Gender'];
        $total_sales_selected = $row_gender_sales_selected['TotalSales'];
        $gender_sales_data_selected[$gender_selected] = $total_sales_selected;
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Booking.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #FFFFFF;
            color: #000000;
        }
        
        .chart-container {
            display: flex;
            justify-content: space-between;
        }

        .chart {
            flex: 1;
            /* คำสั่งเพิ่มขนาดกราฟให้เต็มพื้นที่ที่สามารถ */
            margin-right: 20px;
            height: 300px;
            /* ระบุความสูงของกราฟเท่ากับความสูงของกราฟ Sales by Gender */
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
        echo '<select name="movies" id="movies" class="nav-link" style="background-color: #505050; color: #FFFFFF; width: 150px;">';
        echo '<option value="" disabled selected>Movie</option>'; // เพิ่มตัวเลือกแรก
        while ($row_movies = $result_movies->fetch_assoc()) {
            echo '<option value="' . $row_movies['Namemovie'] . '">' . $row_movies['Namemovie'] . '</option>';
        }
        echo '</select>';
        echo '<input type="submit" value="Show" class="nav-link" style="background-color: #D594C5; color: #FFFFFF;">';
        echo '</form>';
        ?>
        
    </nav>
    <div class="email-container" style="margin-top: -20px;">
        <h4>Welcome <?php echo $user_name; ?>!</h4>
    </div>

    <?php if (isset($selected_movie) && isset($sales_data)) { ?>
        <div class="chart-container">
            <div>
                <h4>Total Sales of <?php echo $selected_movie; ?></h4>
                <p>Total Sales: $<?php echo number_format($total_sales_all, 2); ?></p>
                <canvas id="salesChart" class="chart" width="400" height="200"></canvas>
            </div>
            <div>
                <h4>Sales by Gender for <?php echo $selected_movie; ?></h4>
                <p>
                    <span style='color: rgba(255, 99, 132, 1);'>Female : <?php echo isset($gender_sales_data_selected['Female']) ? '' . number_format($gender_sales_data_selected['Female'], 2) : '$0.00'; ?> Baht</span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span style='color: rgba(255, 206, 86, 1);'>LGBTQ+ : <?php echo isset($gender_sales_data_selected['LGBTQ+']) ? '' . number_format($gender_sales_data_selected['LGBTQ+'], 2) : '$0.00'; ?> Baht</span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span style='color: rgba(89, 210, 190, 1);'>Male : <?php echo isset($gender_sales_data_selected['Male']) ? '' . number_format($gender_sales_data_selected['Male'], 2) : '$0.00'; ?> Baht</span>
                </p>
                <canvas id="genderSalesChartSelected" class="chart" width="400" height="200"></canvas>
            </div>
        </div>

        <script>
            var sales_data = <?php echo json_encode($sales_data); ?>;
            var labels = Object.keys(sales_data);
            var data = Object.values(sales_data);

            var ctx = document.getElementById('salesChart').getContext('2d');
            var gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, '#FF3187');
            gradient.addColorStop(1, '#670015');

            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Sales',
                        data: data,
                        backgroundColor: gradient,
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

            var gender_sales_data_selected = <?php echo json_encode($gender_sales_data_selected); ?>;
            var gender_labels_selected = Object.keys(gender_sales_data_selected);
            var gender_data_selected = Object.values(gender_sales_data_selected);

            var gender_ctx_selected = document.getElementById('genderSalesChartSelected').getContext('2d');
            var gender_chart_selected = new Chart(gender_ctx_selected, {
                type: 'line',
                data: {
                    labels: gender_labels_selected,
                    datasets: [{
                        label: 'Sales by Gender',
                        data: gender_data_selected,
                        pointBackgroundColor: [
                            'rgba(255, 99, 132, 1)', // Female
                            'rgba(255, 206, 86, 1)', // LGBTQ+
                            'rgba(89, 210, 190, 1)', // Male
                        ],
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
                                        label += ': $';
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
        </script>
    <?php } ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            menuContainer.style.display = 'none';

            userIcon.addEventListener('click', function() {
                menuContainer.style.display = (menuContainer.style.display === 'none' || menuContainer.style.display === '') ? 'block' : 'none';
            });

            document.getElementById("logOut").addEventListener("click", function() {
                window.location.href = "logoutContrl.php";
            });

            document.getElementById("editInfo").addEventListener("click", function() {
                var user_id = "<?php echo $user_id['UsersID']; ?>";
                window.location.href = "edit_profile.php?user_id=" + user_id;
            });
        });
    </script>
</body>

</html>