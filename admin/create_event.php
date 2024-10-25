<?php
session_start();
require '../includes/db.php';

// Cek apakah user yang login adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$success_message = '';
$error_message = '';

// Proses form untuk membuat event
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil nilai dari form
    $name = $_POST['name'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $max_participants = $_POST['max_participants'];

    // Set status, default 'open' jika tidak diisi
    $status = isset($_POST['status']) && $_POST['status'] !== '' ? $_POST['status'] : 'open';

    // Proses unggah gambar
    $image_url = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = uniqid() . "-" . basename($_FILES['image']['name']);
        $upload_dir = "../admin/upload/";
        $target_file = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = $image_name;
        } else {
            $error_message = "Failed to upload image.";
        }
    }

    // Simpan event ke database
    $stmt = $conn->prepare("
        INSERT INTO events (name, date, time, location, description, max_participants, status, image_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if ($stmt === false) {
        die("MySQL Error: " . $conn->error);
    }

    // Bind parameter dan eksekusi query
    $stmt->bind_param(
        "sssssiis", 
        $name, $date, $time, $location, $description, $max_participants, $status, $image_url
    );

    if ($stmt->execute()) {
        $success_message = "Event created successfully!";
    } else {
        $error_message = "Failed to create event: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event | JoyFent</title>
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

    <main class="container mx-auto px-4 py-8">
        <div class="form-container">
            <h1 class="text-3xl font-bold mb-4">Create New Event</h1>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block font-medium">Event Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Date</label>
                        <input type="date" name="date" required>
                    </div>
                    <div>
                        <label class="block font-medium">Time</label>
                        <input type="time" name="time" required>
                    </div>
                </div>

                <div>
                    <label class="block font-medium">Location</label>
                    <input type="text" name="location" required>
                </div>

                <div>
                    <label class="block font-medium">Description</label>
                    <textarea name="description" rows="4" required></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Max Participants</label>
                        <input type="number" name="max_participants" required>
                    </div>
                    <div>
                        <label class="block font-medium">Status</label>
                        <select name="status" required>
                            <option value="open" selected>Open</option>
                            <option value="closed">Closed</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-medium">Upload Image</label>
                    <input type="file" name="image">
                </div>

                <button type="submit">Create Event</button>
            </form>
        </div>
    </main>

    <div id="toast" class="toast"></div>

    <script>
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
