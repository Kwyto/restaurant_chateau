<?php
include '../../includes/config.php';

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Tambahkan ini: Ambil data user untuk mendapatkan path foto lama
$user_id = $_SESSION['user_id'];
$query_get_user = "SELECT profile_photo FROM users WHERE id = ?";
$stmt_get_user = mysqli_prepare($conn, $query_get_user);
mysqli_stmt_bind_param($stmt_get_user, "i", $user_id);
mysqli_stmt_execute($stmt_get_user);
$result_user = mysqli_stmt_get_result($stmt_get_user);
$user = mysqli_fetch_assoc($result_user);
// Selesai penambahan

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
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
    
    // PERBAIKAN: Path upload yang benar relatif terhadap root proyek
    
    $project_root = dirname(__DIR__, 2); 
    $upload_dir = $project_root . '/uploads/profiles/';
    
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
    
    // Coba upload file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Path untuk web (disimpan di database)
        $relative_path = '/restaurant_chateau/uploads/profiles/' . $filename;
        
        // Update database
        $query = "UPDATE users SET profile_photo = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $relative_path, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Hapus foto lama jika ada dan bukan foto default
            if (!empty($user['profile_photo'])) {
                $old_file_path = $project_root . str_replace('/restaurant_chateau', '', $user['profile_photo']);
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
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
        $_SESSION['error'] = "Gagal upload file. Cek permission folder. Error: " . ($error['message'] ?? 'Unknown error');
        error_log("Upload error: " . print_r($error, true));
    }
}

header("Location: settings.php");
exit();