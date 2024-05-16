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


// ดึง user_id จาก session
$user_id = $_SESSION['user_id'];

// คำสั่ง SQL เพื่อดึงข้อมูลผู้ใช้
$sql_user = "SELECT * FROM Users WHERE UsersID = $user_id";
$result_user = $conn->query($sql_user);

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    // ดึง Point จากผู้ใช้
    $points = $user['Point'];
} else {
    // ไม่พบข้อมูลผู้ใช้
    // ให้ทำการ handle ตามที่เหมาะสม
    // เช่น redirect หน้าเว็บไปที่หน้า login หรือทำการ logout
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่ามีการส่ง MovieID และ user_id มาจาก homepage.php หรือไม่
if (isset($_GET['user_id']) && isset($_GET['movie_id'])) {
    // ดึง MovieID และ user_id จาก parameter ที่ส่งมา
    $user_id = $_GET['user_id'];
    $movie_id = $_GET['movie_id'];

    // URL หน้าก่อนหน้า
    $previous_page_url = "homepage.php?user_id=" . $user_id;

    // เข้าถึงค่า Point จากผู้ใช้
    $points = $user['Point'];
}

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

    // คำสั่ง SQL เพื่อดึงข้อมูลภาพยนตร์
    $sql_movie_details = "SELECT * FROM Movie WHERE MovieID = $movie_id";
    $result_movie_details = $conn->query($sql_movie_details);

    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if ($result_movie_details->num_rows > 0) {
        $row_movie_details = $result_movie_details->fetch_assoc();


?>

        <!DOCTYPE html>
        <html lang="th">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
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

                body,
                html {
                    overflow-x: hidden;
                    /* ป้องกันการเลื่อนหน้าเว็บไปซ้าย-ขวา */
                }

                nav {
                    background-color: #212121;
                    padding-top: 30px;
                    /* เพิ่ม padding ด้านบน */
                    padding-bottom: 25px;
                    /* เพิ่ม padding ด้านล่าง */
                    text-align: center;
                }

                nav a {
                    color: #fff;
                    text-decoration: none;
                    padding: 10px;
                    margin: 0 10px;
                }

                section {
                    padding: 20px;
                    text-align: center;
                }

                #scrollUp {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background-color: #fe006a;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    display: none;
                }

                footer {
                    background-color: #212121;
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
                    /* ปรับขนาดของ margin-right ให้โปสเตอร์ไปทางขวา */
                    margin-left: 40px;
                    /* เพิ่ม margin-left เพื่อเลื่อนโปสเตอร์ไปทางขวา */
                }

                .movie-details-text {
                    flex: 1;
                    text-align: left;
                }

                /* เพิ่มสไตล์ในการแสดงวิดีโอ YouTube */
                .youtube-video iframe {
                    position: relative;
                    text-align: left;
                    width: 100%;
                    height: 40%;
                    /* ปรับเป็น 100% ของความสูง */
                }

                .youtube-video {
                    position: relative;
                    width: 100%;
                    height: 0;
                    padding-bottom: 56.25%;
                    float: none;
                    margin: 0 auto;
                    margin-left: 370px;
                    /* เพิ่ม margin-left ให้ชิดไปทางขวา */
                }

                .youtube-video iframe {
                    position: absolute;
                    text-align: left;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                }

                .director-container {
                    text-align: center;
                    margin-top: 20px;
                    margin-left: -560px;
                    /* ปรับตำแหน่งไปทางซ้ายหน่อย */
                    display: flex;
                    justify-content: center;
                    /* แกนตั้งตรงกลาง */
                    flex-wrap: wrap;
                }

                .director-container,
                .actors-container {
                    text-align: center;
                    margin-top: 20px;
                    /* ลดระยะห่างของ container กับข้างบน */
                    display: flex;
                    justify-content: center;
                    /* แกนตั้งตรงกลาง */
                    flex-wrap: wrap;
                }

                .director,
                .actor {
                    text-align: center;
                    margin: 10px;
                }

                .director img,
                .actor img {
                    max-width: 100px;
                    /* ปรับให้ความกว้างสูงสุดไม่เกิน 100px */
                    width: 100%;
                    /* ทำให้ความกว้างเท่ากับขนาดที่กำหนด (ไม่เกิน 100px) */
                    height: 150px;
                    /* กำหนดความสูงให้เป็นค่าคงที่ เช่น 100px */
                    margin-left: 5px;
                    margin-top: 10px;
                    object-fit: cover;
                }


                .btn-book {
                    display: inline-block;
                    padding: 10px 30px;
                    background-color: #fe006a;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background-color 0.3s ease;
                    margin-top: 550px;
                }

                .btn-more {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #fe006a;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background-color 0.3s ease;
                    margin-top: 30px;
                    margin-left: -1020px;
                    /* ปรับตำแหน่งไปทางซ้ายหน่อย */
                }

                .btn-book:hover {
                    background-color: #ffd132;
                }

                /* เพิ่มสไตล์สำหรับหนังที่คล้ายกัน */
                h2 {
                    text-align: left;
                    /* จัดข้อความชิดซ้าย */
                    margin-left: 0;
                    /* เอาข้อความมาชิดซ้ายสุด */
                }

                .similar-movies {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                    margin-top: 30px;
                }

                .similar-movies {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: flex-start;
                    /* เปลี่ยนจาก center เป็น flex-start เพื่อให้ชิดด้านซ้าย */
                    margin-top: 30px;
                }

                .similar-movie {
                    margin: 10px;
                    text-align: center;
                    flex: 0 0 auto;
                    /* อนุญาตให้ความกว้างของ .similar-movie เปลี่ยนได้ตามขนาดของเนื้อหา */
                }

                .similar-movie p {
                    margin-top: 5px;
                }

                .similar-movie img {
                    width: 150px;
                    height: auto;
                }

                .similar-movie:last-child {
                    margin-right: 10px;
                    /* เพิ่ม margin-right ให้กับ .similar-movie สุดท้ายเพื่อป้องกันการขยับซ้าย */
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
                    top: 20px;
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
            <div class="email-container">
                <?php echo $email_link; ?>
            </div>

            <section>
                <h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Movie details</h2>
                <div class="movie-details">
                    <div class="movie-poster">
                        <img src="<?php echo $row_movie_details['Poster']; ?>" alt="<?php echo $row_movie_details['NameMovie']; ?>" style="width: 100%;">
                    </div>
                    <div class="movie-details-text">
                        <h3><?php echo $row_movie_details['NameMovie']; ?></h3>
                        <p>Release date : <?php echo $row_movie_details['ReleaseDate']; ?></p>
                        <p>Price : <?php echo $row_movie_details['price']; ?> Baht</p>
                        <p>Duration : <?php echo $row_movie_details['Duration']; ?></p>
                        <?php
                        // ดึงชื่อประเภทของหนังที่กำลังแสดง
                        $genre_id = $row_movie_details['GenreID'];
                        $sql_genre = "SELECT Genre FROM Genre WHERE GenreID = $genre_id";
                        $result_genre = $conn->query($sql_genre);
                        if ($result_genre->num_rows > 0) {
                            $row_genre = $result_genre->fetch_assoc();
                            echo "<p>Genre : " . $row_genre['Genre'] . "</p>";
                        } else {
                            echo "<p>Genre not available</p>";
                        }
                        ?>

                        <div class="director-container">
                            <?php
                            $director_id = $row_movie_details['DirectorID'];
                            $sql_director = "SELECT Director.NameDirector ,Director.ImageURL FROM Director WHERE Director.DirectorID = $director_id";
                            $result_director = $conn->query($sql_director);

                            if ($result_director->num_rows > 0) {
                                while ($row_director = $result_director->fetch_assoc()) {
                                    echo '<div class="director">';
                                    echo '<img src="' . $row_director['ImageURL'] . '" alt="' . $row_director['NameDirector'] . '">';
                                    echo '<p>Director :' . $row_director['NameDirector'] . '</p>';
                                    echo '</div>';
                                }
                            } else {
                                echo "<p>ไม่มีข้อมูลผู้กำกับ</p>";
                            }

                            // ดึงข้อมูล Actor
                            $sql_actors = "SELECT Actors.ActorsID, Actors.NameActor, Actors.ImageURL
                                FROM Movie_Actors
                                JOIN Actors ON Movie_Actors.ActorsID = Actors.ActorsID
                                WHERE Movie_Actors.MovieID = $movie_id";

                            $result_actors = $conn->query($sql_actors);

                            // ตรวจสอบว่ามีข้อมูลหรือไม่
                            if ($result_actors->num_rows > 0) {
                                while ($row_actor = $result_actors->fetch_assoc()) {
                                    echo '<div class="actor">';
                                    echo '<img src="' . $row_actor['ImageURL'] . '" alt="' . $row_actor['NameActor'] . '">';
                                    echo '<p>Actor :' . $row_actor['NameActor'] . '</p>';
                                    echo '</div>';
                                }
                                echo "</p>";
                            } else {
                                echo "<p>ไม่มีข้อมูลนักแสดง</p>";
                            }
                            ?>
                        </div>

                    </div>
                </div>

                <?php
                $current_date = date("Y-m-d");

                if ($row_movie_details['ReleaseDate'] <= $current_date && ($row_movie_details['LeavingDate'] == null || $row_movie_details['LeavingDate'] >= $current_date)) {
                    echo '<a class="btn-more" href="showetime.php?movie_id=' . $row_movie_details['MovieID'] . '">Press to reserve</a>';
                } else {
                    echo '<a class="btn-more">Unable to reserve at this time</a>';
                }
                ?>

                <!-- แสดงตัวอย่างหนังผ่าน YouTube -->
                <?php
                $youtube_video_url = $row_movie_details['LinkVDO'];
                $video_id = getYouTubeVideoId($youtube_video_url);

                if ($video_id) {
                    echo '<div class="youtube-video">';
                    echo '<iframe src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen style="width: 70%; height: 600px;"></iframe>'; // ปรับความสูงตามต้องการ
                    echo '</div>';
                }
                ?>

                <!-- แสดงหนังที่คล้ายกัน -->
                <h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Similar movies</h2>
                <div class="similar-movies">
                    <?php
                    $current_month = date("m"); // เดือนปัจจุบัน
                    $current_year = date("Y"); // ปีปัจจุบัน

                    $similar_movies_sql = "SELECT * FROM Movie 
                                WHERE GenreID = $genre_id 
                                AND MovieID != $movie_id 
                                AND MONTH(ReleaseDate) = '$current_month' 
                                AND YEAR(ReleaseDate) = '$current_year' 
                                AND (LeavingDate IS NULL OR LeavingDate >= CURDATE()) 
                                ORDER BY RAND() LIMIT 10";

                    $similar_movies_result = $conn->query($similar_movies_sql);

                    if ($similar_movies_result->num_rows > 0) {
                        while ($similar_movie_row = $similar_movies_result->fetch_assoc()) {
                            echo '<div class="similar-movie">';
                            echo '<a href="moviemore.php?user_id=' . $user_id . '&movie_id=' . $similar_movie_row['MovieID'] . '">';
                            echo '<img src="' . $similar_movie_row['Poster'] . '" alt="' . $similar_movie_row['NameMovie'] . '">';
                            echo '</a>';
                            echo '<p>' . $similar_movie_row['NameMovie'] . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No similar movies found</p>";
                    }
                    ?>
                </div>
            </section>

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
    } else {
        echo "ไม่พบข้อมูลภาพยนตร์";
    }
} else {
    echo "ไม่ได้ระบุ MovieID";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();

// ฟังก์ชั่นสำหรับดึง Video ID ของ YouTube จาก URL
function getYouTubeVideoId($url)
{
    $video_id = false;
    $url_components = parse_url($url);
    if (isset($url_components['query'])) {
        parse_str($url_components['query'], $params);
        if (isset($params['v'])) {
            $video_id = $params['v'];
        } else if (isset($params['vi'])) {
            $video_id = $params['vi'];
        }
    }
    return $video_id;
}
?>