<?php
session_start();
require_once __DIR__ . '/../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, password_hash FROM admin_users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            // Password is correct, create session
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_email'] = $email;
            
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid password.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "No user found with that email address.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
