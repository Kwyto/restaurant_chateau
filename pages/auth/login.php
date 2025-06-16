<?php 
include '../../includes/config.php';
include '../../includes/header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}
?>

<main class="min-h-screen bg-black text-white flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-8 border border-gold p-12">
        <div class="text-center">
            <h2 class="text-3xl font-serif font-bold">Welcome Back</h2>
            <p class="mt-2 text-sm text-gray-400">Please sign in to your account</p>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
        </div>
        <?php unset($_SESSION['error']); endif; ?>
        
        <form class="mt-8 space-y-6" action="authenticate.php" method="POST">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium">Email address</label>
                    <input id="email" name="email" type="email" required class="mt-1 block w-full bg-black border-b border-gold py-2 focus:outline-none focus:ring-gold focus:border-gold">
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
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-gold focus:ring-gold border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm">Remember me</label>
                </div>
                
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-gold text-gold hover:bg-gold hover:text-black transition duration-300">
                    Sign in
                </button>
            </div>
        </form>
        
        <div class="text-center text-sm">
            <span class="text-gray-400">Don't have an account?</span>
            <a href="signup.php" class="font-medium text-gold hover:text-gold"> Sign up</a>
        </div>
    </div>
</main>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>

