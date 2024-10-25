<?php
session_start();
require '../includes/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$show_success_popup = false;

// Proses cancel registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registration_id'])) {
    $registration_id = $_POST['registration_id'];

    // Hapus registrasi dari database
    $stmt = $conn->prepare("DELETE FROM registrations WHERE registration_id = ?");
    $stmt->bind_param("i", $registration_id);

    if ($stmt->execute()) {
        $show_success_popup = true; // Set flag untuk popup sukses
    } else {
        $error_message = "Failed to cancel registration.";
    }

    $stmt->close();
}

// Ambil daftar event yang diregistrasi user
$registrations = $conn->query("
    SELECT events.*, registrations.registration_id, registrations.registered_at 
    FROM registrations 
    JOIN events ON registrations.event_id = events.event_id 
    WHERE registrations.user_id = '$user_id'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events | JoyFent</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/joyfent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 CDN -->
    <style>
        body {
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        .empty-message {
            text-align: center;
            margin-top: 50px;
            color: #bbb;
            font-size: 1.5rem;
        }

        .event-card {
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .event-card:hover {
            transform: scale(1.05);
            box-shadow: 0px 12px 24px rgba(0, 0, 0, 0.5);
        }

        .btn-cancel {
            background-color: #d33;
            transition: background-color 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #b91c1c;
        }
    </style>
</head>
<body class="bg-gray-900 text-white">

    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto mt-20 px-4 py-12">
        <h1 class="text-4xl font-bold mb-8 text-center">My <span class="text-[#FFC107]">Events<span></h1>

        <?php if (isset($error_message)): ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '<?php echo $error_message; ?>'
                });
            </script>
        <?php endif; ?>

        <?php if ($registrations->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while ($event = $registrations->fetch_assoc()): ?>
                    <div 
                        class="event-card bg-white" 
                        onclick="goToEventDetail(<?php echo $event['event_id']; ?>)">
                        <img src="../admin/upload/<?php echo $event['image_url']; ?>" 
                             class="w-full h-48 object-cover" alt="Event Image">
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900"><?php echo $event['name']; ?></h2>
                            <p class="text-sm text-gray-600 mb-2">
                                Registered on: <?php echo date('M j, Y', strtotime($event['registered_at'])); ?>
                            </p>
                            <button 
                                class="btn-cancel text-white px-4 py-2 rounded mt-4 w-full"
                                onclick="event.stopPropagation(); confirmCancellation(<?php echo $event['registration_id']; ?>)">
                                Cancel Registration
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="empty-message">You have no registered events.</p>
        <?php endif; ?>
    </main>

    <script>
        // Fungsi untuk redirect ke halaman detail event
        function goToEventDetail(eventId) {
            window.location.href = `event_detail.php?event_id=${eventId}`;
        }

        // Fungsi untuk menampilkan SweetAlert konfirmasi pembatalan
        function confirmCancellation(registrationId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to cancel this registration?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('registration-id').value = registrationId;
                    document.getElementById('cancel-form').submit();
                }
            });
        }

        // Tampilkan SweetAlert sukses jika pembatalan berhasil
        <?php if ($show_success_popup): ?>
            Swal.fire({
                icon: 'success',
                title: 'Cancelled!',
                text: 'Registration canceled successfully.',
                showConfirmButton: false,
                timer: 1500
            });
        <?php endif; ?>
    </script>

    <!-- Form tersembunyi untuk membatalkan registrasi -->
    <form method="POST" id="cancel-form" style="display: none;">
        <input type="hidden" name="registration_id" id="registration-id">
    </form>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script>
        feather.replace(); // Initialize Feather Icons
    </script>

</body>
</html>
