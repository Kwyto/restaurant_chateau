<?php
// Get current page name
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-12">
    <a href="index.php" 
       class="border <?php echo $current_page === 'index' ? 'border-gold text-gold' : 'border-gray-800'; ?> py-3 px-4 text-center hover:border-gold hover:text-gold transition duration-300">
        Profile
    </a>
    <a href="personalization.php" 
       class="border <?php echo $current_page === 'personalization' ? 'border-gold text-gold' : 'border-gray-800'; ?> py-3 px-4 text-center hover:border-gold hover:text-gold transition duration-300">
        personalization
    </a>
    <a href="history.php" 
       class="border <?php echo $current_page === 'history' ? 'border-gold text-gold' : 'border-gray-800'; ?> py-3 px-4 text-center hover:border-gold hover:text-gold transition duration-300">
        History
    </a>
    <a href="membership.php" 
       class="border <?php echo $current_page === 'membership' ? 'border-gold text-gold' : 'border-gray-800'; ?> py-3 px-4 text-center hover:border-gold hover:text-gold transition duration-300">
        Membership
    </a>
    <a href="coupons.php" 
       class="border <?php echo $current_page === 'coupons' ? 'border-gold text-gold' : 'border-gray-800'; ?> py-3 px-4 text-center hover:border-gold hover:text-gold transition duration-300">
        Coupons
    </a>
    <a href="settings.php" 
       class="border <?php echo $current_page === 'settings' ? 'border-gold text-gold' : 'border-gray-800'; ?> py-3 px-4 text-center hover:border-gold hover:text-gold transition duration-300">
        Settings
    </a>
</div>