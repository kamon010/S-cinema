<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);

// ตรวจสอบว่ามีข้อผิดพลาดในการเชื่อมต่อหรือไม่
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}
if (!isset($_SESSION['user_id'])) {
    header("Location: loginContrl.php");
    exit();
}

// ตรวจสอบว่ามีการส่งค่า ID ของโปรโมชั่นที่ต้องการลบหรือไม่
if (isset($_GET['id'])) {
    $promotion_id = $_GET['id'];

    // เตรียมคำสั่ง SQL เพื่อลบข้อมูลโปรโมชั่น
    $sql = "DELETE FROM promotion WHERE PromotionID=$promotion_id";

    if ($conn->query($sql) === TRUE) {
        // ไม่ต้องแสดงข้อความเมื่อลบข้อมูลสำเร็จ
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูล: " . $conn->error;
    }
}

// คำสั่ง SQL เพื่อดึงข้อมูล PosterID และ Poster (ภาพ) จากตาราง poster_promotion
$sql = "SELECT PromotionID, PromotionName, StartDate, EndDate, DiscountAmount FROM promotion";

// ส่งคำสั่ง SQL ไปยังฐานข้อมูล
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotion Management</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    
    <style>

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
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: bold;
            color: #333333;
            background-color: #382628;
            background-size: cover;
            background-position: center;
            backdrop-filter: blur(10px);
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);            
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 95%; /* ปรับความกว้างของ container เพื่อขยายฟอร์มให้ใหญ่ขึ้น */
            margin: 0 auto; /* จัดกึ่งกลางฟอร์มในหน้าจอ */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-size: 24px; /* ปรับขนาดของตัวหัวเรื่อง */
        }

        form {
            display: flex;
            flex-direction: column;
            width: 100%;
            
        }

        label {
            margin-bottom: 5px;
            color: #555;
            font-size: 16px; /* ปรับขนาดของตัวอักษร label */
        }

        input[type="text"],
        input[type="date"],
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="submit"],
        .btn-cancel {
            background: linear-gradient(135deg, #382628, #73373E);
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            border: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        input[type="submit"]:hover,
        .btn-cancel:hover {
            background-color: #23A7C8;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #382628, #73373E);
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            border: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-container {
            text-align: center;
        }

        .btn-secondary:hover {
            background-color: #F0368D;
        }

        .dropdown-menu {
            left: auto;
            right: 0;
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
                <a class="nav-link" href="ShowMovie.php">Movie</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all_actors.php">Actors</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="all_director.php">Directors </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="promotion_more.php">Promotion</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="room_status.php">Room status</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="poster_more.php">Poster</a>
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
    <div class="container">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    <h2>Promotion Management</h2>
        <div class="text-left mb-3">
            <a href="promotion_add.php" class="btn btn-secondary">Add Promotion</a>
        </div>
        <?php
        // ตรวจสอบว่ามีข้อมูลที่ได้รับหรือไม่
        if ($result->num_rows > 0) {
            // แสดงข้อมูลทั้งหมดในตาราง promotion
            echo "<table class='table'>
                    <thead class='thead-dark'>
                        <tr>
                            <th>Promotion Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Discount Amount</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$row["PromotionName"]."</td>
                        <td>".$row["StartDate"]."</td>
                        <td>".$row["EndDate"]."</td>
                        <td>".$row["DiscountAmount"]."</td>
                        <td><a href='promotion_edit.php?id=".$row["PromotionID"]."' class='btn btn-secondary btn-sm'>Edit</a></td>

                        <td><a href='?id=".$row["PromotionID"]."' class='btn btn-secondary btn-sm'>Delete</a></td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='text-danger'>ไม่พบข้อมูลในตาราง promotion</p>";
        }

        // ปิดการเชื่อมต่อฐานข้อมูล
        $conn->close();
        ?>
        </form>
    </div>
    
</body>
</html>
