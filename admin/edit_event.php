<?php
session_start();
require '../includes/db.php';

// Cek apakah user yang login adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$event_id = $_GET['event_id'];
$event = $conn->query("SELECT * FROM events WHERE event_id = '$event_id'")->fetch_assoc();
$success_message = '';
$error_message = '';

// Proses update event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_event'])) {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];

    $sql = "UPDATE events 
            SET name = '$name', date = '$date', time = '$time', location = '$location', 
                description = '$description', max_participants = '$max_participants', status = '$status' 
            WHERE event_id = '$event_id'";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Event updated successfully!";
        $event = $conn->query("SELECT * FROM events WHERE event_id = '$event_id'")->fetch_assoc(); // Refresh data
    } else {
        $error_message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event | JoyFent</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/joyfent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        .form-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        .form-container h1 {
            color: #1f2937;
        }

        .form-container input, 
        .form-container textarea, 
        .form-container select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            margin-top: 8px;
        }

        .form-container button {
            width: 100%;
            background-color: #10b981;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            margin-top: 16px;
            transition: background-color 0.3s;
        }

        .form-container button:hover {
            background-color: #059669;
        }

        .alert {
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 8px;
            text-align: center;
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
<body>
    <?php include 'navbar_admin.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="form-container">
            <h1 class="text-3xl font-bold mb-4">Edit Event</h1>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block font-medium">Event Name</label>
                    <input type="text" name="name" value="<?php echo $event['name']; ?>" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Date</label>
                        <input type="date" name="date" value="<?php echo $event['date']; ?>" required>
                    </div>
                    <div>
                        <label class="block font-medium">Time</label>
                        <input type="time" name="time" value="<?php echo $event['time']; ?>" required>
                    </div>
                </div>

                <div>
                    <label class="block font-medium">Location</label>
                    <input type="text" name="location" value="<?php echo $event['location']; ?>" required>
                </div>

                <div>
                    <label class="block font-medium">Description</label>
                    <textarea name="description" rows="4" required><?php echo $event['description']; ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Max Participants</label>
                        <input type="number" name="max_participants" value="<?php echo $event['max_participants']; ?>" required>
                    </div>
                    <div>
                        <label class="block font-medium">Status</label>
                        <select name="status" required>
                            <option value="open" <?php if ($event['status'] == 'open') echo 'selected'; ?>>Open</option>
                            <option value="closed" <?php if ($event['status'] == 'closed') echo 'selected'; ?>>Closed</option>
                            <option value="canceled" <?php if ($event['status'] == 'canceled') echo 'selected'; ?>>Canceled</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="edit_event">Update Event</button>
            </form>
        </div>
    </main>

    <script>
        feather.replace();  // Initialize Feather Icons
    </script>
</body>
</html>
