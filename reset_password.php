<?php 
session_start();
require_once 'includes/db.php'; // Ensure the correct path to the database connection

$message = '';

// Retrieve token from URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Prepare query to check if the token is valid
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the token is found
    if ($user = $result->fetch_assoc()) {
        // If the form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Ensure the new password matches confirmation
            if ($new_password === $confirm_password) {
                // Hash the new password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password and remove the token if successful
                $update_stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE user_id = ?");
                $update_stmt->bind_param("si", $new_password_hash, $user['user_id']);
                
                // Execute query and check if successful
                if ($update_stmt->execute()) {
                    $message = "Your password has been successfully reset. You can now log in.";
                    header("Location: index.php"); // Redirect to the main page after successful reset
                    exit();
                } else {
                    $message = "Error updating password.";
                }
            } else {
                $message = "The passwords do not match.";
            }
        }
    } else {
        // Token is invalid or not found in the database
        $message = "Invalid token. Please request a new password reset link.";
    }
} else {
    // Token was not provided in the URL
    $message = "No token provided. Please request a new password reset link.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Tailwind CSS Configuration for Custom Colors
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FFA500', // Orange color for buttons
                        darkGray: '#1c1c1c',
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
    <script>
        function validatePasswords() {
            var newPass = document.getElementById('new_password');
            var confirmPass = document.getElementById('confirm_password');
            if (newPass.value !== confirmPass.value) {
                alert('Passwords do not match.');
                return false;
            }
            return true;
        }
    </script>
</head>
<body class="gradient-bg h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-gray-800 form-overlay shadow-lg rounded-lg px-8 pt-6 pb-8 mb-4">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-primary">Reset Password</h2>
        </div>
        <?php if ($message): ?>
            <p class="text-center text-red-500"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" action="" onsubmit="return validatePasswords();" class="space-y-6">
            <div class="mb-4">
                <label class="block text-gray-200 text-sm font-bold mb-2" for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-300 bg-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary"
                    placeholder="Enter new password">
            </div>
            <div class="mb-4">
                <label class="block text-gray-200 text-sm font-bold mb-2" for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-300 bg-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary"
                    placeholder="Confirm new password">
            </div>
            <div class="flex items-center justify-center">
                <button type="submit"
                    class="bg-primary hover:bg-orange-600 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-200 ease-in-out">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</body>
</html>