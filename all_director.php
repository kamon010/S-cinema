<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET CHARACTER SET utf8");

    // ดึงข้อมูลของผู้กำกับจากฐานข้อมูล
    $sql = "SELECT * FROM Director";
    $stmt = $conn->query($sql);
    $directors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// ตรวจสอบว่ามีการลบข้อมูลผู้กำกับ
if(isset($_GET['delete'])) {
    // ตรวจสอบค่า id ของผู้กำกับที่ต้องการลบ
    $director_id = $_GET['delete'];

    // สร้างคำสั่ง SQL สำหรับลบข้อมูลผู้กำกับ
    $sql_delete = "DELETE FROM Director WHERE DirectorID = :director_id";

    // เตรียมและประมวลผลคำสั่ง SQL
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindParam(':director_id', $director_id);
    $stmt_delete->execute();

    // หลังจากลบข้อมูลเสร็จแล้ว ให้ Redirect กลับไปยังหน้า all_director.php
    header("Location: all_director.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <title>Director Information</title>
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
            font-size: 16px;
            font-weight: bold;
            color: #333333;
            margin: 0 auto;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            transform: scale(0.985);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            color: #333333;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #382628;
            color: white;
        }
        .director-btn {
            padding: 8px 16px;
            background-color: #AF39A8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .edit-btn {
            background: linear-gradient(135deg, #382628, #73373E);
            padding: 8px 16px;
            background-color: #382628;
            font-size: 16px;
            font-weight: bold;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .edit-btn:hover {
            background-color: linear-gradient(135deg, #73373E ,#382628);
        }

        .director img {
            width: 100px;
            height: 100px;
            object-fit: cover; /* ปรับขนาดรูปให้พอดีกับขนาดของ div */
            border-radius: 8px; /* ใช้สี่เหลี่ยมแทนวงกลม */
        }
        .director{
            width: 100px;
            height: 100px;
            overflow: hidden; /* ซ่อนส่วนที่เกินของรูป */
            margin: 0 auto; /* ตำแหน่งตรงกลาง */
        }
        .director-name {
            text-align: center;
            margin-top: 10px;
        }

        .btn-container {
            text-align: center;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #382628, #73373E);
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #F0368D;
        }
        .dropdown-menu {
            left: auto;
            right: 0;
        }
        .delete-btn {
        background: linear-gradient(135deg, #382628, #73373E);
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        color: #fff;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .delete-btn:hover {
        background-color: #F0368D;
    }
    </style>
    </style>
</head>
<body>
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
        <h2>Directors Information</h2>
        <a href="add_director.php" class="btn btn-secondary">Add New Director</a><br><br>
        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            <?php foreach ($directors as $director): ?>
                <tr>
                    <td class="director"><img src="<?php echo $director['ImageURL']; ?>" alt="Director Image"></td>
                    <td><?php echo $director['NameDirector']; ?></td>
                    <!-- ส่วนของ HTML -->
                    <td class="btn-container">
                        <a href="edit_director.php?id=<?php echo $director['DirectorID']; ?>" class="edit-btn">Edit</a>
                    </td>
                    <td class="btn-container">
                        <a href="?delete=<?php echo $director['DirectorID']; ?>" class="delete-btn">Delete</a>
                    </td>

                </tr>
            <?php endforeach; ?>
        </table>
    
        
    
    <script>
        // ฟังก์ชันที่จะทำงานเมื่อเกิดการโหลดหน้าเว็บ
        window.addEventListener('DOMContentLoaded', (event) => {
            document.querySelector(".btn-add").style.display = "inline-block"; // แสดงปุ่มเมื่อโหลดหน้าเว็บเสร็จสมบูรณ์
        });

        // ฟังก์ชันที่จะทำงานเมื่อเกิดการเลื่อนหน้าจอ
        window.onscroll = function() {scrollFunction()};

        function scrollFunction() {
            var btnAdd = document.querySelector(".btn-add");
            // ถ้าเลื่อนหน้าจอลงมาต่ำกว่า 20px และปุ่มไม่แสดงอยู่
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                if (btnAdd.style.display !== "none") {
                    btnAdd.style.display = "none"; // ซ่อนปุ่ม
                }
            } else { // ถ้าเลื่อนหน้าจอขึ้นมาบนสุดและปุ่มไม่แสดงอยู่
                if (btnAdd.style.display === "none") {
                    btnAdd.style.display = "inline-block"; // แสดงปุ่ม
                }
            }
        }

    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
