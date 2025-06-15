<?php
include '../../includes/config.php';

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_photo'];
    
    // Validasi file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['error'] = "Hanya file JPG, PNG atau GIF yang diperbolehkan.";
        header("Location: settings.php");
        exit();
    }
    
    if ($file['size'] > $max_size) {
        $_SESSION['error'] = "Ukuran file maksimal 5MB.";
        header("Location: settings.php");
        exit();
    }
    
    // Path upload yang benar
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profiles/';
    
    // Buat folder jika belum ada
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $_SESSION['error'] = "Gagal membuat folder upload.";
            error_log("Failed to create directory: " . $upload_dir);
            header("Location: settings.php");
            exit();
        }
    }
    
    // Cek apakah folder bisa ditulisi
    if (!is_writable($upload_dir)) {
        $_SESSION['error'] = "Folder upload tidak bisa ditulisi. Periksa permission folder.";
        error_log("Directory not writable: " . $upload_dir);
        header("Location: settings.php");
        exit();
    }
    
    // Generate nama file unik
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Debug path
    error_log("Attempting to upload to: " . $filepath);
    
    // Coba upload file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Verifikasi file benar-benar ada
        if (!file_exists($filepath)) {
            $_SESSION['error'] = "File berhasil diupload tetapi tidak ditemukan di lokasi tujuan!";
            error_log("File not found after upload: " . $filepath);
            header("Location: settings.php");
            exit();
        }
        
        // Path untuk web
        $relative_path = '/uploads/profiles/' . $filename;
        
        // Update database
        $query = "UPDATE users SET profile_photo = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $relative_path, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Hapus foto lama jika ada
            if (!empty($user['profile_photo'])) {
                $old_file = $_SERVER['DOCUMENT_ROOT'] . $user['profile_photo'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            $_SESSION['success'] = "Foto profil berhasil diupdate!";
        } else {
            $_SESSION['error'] = "Gagal update database: " . mysqli_error($conn);
            // Hapus file yang sudah diupload jika gagal update database
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
    } else {
        $error = error_get_last();
        $_SESSION['error'] = "Gagal upload file. Error: " . $error['message'];
        error_log("Upload error: " . print_r($error, true));
    }
}

header("Location: settings.php");
exit();