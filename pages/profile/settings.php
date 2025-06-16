<?php 
include '../../includes/config.php';
include '../../includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch current user data
$user_id = $_SESSION['user_id'];
$query_fetch_user = "SELECT * FROM users WHERE id = ?";
$stmt_fetch_user = mysqli_prepare($conn, $query_fetch_user);
mysqli_stmt_bind_param($stmt_fetch_user, "i", $user_id);
mysqli_stmt_execute($stmt_fetch_user);
$result = mysqli_stmt_get_result($stmt_fetch_user);
$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //
    // --- UPDATE PERSONAL INFORMATION ---
    //
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    
    $update_query = "UPDATE users SET 
        first_name = ?, last_name = ?, email = ?, gender = ?, phone = ?, address = ?, date_of_birth = ?, updated_at = CURRENT_TIMESTAMP
        WHERE id = ?";
        
    $stmt_update_user = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt_update_user, "sssssssi", 
        $first_name, $last_name, $email, $gender, $phone, $address, $date_of_birth, $user_id
    );
    
    if (mysqli_stmt_execute($stmt_update_user)) {
        // PERBAIKAN LOGIKA: Blok 'Refresh user data' yang error dihapus.
        // Data user akan di-refresh saat halaman dimuat ulang.
        $user['first_name'] = $first_name;
        $user['last_name'] = $last_name;
        // ... (dan seterusnya untuk data lain yang diubah)
        
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
        
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
    
    //
    // --- UPDATE PASSWORD ---
    //
    if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
        // Gunakan data $user yang diambil di awal untuk verifikasi password
        if (password_verify($_POST['current_password'], $user['password'])) {
            $new_password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            
            $pass_update_query = "UPDATE users SET password = ? WHERE id = ?";
            $pass_stmt = mysqli_prepare($conn, $pass_update_query);
            mysqli_stmt_bind_param($pass_stmt, "si", $new_password_hash, $user_id);
            
            if (mysqli_stmt_execute($pass_stmt)) {
                $success_message = ($success_message ?? '') . " Password updated successfully!";
            } else {
                $error_message = ($error_message ?? '') . " Error updating password.";
            }
        } else {
            $error_message = "Current password is incorrect.";
        }
    }
}
?>

<main class="min-h-screen bg-black text-white">
    <div class="max-w-7xl mx-auto py-12 px-4">
        <?php include 'komponen/profile.php'; ?>
        
        <?php include 'komponen/navigasi.php'; ?>
        
        <div class="border border-gray-800 p-8">
            <h2 class="text-2xl font-serif font-bold mb-6">Account Settings</h2>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-8">
                <?php if (isset($success_message)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo $success_message; ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo $error_message; ?></span>
                    </div>
                <?php endif; ?>

                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-medium mb-4">Personal Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium mb-1">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold" required>
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium mb-1">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Gender</label>
                                <div class="flex gap-4">
                                    <div class="flex items-center">
                                        <input type="radio" id="male" name="gender" value="male" <?php echo (($user['gender'] ?? '') == 'male') ? 'checked' : ''; ?> class="h-4 w-4 text-gold focus:ring-gold border-gray-800">
                                        <label for="male" class="ml-2">Mr.</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" id="female" name="gender" value="female" <?php echo (($user['gender'] ?? '') == 'female') ? 'checked' : ''; ?> class="h-4 w-4 text-gold focus:ring-gold border-gray-800">
                                        <label for="female" class="ml-2">Mrs.</label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium mb-1">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold">
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium mb-4">Contact Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold" required>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium mb-1">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold">
                            </div>
                            <div>
                                <label for="address" class="block text-sm font-medium mb-1">Address</label>
                                <textarea id="address" name="address" rows="3" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-4">Change Password</h3>
                    <div class="space-y-4 max-w-md">
                        <div>
                            <label for="current_password" class="block text-sm font-medium mb-1">Current Password</label>
                            <div class="relative">
                                <input type="password" id="current_password" name="current_password" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold pr-10">
                                <button type="button" onclick="togglePassword('current_password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gold">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-medium mb-1">New Password</label>
                             <div class="relative">
                                <input type="password" id="new_password" name="new_password" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold pr-10">
                                 <button type="button" onclick="togglePassword('new_password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gold">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="button" onclick="window.location.href='index.php'" class="px-6 py-3 border border-gray-800 text-white hover:border-gold hover:text-gold transition duration-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gold text-black hover:bg-gold-dark transition duration-300">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const iconEye = `
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>`;
    const iconEyeSlash = `
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
        </svg>`;

    if (input.type === 'password') {
        input.type = 'text';
        button.innerHTML = iconEyeSlash;
    } else {
        input.type = 'password';
        button.innerHTML = iconEye;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>