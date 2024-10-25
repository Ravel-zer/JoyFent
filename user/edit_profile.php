<?php
session_start();
require '../includes/db.php';

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE user_id = '$user_id'")->fetch_assoc();
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? md5($_POST['password']) : $user['password'];

    // Proses Upload Foto Profil
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "../user/uploads/";
        $file_name = time() . '-' . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;

        // Hapus foto lama jika ada dan bukan default
        if ($user['profile_image'] != 'default-avatar.png') {
            $old_file = $target_dir . $user['profile_image'];
            if (file_exists($old_file)) {
                unlink($old_file); // Menghapus file lama
            }
        }

        // Pindahkan file baru
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $conn->query("UPDATE users SET profile_image = '$file_name' WHERE user_id = '$user_id'");
            $success_message = "Profile updated successfully.";
        } else {
            $error_message = "Failed to upload profile image.";
        }
    }

    // Update data user
    $conn->query("UPDATE users SET username = '$username', email = '$email', password = '$password' WHERE user_id = '$user_id'");

    // Refresh data user
    $user = $conn->query("SELECT * FROM users WHERE user_id = '$user_id'")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | JoyFent</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/joyfent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        .btn-save {
            background-color: #1a1a1a;
        }

        .btn-save:hover {
            background-color: #333333;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-4 py-12">
        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-8">
                <h1 class="text-3xl font-semibold text-gray-800 mb-6">Edit Profile</h1>

                <!-- Alert Box -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php elseif ($error_message): ?>
                    <div class="alert alert-error">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 font-medium">Username</label>
                        <input type="text" name="username" value="<?php echo $user['username']; ?>" 
                               class="w-full mt-2 border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Email</label>
                        <input type="email" name="email" value="<?php echo $user['email']; ?>" 
                               class="w-full mt-2 border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">New Password (optional)</label>
                        <input type="password" name="password" 
                               class="w-full mt-2 border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Profile Image</label>
                        <input type="file" name="profile_image" 
                               class="w-full mt-2 border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm">
                    </div>
                    <button type="submit" class="btn-save w-full text-white font-medium py-2 px-4 rounded mt-4 transition-all duration-300">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>

</html>
