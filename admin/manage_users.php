<?php
session_start();
require '../includes/db.php';

// Cek apakah user adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Proses delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Hapus registrasi terkait user
    $conn->query("DELETE FROM registrations WHERE user_id = '$user_id'");

    // Hapus user
    $conn->query("DELETE FROM users WHERE user_id = '$user_id'");

    header("Location: manage_users.php");
    exit();
}

// Ambil semua user
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | JoyFent</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/joyfent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background-color: #f9fafb;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
        }

        thead {
            background-color: #1f2937;
            color: white;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

 

        .delete-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #f87171;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .delete-button:hover {
            background-color: #ef4444;
        }

        .delete-button i {
            width: 16px;
            height: 16px;
        }

        .modal {
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar_admin.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Manage Users</h1>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo ucfirst($user['role']); ?></td>
                            <td>
                                <form method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this user?');" 
                                      class="inline-block">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <button type="submit" name="delete_user" class="delete-button">
                                        <i data-feather="trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        feather.replace(); // Initialize Feather Icons
    </script>
</body>
</html>
