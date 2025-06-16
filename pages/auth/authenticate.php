<?php
include('../../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_gender'] = $user['gender'];
            $_SESSION['user_created_at'] = $user['created_at'];
            
            // Handle remember me functionality
            if (isset($_POST['remember-me'])) {
                try {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
                    
                    // Store token in database
                    $update_query = "UPDATE users SET remember_token = ?, last_login = CURRENT_TIMESTAMP WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $update_query);
                    mysqli_stmt_bind_param($stmt, "si", $token, $user['id']);
                    mysqli_stmt_execute($stmt);
                } catch (Exception $e) {
                    // Log error but continue - remember me is not critical
                    error_log("Remember me token update failed: " . $e->getMessage());
                }
            }
            
            // Redirect to index page
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard/y");
            } else {
                header("Location: ../../index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>