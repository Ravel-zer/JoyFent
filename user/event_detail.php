<?php
session_start();
require '../includes/db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$event_id = $_GET['event_id'];
$user_id = $_SESSION['user_id'];

// Ambil data event
$event = $conn->query("SELECT * FROM events WHERE event_id = '$event_id'")->fetch_assoc();
$success_message = '';
$error_message = '';

// Hitung jumlah partisipan yang telah mendaftar
$participants_count = $conn->query("SELECT COUNT(*) AS total FROM registrations WHERE event_id = '$event_id'")->fetch_assoc()['total'];

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_event'])) {
    $check_registration = $conn->query("SELECT * FROM registrations WHERE user_id = '$user_id' AND event_id = '$event_id'");
    
    if ($check_registration->num_rows > 0) {
        $error_message = "You are already registered for this event.";
    } else {
        $conn->query("INSERT INTO registrations (user_id, event_id) VALUES ('$user_id', '$event_id')");
        $success_message = "You have successfully registered for the event.";
        $participants_count++; // Increment setelah berhasil registrasi
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $event['name']; ?> | Event Detail</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/joyfent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .event-card {
            box-shadow: 0px 12px 24px rgba(0, 0, 0, 0.5);
            border-radius: 15px;
            overflow: hidden;
            background-color: #fff;
        }

        .btn-register {
            background-color: #1f2937;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-register:hover {
            background-color: #4b5563;
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-900 text-white">

<!-- Navbar -->
<?php include 'navbar.php'; ?>

<main class="container mx-auto px-4 py-12">
    <div class="event-card max-w-4xl mx-auto overflow-hidden">
        <img src="../admin/upload/<?php echo $event['image_url']; ?>" 
             alt="<?php echo $event['name']; ?>" class="w-full h-80 object-cover">
        <div class="p-8">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4"><?php echo $event['name']; ?></h1>
            <p class="text-lg text-gray-700 mb-6 leading-relaxed"><?php echo $event['description']; ?></p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg text-gray-600 mb-6">
                <p><i data-feather="map-pin"></i> <strong>Location:</strong> <?php echo $event['location']; ?></p>
                <p><i data-feather="calendar"></i> <strong>Date:</strong> <?php echo date('M j, Y', strtotime($event['date'])); ?></p>
                <p><i data-feather="clock"></i> <strong>Time:</strong> <?php echo date('h:i A', strtotime($event['time'])); ?></p>
                <p><i data-feather="users"></i> 
                    <strong>Participants:</strong> 
                    <?php echo $participants_count; ?> / <?php echo $event['max_participants']; ?>
                </p>
            </div>

            <?php if ($success_message): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded-lg flex items-center mb-4">
                    <i data-feather="check-circle" class="mr-2"></i>
                    <span><?php echo $success_message; ?></span>
                </div>
            <?php elseif ($error_message): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-lg flex items-center mb-4">
                    <i data-feather="alert-circle" class="mr-2"></i>
                    <span><?php echo $error_message; ?></span>
                </div>
            <?php else: ?>
                <form method="POST" class="mt-6">
                    <button type="submit" name="register_event" 
                            class="btn-register text-white font-bold py-3 px-6 rounded-lg w-full md:w-auto flex items-center gap-2 justify-center">
                        <i data-feather="plus-circle"></i> Register Now
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include 'footer.php'; ?>

<script>
    feather.replace(); // Initialize Feather Icons
</script>

</body>
</html>
