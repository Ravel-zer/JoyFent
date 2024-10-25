<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure the autoload from Composer is correctly included
$conn = require 'includes/db.php'; // Include your database connection settings

$message = '';
$message_type = ''; // Variable to track the type of message (success or error)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email = mysqli_real_escape_string($conn, $email);

        // Check if email exists in the database
        $query = $conn->query("SELECT * FROM users WHERE email = '{$email}'");
        if ($query && $query->num_rows > 0) {
            $token = bin2hex(random_bytes(50));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Update the database with the reset token and expiry
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
            $stmt->bind_param("sss", $token, $expiry, $email);
            $stmt->execute();

            // Setup PHPMailer
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jerdom17@gmail.com'; // Replace with your actual Gmail address
            $mail->Password = 'trep anuh okiy fytk'; // Replace with your actual App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('jerdom17@gmail.com', 'JoyFent - Make A joy and fun in event');
            $mail->addAddress($email); // Add recipient
            $reset_link = "joyfent.site/reset_password.php?token=$token";
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body    = "Click here to reset your password: <a href='$reset_link'>Reset Password</a>";

            try {
                $mail->send();
                $message = "Password reset link has been sent to your email.";
                $message_type = 'success'; // Set message type to success
            } catch (Exception $e) {
                $message = "Mailer Error: " . $mail->ErrorInfo;
                $message_type = 'error'; // Set message type to error
            }
        } else {
            $message = "Email not found.";
            $message_type = 'error';
        }
    } else {
        $message = "Invalid email format.";
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Tailwind CSS Configuration for Custom Colors
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FFA500', // Orange color for buttons
                        darkGray: '#1c1c1c',
                        success: '#28a745' // Green color for success messages
                    },
                },
            },
        };
    </script>
    <style>
        /* Custom styles for the gradient background */
        .gradient-bg {
            background: linear-gradient(to right, #1c1c1c, #FFA500);
        }

        /* Slightly dark overlay for the form container */
        .form-overlay {
            background-color: rgba(28, 28, 28, 0.85);
        }
    </style>
</head>
<body class="gradient-bg h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-gray-800 form-overlay shadow-lg rounded-lg px-8 pt-6 pb-8 mb-4">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-primary">Forgot Password</h2>
        </div>
        <?php if ($message): ?>
            <!-- Display message with conditional styling based on the message type -->
            <p class="text-center <?php echo $message_type === 'success' ? 'text-green-500' : 'text-red-500'; ?>">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>
        <form method="POST" action="forgot_password.php" class="space-y-6">
            <div class="mb-4">
                <label class="block text-gray-200 text-sm font-bold mb-2" for="email">Email Address</label>
                <input type="email" id="email" name="email" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-300 bg-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary"
                       placeholder="Enter your email">
            </div>
            <div class="flex items-center justify-center">
                <button type="submit" class="bg-primary hover:bg-orange-600 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-200 ease-in-out">
                    Send Reset Link
                </button>
            </div>
        </form>
        <!-- Login link positioned below the form -->
        <div class="text-center mt-4">
            <a href="index.php" class="text-sm text-gray-300 hover:text-primary transition duration-200">Login</a>
        </div>
    </div>
</body>
</html>