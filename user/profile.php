<?php
session_start();
require '../includes/db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE user_id = '$user_id'")->fetch_assoc();
$profile_image = "../user/uploads/" . ($user['profile_image'] ?? 'default-avatar.png');

// Pagination Logic
$events_per_page = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $events_per_page;

$registrations = $conn->query("
    SELECT events.name, events.date, events.location, events.image_url 
    FROM registrations 
    JOIN events ON registrations.event_id = events.event_id 
    WHERE registrations.user_id = '$user_id'
    LIMIT $events_per_page OFFSET $offset
");

$total_events = $conn->query("
    SELECT COUNT(*) AS total FROM registrations WHERE user_id = '$user_id'
")->fetch_assoc()['total'];
$total_pages = ceil($total_events / $events_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | JoyFent</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/joyfent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        .bg-header {
            background-color: #1a1a1a;
        }

        .event-card {
            display: flex;
            background-color: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .event-card:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .event-image {
            height: 150px;
            width: 150px;
            object-fit: cover;
            margin-right: 16px;
        }

        .event-details {
            flex: 1;
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .event-info {
            display: flex;
            align-items: center;
            margin-top: 8px;
            font-size: 16px;
            color: #4b5563;
        }

        .event-info i {
            margin-right: 8px;
            color: #6b7280;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 24px;
            gap: 12px;
        }

        .pagination a {
            background-color: #1f2937;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #374151;
        }

        .pagination a.active {
            background-color: #4b5563;
        }

        @media (min-width: 768px) {
            .event-card {
                flex-direction: row;
                align-items: center;
            }
        }

        @media (max-width: 767px) {
            .event-card {
                flex-direction: column;
            }
            .event-image {
                width: 100%;
                height: 180px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-header h-40"></div>
            <div class="relative -mt-16 flex justify-center">
                <img class="w-36 h-36 rounded-full border-4 border-white object-cover" 
                     src="<?php echo $profile_image; ?>" 
                     alt="User Avatar">
            </div>
            <div class="p-8 text-center">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <?php echo $user['username']; ?>
                </h1>
                <p class="text-lg text-gray-500 mb-6"><?php echo $user['email']; ?></p>

                <!-- Tambahkan Link Edit Profile -->
                <a href="edit_profile.php" 
                   class="btn-custom bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Edit Profile
                </a>

                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-6">My Event Registrations</h2>

                <div class="grid grid-cols-1 gap-6">
                    <?php if ($registrations->num_rows > 0): ?>
                        <?php while ($event = $registrations->fetch_assoc()): ?>
                            <div class="event-card">
                                <img src="../admin/upload/<?php echo $event['image_url']; ?>" 
                                     alt="<?php echo $event['name']; ?>" class="event-image">
                                <div class="event-details">
                                    <h3 class="text-xl font-bold text-gray-800"><?php echo $event['name']; ?></h3>
                                    <div class="event-info">
                                        <i data-feather="calendar"></i>
                                        <span><?php echo date('M j, Y', strtotime($event['date'])); ?></span>
                                    </div>
                                    <div class="event-info">
                                        <i data-feather="map-pin"></i>
                                        <span><?php echo $event['location']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-600">You have not registered for any events yet.</p>
                    <?php endif; ?>
                </div>

                <div class="pagination mt-8">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        feather.replace();  // Initialize Feather Icons
    </script>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>
