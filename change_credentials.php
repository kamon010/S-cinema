<?php
session_start();

$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: loginContrl.php");
    exit();
}

if (isset($_POST['change_credentials'])) {
    $oldUsername = $_SESSION['user_name'];
    $oldPassword = $_POST['password'];
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];
    $userType = $_POST['user_type'];

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if the old password matches
        $sqlCheckUser = "SELECT * FROM Control WHERE NameControl = :username";
        $stmtCheckUser = $conn->prepare($sqlCheckUser);
        $stmtCheckUser->bindParam(':username', $oldUsername);
        $stmtCheckUser->execute();
        
        $user = $stmtCheckUser->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($oldPassword, $user['Password'])) {
            // Update the user credentials
            $sqlUpdateCredentials = "UPDATE Control SET NameControl = :new_username, Password = :new_password, Type = :user_type WHERE NameControl = :old_username";
            $stmtUpdateCredentials = $conn->prepare($sqlUpdateCredentials);
            $stmtUpdateCredentials->bindParam(':new_username', $newUsername);
            $stmtUpdateCredentials->bindParam(':new_password', password_hash($newPassword, PASSWORD_DEFAULT));
            $stmtUpdateCredentials->bindParam(':user_type', $userType);
            $stmtUpdateCredentials->bindParam(':old_username', $oldUsername);
            
            if ($stmtUpdateCredentials->execute()) {
                echo '<script>alert("Credentials updated successfully."); window.location.href = "loginContrl.php";</script>';
                exit;
            } else {
                echo '<script>alert("Failed to update credentials.");</script>';
            }
        } else {
            echo '<script>alert("Invalid username or password.");</script>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
}
?>
