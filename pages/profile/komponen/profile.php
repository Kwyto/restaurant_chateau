<?php
// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Get total reservations count
$count_query = "SELECT COUNT(*) as total FROM reservations WHERE user_id = ?";
$count_stmt = mysqli_prepare($conn, $count_query);
mysqli_stmt_bind_param($count_stmt, "i", $user_id);
mysqli_stmt_execute($count_stmt);
$total_reservations = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];


// MODIFIKASI: Menggunakan null coalescing operator (??) untuk mencegah error jika membership_level adalah NULL.
$membership_badge = '<span class="px-4 py-1 bg-gold/10 text-gold rounded-full text-sm">' . 
    ucfirst(htmlspecialchars($user['membership_level'] ?? '')) . ' Member</span>';

?>
        <div class="flex flex-col md:flex-row items-start md:items-center gap-8 mb-12">
            <div class="relative group">
                <div class="w-32 h-32 rounded-full bg-gray-800 flex items-center justify-center overflow-hidden border-2 border-gold cursor-pointer transition-all duration-300 hover:opacity-75"
                     onclick="document.getElementById('profilePhotoInput').click()">
                    <?php if (!empty($user['profile_photo'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_photo'] ?? ''); ?>" 
                             alt="Profile Photo" 
                             class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.src='../../assets/images/default-profile.png';">
                    <?php else: ?>
                        <span class="text-4xl"><?php echo strtoupper(substr($user['first_name'] ?? '', 0, 1)); ?></span>
                    <?php endif; ?>
                    
                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full">
                        <div class="text-white text-sm text-center px-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Upload Photo
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h1 class="text-4xl font-serif font-bold mb-2">
                    <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>
                </h1>
                <p class="text-gray-400 mb-4">Member since <?php echo !empty($user['created_at']) ? date('F Y', strtotime($user['created_at'])) : 'N/A'; ?></p>
                <div class="flex flex-wrap gap-4">
                    <?php echo $membership_badge; ?>
                    <span class="px-4 py-1 bg-gray-800 rounded-full text-sm">
                        <?php echo $total_reservations; ?> Reservations
                    </span>
                </div>
            </div>
        </div>
        <form id="profilePhotoForm" action="upload_photo.php" method="POST" enctype="multipart/form-data" class="hidden">
            <input type="file" name="profile_photo" id="profilePhotoInput" accept="image/*" class="hidden">
        </form>
        
        <script>
            document.getElementById('profilePhotoInput').addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    document.getElementById('profilePhotoForm').submit();
                }
            });
        </script>