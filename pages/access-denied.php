<?php
// access-denied.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-red-600 p-4 text-white">
                <div class="flex items-center justify-center">
                    <i class="fas fa-ban text-4xl mr-3"></i>
                    <h1 class="text-2xl font-bold">Access Denied</h1>
                </div>
            </div>
            
            <div class="p-6 text-center">
                <div class="mb-6">
                    <i class="fas fa-lock text-6xl text-red-500 mb-4"></i>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Unauthorized Access</h2>
                    <p class="text-gray-600">You don't have permission to access this page.</p>
                    <p class="text-gray-600 mt-2">Please contact administrator if you believe this is an error.</p>
                </div>
                
                <div class="flex flex-col space-y-3">
                    <a href="../" 
                       class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition duration-200 text-center">
                       <i class="fas fa-home mr-2"></i>Return to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>