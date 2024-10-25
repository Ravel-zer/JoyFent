<?php
session_start();
require '../includes/db.php';

// Cek apakah user adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Proses hapus event setelah konfirmasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Ambil nama gambar untuk dihapus
    $stmt = $conn->prepare("SELECT image_url FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->bind_result($image_url);
    $stmt->fetch();
    $stmt->close();

    if (!empty($image_url) && file_exists("../admin/upload/" . $image_url)) {
        unlink("../admin/upload/" . $image_url);
    }

    // Hapus semua registrasi terkait dan event
    $conn->query("DELETE FROM registrations WHERE event_id = $event_id");
    if ($conn->query("DELETE FROM events WHERE event_id = $event_id")) {
        $success_message = "Event deleted successfully.";
    } else {
        $error_message = "Failed to delete event.";
    }
}

// Ambil semua event
$events = $conn->query("
    SELECT events.*, 
           (SELECT COUNT(*) FROM registrations WHERE registrations.event_id = events.event_id) AS registrants_count 
    FROM events
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Admin</title>
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
            color: #fff;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:hover {
            background-color: #f3f4f6;
        }

        .action-buttons a, .action-buttons button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .action-buttons a:hover, .action-buttons button:hover {
            opacity: 0.9;
        }

        .modal {
            backdrop-filter: blur(5px);
        }

        .modal-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .modal-buttons button {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'navbar_admin.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Manage Events</h1>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                <?php echo $success_message; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Registrants / Max Participants</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($event = $events->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $event['name']; ?></td>
                            <td><?php echo $event['date']; ?></td>
                            <td><?php echo $event['registrants_count']; ?> / <?php echo $event['max_participants']; ?></td>
                            <td><?php echo ucfirst($event['status']); ?></td>
                            <td class="action-buttons flex gap-2">
                                <a href="view_registrants.php?event_id=<?php echo $event['event_id']; ?>" 
                                   class="bg-blue-500 text-white rounded">
                                    <i data-feather="eye"></i> View
                                </a>
                                <a href="edit_event.php?event_id=<?php echo $event['event_id']; ?>" 
                                   class="bg-green-500 text-white rounded">
                                    <i data-feather="edit"></i> Edit
                                </a>
                                <button class="bg-red-500 text-white rounded" 
                                        onclick="openModal(<?php echo $event['event_id']; ?>)">
                                    <i data-feather="trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div id="delete-modal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="modal-container">
            <h2 class="text-xl font-bold mb-4">Are you sure?</h2>
            <p class="mb-4">Do you really want to delete this event? This process cannot be undone.</p>
            <div class="modal-buttons flex justify-center space-x-4">
                <button class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded" onclick="closeModal()">Cancel</button>
                <form method="POST" id="delete-form">
                    <input type="hidden" name="event_id" id="event-id">
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(eventId) {
            document.getElementById('event-id').value = eventId;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }

        feather.replace(); // Initialize Feather Icons
    </script>
</body>
</html>
