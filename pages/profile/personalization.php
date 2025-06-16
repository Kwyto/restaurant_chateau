<?php 
include '../../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Handle form submission first, before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $favorite_cuisines = isset($_POST['cuisines']) ? implode(',', $_POST['cuisines']) : '';
    $preferred_seating = $_POST['seating'] ?? '';
    $dietary_restrictions = isset($_POST['dietary']) ? implode(',', $_POST['dietary']) : '';
    // $special_requests = $_POST['special_requests'] ?? ''; // DIHAPUS
    
    // Update user preferences
    // Menghapus baris 'special_requests = ?' dari query
    $update_query = "UPDATE users SET 
                        favorite_cuisines = ?,
                        preferred_seating = ?,
                        dietary_restrictions = ?
                        WHERE id = ?";
                        
    $stmt = mysqli_prepare($conn, $update_query);
    // Menghapus 's' untuk special_requests dan variabelnya dari bind_param
    mysqli_stmt_bind_param($stmt, "sssi", 
        $favorite_cuisines, 
        $preferred_seating,
        $dietary_restrictions,
        $_SESSION['user_id']
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Preferences updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating preferences.";
    }
    
    header("Location: personalization.php");
    exit();
}

// Fetch user preferences
$user_id = $_SESSION['user_id'];
// Menghapus 'special_requests' dari query SELECT
$query = "SELECT favorite_cuisines, preferred_seating, dietary_restrictions 
          FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Convert stored preferences to arrays
$saved_cuisines = !empty($user['favorite_cuisines']) ? explode(',', $user['favorite_cuisines']) : [];
$saved_dietary = !empty($user['dietary_restrictions']) ? explode(',', $user['dietary_restrictions']) : [];

// Now include header after all redirects
include '../../includes/header.php';
?>

<main class="min-h-screen bg-black text-white">
    <div class="max-w-7xl mx-auto py-12 px-4">
        <?php include 'komponen/profile.php'; ?>
        
        <?php include 'komponen/navigasi.php'; ?>
        
        <div class="border border-gray-800 p-8">
            <h2 class="text-2xl font-serif font-bold mb-6">Dining Preferences</h2>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-lg font-medium mb-4">Favorite Cuisines</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <?php
                            $cuisine_options = ['French', 'Italian', 'Japanese', 'Steakhouse'];
                            foreach ($cuisine_options as $cuisine): 
                            ?>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="cuisine-<?php echo strtolower($cuisine); ?>" 
                                       name="cuisines[]" 
                                       value="<?php echo $cuisine; ?>"
                                       <?php echo in_array($cuisine, $saved_cuisines) ? 'checked' : ''; ?>
                                       class="h-4 w-4 text-gold focus:ring-gold border-gray-800 rounded">
                                <label for="cuisine-<?php echo strtolower($cuisine); ?>" class="ml-2"><?php echo $cuisine; ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium mb-4">Preferred Seating</h3>
                        <div class="space-y-3">
                            <?php
                            $seating_options = [
                                'window' => 'Window',
                                'private' => 'Private Booth',
                                'main' => 'Main Dining Room'
                            ];
                            foreach ($seating_options as $value => $label): 
                            ?>
                            <div class="flex items-center">
                                <input type="radio" 
                                       id="seating-<?php echo $value; ?>" 
                                       name="seating" 
                                       value="<?php echo $value; ?>"
                                       <?php echo ($user['preferred_seating'] === $value) ? 'checked' : ''; ?>
                                       class="h-4 w-4 text-gold focus:ring-gold border-gray-800">
                                <label for="seating-<?php echo $value; ?>" class="ml-2"><?php echo $label; ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-4">Dietary Restrictions</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php
                        $dietary_options = [
                            'vegetarian' => 'Vegetarian',
                            'vegan' => 'Vegan',
                            'gluten' => 'Gluten-Free',
                            'dairy' => 'Dairy-Free',
                            'nuts' => 'Nut Allergy',
                            'shellfish' => 'Shellfish Allergy'
                        ];
                        foreach ($dietary_options as $value => $label): 
                        ?>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="diet-<?php echo $value; ?>" 
                                   name="dietary[]" 
                                   value="<?php echo $value; ?>"
                                   <?php echo in_array($value, $saved_dietary) ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-gold focus:ring-gold border-gray-800 rounded">
                            <label for="diet-<?php echo $value; ?>" class="ml-2"><?php echo $label; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <button type="submit" class="px-8 py-3 bg-gold text-black hover:bg-transparent hover:text-gold hover:border hover:border-gold transition duration-300">
                    Save Preferences
                </button>
            </form>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>