<?php
// subscribe.php
require_once 'config/db_connect.php';

// Enable error reporting for mysqli to throw exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.html';

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            // Check if table exists, create if not
            $checkTable = "CREATE TABLE IF NOT EXISTS subscribers (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $conn->query($checkTable);

            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $stmt->bind_param("s", $email);
            
            $stmt->execute();
            echo "<script>alert('Thank you for subscribing to our newsletter!'); window.location.href = '$referer';</script>";
            
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Duplicate entry
                echo "<script>alert('You are already subscribed to our newsletter!'); window.location.href = '$referer';</script>";
            } else {
                echo "<script>alert('An error occurred. Please try again.'); window.location.href = '$referer';</script>";
            }
        } catch (Exception $e) {
            echo "<script>alert('An error occurred. Please try again.'); window.location.href = '$referer';</script>";
        }
    } else {
        echo "<script>alert('Invalid email format. Please try again.'); window.location.href = '$referer';</script>";
    }
} else {
    header("Location: index.html");
    exit();
}
$conn->close();
?>
