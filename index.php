<?php
session_start();
require 'includes/db.php';

$login_error = '';
$register_error = '';
$register_success = '';

// Login Process
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Retrieve user data from the database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Use password_verify to check the hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: user/index.php");
            }
            exit();
        } else {
            $login_error = "Invalid username or password.";
        }
    } else {
        $login_error = "Invalid username or password.";
    }
}

// Registration Process
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['reg_username']);
    $email = mysqli_real_escape_string($conn, $_POST['reg_email']);
    $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);

    // Check if username or email already exists
    $check_user = $conn->query("SELECT * FROM users WHERE username = '$username' OR email = '$email'");
    if ($check_user->num_rows > 0) {
        $register_error = "Username or email already exists.";
    } else {
        $conn->query("INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', 'user')");
        $register_success = "Registration successful! Please log in.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management - Login & Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Tailwind CSS Configuration for Custom Colors
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FFA500', // Bright Orange for buttons
                        darkGray: '#1c1c1c', // Dark gray for background
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

        /* Custom styles for the form container */
        .form-overlay {
            background-color: rgba(28, 28, 28, 0.85); /* Dark semi-transparent background */
        }
    </style>
</head>
<body class="gradient-bg h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-gray-800 form-overlay shadow-lg rounded-lg px-8 pt-6 pb-8 mb-4">
        <div class="mb-6 flex justify-between text-gray-300">
            <button id="login-tab" class="text-xl font-bold focus:outline-none" onclick="switchTab('login')">Login</button>
            <button id="register-tab" class="text-xl font-bold focus:outline-none" onclick="switchTab('register')">Register</button>
        </div>

        <!-- Login Form -->
        <form id="login-form" method="POST" class="space-y-6">
            <h2 class="text-3xl font-bold text-center mb-6 text-primary">Login</h2>
            <?php if ($login_error): ?>
                <p class="text-red-500 text-center"><?php echo $login_error; ?></p>
            <?php endif; ?>
            <div class="mb-4">
                <label class="block text-gray-200 text-sm font-bold mb-2" for="username">Username</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-300 bg-gray-700 leading-tight focus:outline-none focus:border-primary"
                       id="username" name="username" type="text" placeholder="Enter your username" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-200 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-300 bg-gray-700 leading-tight focus:outline-none focus:border-primary"
                       id="password" name="password" type="password" placeholder="Enter your password" required>
            </div>
            <div class="flex items-center justify-center">
                <button name="login" class="bg-primary hover:bg-orange-600 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-200 ease-in-out"
                        type="submit">
                    Log In
                </button>
            </div>
            <div class="text-center mt-4">
                <a href="forgot_password.php" class="text-sm text-gray-300 hover:text-white transition duration-200">Forgot Password?</a>
            </div>
        </form>

        <!-- Registration Form -->
        <form id="register-form" method="POST" class="space-y-6 hidden">
            <h2 class="text-3xl font-bold text-center mb-6 text-primary">Register</h2>
            <?php if ($register_error): ?>
                <p class="text-red-500 text-center"><?php echo $register_error; ?></p>
            <?php elseif ($register_success): ?>
                <p class="text-green-500 text-center"><?php echo $register_success; ?></p>
            <?php endif; ?>
            <div class="mb-4">
                <label class="block text-gray-200 text-sm font-bold mb-2" for="reg_username">Username</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-300 bg-gray-700 leading-tight focus:outline-none focus:border-primary"
                       id="reg_username" name="reg_username" type="text" placeholder="Enter a username" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-200 text-sm font-bold mb-2" for="reg_email">Email</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-300 bg-gray-700 leading-tight focus:outline-none focus:border-primary"
                       id="reg_email" name="reg_email" type="email" placeholder="Enter your email" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-200 text-sm font-bold mb-2" for="reg_password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-300 bg-gray-700 leading-tight focus:outline-none focus:border-primary"
                       id="reg_password" name="reg_password" type="password" placeholder="Enter a password" required>
            </div>
            <div class="flex items-center justify-center">
                <button name="register" class="bg-primary hover:bg-orange-600 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-200 ease-in-out"
                        type="submit">
                    Register
                </button>
            </div>
        </form>
    </div>

    <script>
        // Function to switch between login and register tabs
        function switchTab(tab) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const loginTab = document.getElementById('login-tab');
            const registerTab = document.getElementById('register-tab');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                loginTab.classList.add('border-b-2', 'border-primary');
                registerTab.classList.remove('border-b-2', 'border-primary');
            } else {
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');
                registerTab.classList.add('border-b-2', 'border-primary');
                loginTab.classList.remove('border-b-2', 'border-primary');
            }
        }
    </script>
</body>
</html>