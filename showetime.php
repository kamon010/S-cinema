<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>S-Cinima - Naresuann</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* CSS styles for showtime.php */
        body {
            background-color: #000;
            color: #FFF;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .movie-image {
            width: 300px;
            margin: 20px auto;
            display: block;
        }

        .buttons-container {
            text-align: center;
            margin-top: 10px;
        }

        .date-button,
        .time-button {
            width: 100px;
            /* ปรับขนาดตามที่คุณต้องการ */
            height: 50px;
            /* ปรับขนาดตามที่คุณต้องการ */
            border-radius: 5px;
            /* เพิ่มขอบมนเข้าไป */
            background-color: #FF3187;
            color: #FFF;
            margin: 10px;
            font-size: 18px;
            line-height: 50px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
        }

        .highlighted {
            background-color: #8D003B;
            /* Highlight color as desired */
        }

        .date-button.inactive {
            background-color: #FF8EBD;
            /* Gray color */
            cursor: not-allowed;
            /* Change cursor to not-allowed */
        }
    </style>
</head>

<body>

    <!-- Movie image -->
    <?php
    session_start(); // Start session

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page if not logged in
        header("Location: login.php");
        exit();
    }

    // Fetch user details from the database or session
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
    }

    // Check if MovieID is provided in the URL
    if (isset($_GET['movie_id'])) {
        $movie_id = $_GET['movie_id'];
    }

    // URL หน้าก่อนหน้า
    $previous_page_url = "moviemore.php?user_id=" . $user_id . '&name=' . urlencode($user_name) . '&movie_id=' . $row_movie_details["MovieID"];

    $sql_movie_details = "SELECT * FROM Movie WHERE MovieID = $movie_id";

    // Database connection parameters
    // ติดต่อฐานข้อมูล
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

    // Fetch movie details from the database
    $sql_movie_details = "SELECT * FROM Movie WHERE MovieID = $movie_id";
    $result_movie_details = $conn->query($sql_movie_details);

    // Check if movie details are available
    if ($result_movie_details->num_rows == 0) {
        echo "Movie details not found";
        exit();
    }

    // Fetch movie details
    $row_movie_details = $result_movie_details->fetch_assoc();
    ?>

    <!-- Display movie image -->
    <img src="<?php echo $row_movie_details['Poster']; ?>" alt="<?php echo $row_movie_details['NameMovie']; ?>" class="movie-image">

    <!-- Date buttons -->
    <div class="buttons-container">
        <?php
        // Generate date buttons for the next 7 days
        // Generate date buttons for the next 7 days
        $start_date = date('Y-m-d');
        for ($i = 0; $i < 7; $i++) {
            $current_date = date('Y-m-d', strtotime("+$i days", strtotime($start_date)));
            $button_class = (strtotime($current_date) < strtotime('today')) ? 'inactive' : ''; // Check if the date is in the past
            echo '<a href="#" class="date-button ' . $button_class . '" onclick="highlightDate(event, this)">' . date('d-m-Y', strtotime($current_date)) . '</a>';
        }

        ?>
    </div>

    <!-- Time buttons -->
    <div class="buttons-container">
        <?php
        // Fetch time slots for the selected movie from the database
        $sql_time_slots = "SELECT DISTINCT Showtime FROM Showtimes WHERE MovieID = $movie_id";
        $result_time_slots = $conn->query($sql_time_slots);

        // Check if time slots are available
        if ($result_time_slots->num_rows > 0) {
            // Display time buttons for each time slot
            while ($row_time_slot = $result_time_slots->fetch_assoc()) {
                $time = $row_time_slot['Showtime'];
                echo '<a href="#" class="time-button" onclick="selectTime(\'' . $time . '\')">' . $time . '</a>';
            }
        } else {
            echo "No time slots available";
        }
        ?>
    </div>

    <script>
        // Function to check and disable past dates
        // Function to check and disable past dates
        function checkAndDisablePastDates() {
            // Get all date buttons
            var dateButtons = document.getElementsByClassName('date-button');

            // Get current date
            var currentDate = new Date();

            // Loop through date buttons
            for (var i = 0; i < dateButtons.length; i++) {
                // Extract date value from button text
                var dateValue = dateButtons[i].innerText.split('-').reverse().join('-');
                var selectedDate = new Date(dateValue);

                // Compare current date with the button date
                if (currentDate > selectedDate) {
                    // Disable the button and add a class to style it as inactive
                    dateButtons[i].disabled = true;
                    dateButtons[i].classList.add('inactive');
                }
            }
        }

        // Call the function on page load
        window.onload = checkAndDisablePastDates;

        // Function to handle date button click
        function highlightDate(event, clickedDate) {
            // Check if the button is disabled (past date)
            if (clickedDate.disabled) {
                alert('You cannot select a past date');
                event.preventDefault(); // Prevent default action if the button is disabled
                return;
            }

            // Remove 'highlighted' class from all elements with class 'date-button'
            var dateButtons = document.getElementsByClassName('date-button');
            for (var i = 0; i < dateButtons.length; i++) {
                dateButtons[i].classList.remove('highlighted');
            }

            // Add 'highlighted' class to the clicked element
            clickedDate.classList.add('highlighted');

            // Prevent default action of the link
            event.preventDefault();
        }

        // Function to select time and redirect to booking page with selected date and time
        function selectTime(time) {
            // Check if a date is selected
            var selectedDate = document.querySelector('.highlighted');
            if (!selectedDate) {
                alert('Please select a date before selecting a time');
                return;
            }

            // Extract selected date
            var selectedDateValue = selectedDate.innerText.split('/').reverse().join('-');

            // Redirect to booking page with selected date, time, user_id, and name
            window.location.href = 'booking.php?movie_id=<?php echo $movie_id; ?>&date=' + selectedDateValue + '&time=' + time + '&user_id=<?php echo $user_id; ?>&name=<?php echo urlencode($user_name); ?>';
        }
    </script>



</body>

</html>