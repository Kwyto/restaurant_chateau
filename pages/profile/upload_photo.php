<?php
include '../../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_photo'];
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['error'] = "Invalid file type. Please upload a JPG, PNG or GIF file.";
        header("Location: settings.php");
        exit();
    }
    
    if ($file['size'] > $max_size) {
        $_SESSION['error'] = "File is too large. Maximum size is 5MB.";
        header("Location: settings.php");
        exit();
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profiles/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Update database with new photo path - use web-accessible path
        $relative_path = '../uploads/profiles/' . $filename;
        $query = "UPDATE users SET profile_photo = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $relative_path, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Profile photo updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating profile photo in database.";
        }
    } else {
        $_SESSION['error'] = "Error uploading file.";
    }
}

header("Location: settings.php");
exit();