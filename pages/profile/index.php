<?php 
include '../../includes/config.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}


include '../../includes/header.php';
?>

<main class="min-h-screen bg-black text-white">
    <div class="max-w-7xl mx-auto py-12 px-4">
        <!-- Profile Header -->
        <?php include 'komponen/profile.php'; ?>
        
        <!-- Profile Navigation -->
        <?php include 'komponen/navigasi.php'; ?>
        
        <!-- Profile Content -->
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Basic Information -->
            <div class="border border-gray-800 p-8">
                <h2 class="text-2xl font-serif font-bold mb-6">Basic Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Full Name</label>
                        <p class="font-medium"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Email</label>
                        <p class="font-medium"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Gender</label>
                        <p class="font-medium"><?php echo $user['gender'] == 'male' ? 'Mr.' : 'Mrs.'; ?></p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Phone Number</label>
                        <p class="font-medium"><?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'Not set'; ?></p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Address</label>
                        <p class="font-medium"><?php echo $user['address'] ? nl2br(htmlspecialchars($user['address'])) : 'Not set'; ?></p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Date of Birth</label>
                        <p class="font-medium"><?php echo $user['date_of_birth'] ? date('F d, Y', strtotime($user['date_of_birth'])) : 'Not set'; ?></p>
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="border border-gray-800 p-8">
                <h2 class="text-2xl font-serif font-bold mb-6">Dining Preferences</h2>
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium mb-2">Favorite Cuisines</h3>
                        <?php if (!empty($user['favorite_cuisines'])): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach(explode(',', $user['favorite_cuisines']) as $cuisine): ?>
                                    <span class="px-3 py-1 bg-gray-800 rounded-full text-sm">
                                        <?php echo htmlspecialchars(ucfirst($cuisine)); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-400">No preferences set</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium mb-2">Preferred Seating</h3>
                        <?php if (!empty($user['preferred_seating'])): ?>
                            <p><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $user['preferred_seating']))); ?></p>
                        <?php else: ?>
                            <p class="text-gray-400">No preference set</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium mb-2">Dietary Restrictions</h3>
                        <?php if (!empty($user['dietary_restrictions'])): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach(explode(',', $user['dietary_restrictions']) as $diet): ?>
                                    <span class="px-3 py-1 bg-gray-800 rounded-full text-sm">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $diet))); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-400">No restrictions set</p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($user['special_requests'])): ?>
                    <div>
                        <h3 class="text-lg font-medium mb-2">Special Requests</h3>
                        <p><?php echo nl2br(htmlspecialchars($user['special_requests'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>