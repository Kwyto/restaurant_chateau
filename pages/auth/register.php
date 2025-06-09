<?php
include '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = "Passwords do not match";
        header("Location: signup.php");
        exit();
    }

    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);
    
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['register_error'] = "Email address is already registered";
        header("Location: signup.php");
        exit();
    }

    // If email doesn't exist, proceed with registration
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (first_name, last_name, email, gender, password) 
              VALUES (?, ?, ?, ?, ?)";
              
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $email, $gender, $hashed_password);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['register_error'] = "Registration failed. Please try again.";
        header("Location: signup.php");
        exit();
    }
} else {
    header("Location: signup.php");
    exit();
}
?>