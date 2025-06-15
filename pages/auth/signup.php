<?php 
include '../../includes/config.php'; 
include '../../includes/header.php'; 

// Check for registration errors in session
$error_message = '';
if (isset($_SESSION['register_error'])) {
    $error_message = $_SESSION['register_error'];
    unset($_SESSION['register_error']); // Clear the error after displaying
}
?>

<main class="min-h-screen bg-black text-white flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-8 border border-gold p-12">
        <div class="text-center">
            <h2 class="text-3xl font-serif font-bold">Create Your Account</h2>
            <p class="mt-2 text-sm text-gray-400">Join Us</p>
        </div>
        
        <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
        </div>
        <?php endif; ?>
        
        <form class="mt-8 space-y-6" action="register.php" method="POST">
            <div class="rounded-md shadow-sm space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium">First Name</label>
                        <input id="first_name" name="first_name" type="text" required class="mt-1 block w-full bg-black border-b border-gold py-2 focus:outline-none focus:ring-gold focus:border-gold">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium">Last Name</label>
                        <input id="last_name" name="last_name" type="text" required class="mt-1 block w-full bg-black border-b border-gold py-2 focus:outline-none focus:ring-gold focus:border-gold">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium">Email address</label>
                    <input id="email" name="email" type="email" required class="mt-1 block w-full bg-black border-b border-gold py-2 focus:outline-none focus:ring-gold focus:border-gold">
                </div>
                
                <div>
                    <label class="block text-sm font-medium">Gender</label>
                    <div class="mt-2 flex gap-4">
                        <div class="flex items-center">
                            <input id="male" name="gender" type="radio" value="male" class="h-4 w-4 text-gold focus:ring-gold border-gray-300">
                            <label for="male" class="ml-2 block text-sm">Mr.</label>
                        </div>
                        <div class="flex items-center">
                            <input id="female" name="gender" type="radio" value="female" class="h-4 w-4 text-gold focus:ring-gold border-gray-300">
                            <label for="female" class="ml-2 block text-sm">Mrs.</label>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required class="mt-1 block w-full bg-black border-b border-gold py-2 pr-10 focus:outline-none focus:ring-gold focus:border-gold">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gold">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium">Confirm Password</label>
                    <div class="relative">
                        <input id="confirm_password" name="confirm_password" type="password" required class="mt-1 block w-full bg-black border-b border-gold py-2 pr-10 focus:outline-none focus:ring-gold focus:border-gold">
                        <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gold">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 text-gold focus:ring-gold border-gray-300 rounded">
                <label for="terms" class="ml-2 block text-sm">I agree to the <a href="#" class="text-gold">terms and conditions</a></label>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-gold text-gold hover:bg-gold hover:text-black transition duration-300">
                    Create Account
                </button>
            </div>
        </form>
        
        <div class="text-center text-sm">
            <span class="text-gray-400">Already have an account?</span>
            <a href="login.php" class="font-medium text-gold hover:text-gold"> Sign in</a>
        </div>
    </div>
</main>



<script>
function togglePasswordVisibility(inputId, buttonId) {
    const input = document.getElementById(inputId);
    const button = document.getElementById(buttonId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.getElementById('togglePassword').addEventListener('click', function() {
    togglePasswordVisibility('password', 'togglePassword');
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
    togglePasswordVisibility('confirm_password', 'toggleConfirmPassword');
});
</script>