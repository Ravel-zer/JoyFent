<?php
session_start();
require '../includes/db.php';

// Cek apakah user adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Proses hapus event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Hapus gambar event
    $stmt = $conn->prepare("SELECT image_url FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->bind_result($image_url);
    $stmt->fetch();
    $stmt->close();

    if (!empty($image_url) && file_exists("../admin/upload/" . $image_url)) {
        unlink("../admin/upload/" . $image_url);
    }

    // Hapus registrasi terkait event
    $stmt = $conn->prepare("DELETE FROM registrations WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();

    // Hapus event dari tabel events
    $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        $success_message = "Event deleted successfully!";
    } else {
        $error_message = "Failed to delete event.";
    }

    $stmt->close();
}

// Ambil daftar event
$events = $conn->query("
    SELECT events.*, 
           (SELECT COUNT(*) FROM registrations WHERE registrations.event_id = events.event_id) AS registrants_count 
    FROM events
");

if ($events === false) {
    die("Error: " . $conn->error); // Tangani jika query gagal
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | JoyFent</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/joyfent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background-color: #f3f4f6;
        }

        .card-header {
            background-color: #1f2937;
            color: white;
            padding: 1rem;
        }

        .action-buttons a, .action-buttons button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .action-buttons a:hover, .action-buttons button:hover {
            opacity: 0.9;
        }

        .table th {
            background-color: #1f2937;
            color: white;
        }

        .text-green-500 {
            color: #10B981;
        }

        .text-red-500 {
            color: #EF4444;
        }

        .modal {
            backdrop-filter: blur(5px);
        }

        .toast {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem;
            background-color: #10B981;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .toast.show {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }

        .toast.hide {
            opacity: 0;
            transform: translateY(-20px);
        }

        .toast-error {
            background-color: #EF4444;
        }
    </style>
</head>
<body>
    <?php include 'navbar_admin.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manage Events</h1>
            <a href="create_event.php" 
               class="bg-black hover:bg-gray-800 text-white flex items-center gap-2 px-4 py-2 rounded">
                <i data-feather="plus"></i> Create Event
            </a>
        </div>

        <table class="table w-full border-collapse rounded-lg overflow-hidden shadow-lg">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Event Name</th>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Registrants / Max Participants</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($event = $events->fetch_assoc()): ?>
                    <tr class="bg-white hover:bg-gray-100 border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($event['name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($event['date']); ?></td>
                        <td class="px-4 py-2">
                            <?php echo $event['registrants_count']; ?> / <?php echo htmlspecialchars($event['max_participants']); ?>
                        </td>
                        <td class="px-4 py-2">
                            <span class="<?php echo ($event['status'] === 'open') ? 'text-green-500' : 'text-red-500'; ?>">
                                <?php echo ucfirst($event['status']); ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 flex justify-center gap-2 action-buttons">
                            <a href="view_registrants.php?event_id=<?php echo $event['event_id']; ?>" 
                               class="bg-blue-500 text-white px-4 py-2 rounded">
                                <i data-feather="eye"></i> View
                            </a>
                            <a href="edit_event.php?event_id=<?php echo $event['event_id']; ?>" 
                               class="bg-green-500 text-white px-4 py-2 rounded">
                                <i data-feather="edit"></i> Edit
                            </a>
                            <button onclick="openDeleteModal(<?php echo $event['event_id']; ?>)" 
                                    class="bg-red-500 text-white px-4 py-2 rounded">
                                <i data-feather="trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="delete-modal" class="modal fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Are you sure?</h2>
            <p class="mb-4">This action cannot be undone. Do you want to proceed?</p>
            <div class="flex justify-end space-x-4">
                <button class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded" onclick="closeDeleteModal()">
                    Cancel
                </button>
                <form method="POST" id="delete-form">
                    <input type="hidden" name="event_id" id="event-id">
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        function openDeleteModal(eventId) {
            document.getElementById('event-id').value = eventId;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + (type === 'error' ? 'toast-error' : '');
            toast.classList.add('show');

            setTimeout(() => {
                toast.classList.add('hide');
                setTimeout(() => toast.classList.remove('show', 'hide'), 300);
            }, 3000);
        }

        <?php if (isset($success_message)): ?>
            showToast('<?php echo $success_message; ?>');
        <?php elseif (isset($error_message)): ?>
            showToast('<?php echo $error_message; ?>', 'error');
        <?php endif; ?>

        feather.replace();
    </script>
</body>
</html>
