<?php 
include '../includes/config.php';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'chateaulumiere27@gmail.com';
        $mail->Password = 'bdqy ihml ncly kmph';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($_POST['email'], $_POST['name']);
        $mail->addAddress('chateaulumiere27@gmail.com');
        $mail->addReplyTo($_POST['email'], $_POST['name']);

        $mail->isHTML(true);
        $mail->Subject = "Contact Form: {$_POST['subject']}";
        $mail->Body = "
            <div style='font-family: Arial, sans-serif;'>
                <h2>New Contact Message</h2>
                <p><strong>Name:</strong> {$_POST['name']}</p>
                <p><strong>Email:</strong> {$_POST['email']}</p>
                <p><strong>Message:</strong><br>{$_POST['message']}</p>
            </div>";

        $mail->send();
        $_SESSION['success'] = "Message sent successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Message could not be sent. Error: {$mail->ErrorInfo}";
    }

    header("Location: contact.php");
    exit();
}

include '../includes/header.php';
?>

<main class="min-h-screen bg-black text-white relative overflow-hidden">
    <!-- Luxury Background Effects -->
    <div class="hero-bg absolute inset-0"></div>
    <div class="diamond-overlay absolute inset-0"></div>
    <div class="particles absolute inset-0"></div>
    
    <div class="max-w-7xl mx-auto py-16 px-4 relative z-10">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4 gold-emboss">Get in Touch</h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">Experience the epitome of fine dining. Reach out to us for reservations or inquiries.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-black/50 backdrop-blur-sm border border-gray-800 p-8 rounded-lg">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mb-6 p-4 bg-green-100/10 border border-green-400 text-green-400 rounded">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mb-6 p-4 bg-red-100/10 border border-red-400 text-red-400 rounded">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-300">Name</label>
                        <input type="text" name="name" required
                               class="w-full bg-white border border-gray-800 rounded py-3 px-4 
                                      focus:ring-gold focus:border-gold transition-colors
                                      text-black placeholder-gray-500"
                               placeholder="Enter your name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-300">Email</label>
                        <input type="email" name="email" required
                               class="w-full bg-white border border-gray-800 rounded py-3 px-4 
                                      focus:ring-gold focus:border-gold transition-colors
                                      text-black placeholder-gray-500"
                               placeholder="Enter your email">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-300">Subject</label>
                        <input type="text" name="subject" required
                               class="w-full bg-white border border-gray-800 rounded py-3 px-4 
                                      focus:ring-gold focus:border-gold transition-colors
                                      text-black placeholder-gray-500"
                               placeholder="Enter subject">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-300">Message</label>
                        <textarea name="message" rows="5" required
                                  class="w-full bg-white border border-gray-800 rounded py-3 px-4 
                                         focus:ring-gold focus:border-gold transition-colors
                                         text-black placeholder-gray-500"
                                  placeholder="Enter your message"></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gold text-black py-3 px-6 rounded hover:bg-gold/90 transition-colors duration-300">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <div class="bg-black/50 backdrop-blur-sm border border-gray-800 p-8 rounded-lg">
                    <h3 class="text-xl font-serif font-bold mb-6 text-gold">Location & Hours</h3>
                    <div class="space-y-6 text-gray-400">
                        <div>
                            <h4 class="font-medium mb-2">Address</h4>
                            <p>123 Luxury Avenue<br>Fine Dining District<br>Beverly Hills, CA 90210</p>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Opening Hours</h4>
                            <p>Monday - Thursday: :5:30 PM - 10:30 PM<br>
                               Frinday - Saturday: 5:00 PM - 11:30 PM<br>
                               Sunday: 5:00 PM - 9:30 PM</p>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Contact</h4>
                            <p>Email: chateaulumiere27@gmail.com<br>
                               Phone: (+62) 895-1293-7862</p>
                        </div>
                    </div>
                </div>

                <div class="bg-black/50 backdrop-blur-sm border border-gray-800 p-8 rounded-lg">
                    <h3 class="text-xl font-serif font-bold mb-6 text-gold">Follow Us</h3>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-gold transition-colors">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gold transition-colors">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>