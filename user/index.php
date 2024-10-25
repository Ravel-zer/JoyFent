<?php
session_start();
require '../includes/db.php';

// Pastikan user sudah login
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Query untuk menampilkan event berdasarkan jumlah pendaftar (untuk carousel)
$popular_events = $conn->query("
    SELECT events.*, COUNT(registrations.event_id) AS registrant_count 
    FROM events 
    LEFT JOIN registrations ON events.event_id = registrations.event_id 
    WHERE events.status = 'open' 
    GROUP BY events.event_id 
    ORDER BY registrant_count DESC
");

// Query untuk menampilkan semua event tanpa batasan
$all_events = $conn->query("SELECT * FROM events ORDER BY date ASC");

// Ambil daftar event yang sudah diregistrasi user
$registered_events = [];
if ($user_id) {
    $result = $conn->query("SELECT event_id FROM registrations WHERE user_id = '$user_id'");
    while ($row = $result->fetch_assoc()) {
        $registered_events[] = $row['event_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | JoyFent</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/joyfent.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .event-card {
            transition: transform 0.3s, box-shadow 0.3s;
            background-color: #1f2937;
            color: #f3f4f6;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer; /* Set cursor menjadi pointer */
        }

        .event-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5);
            cursor: pointer; /* Kursor berubah saat hover */
        }

        .carousel-item {
            cursor: pointer;
        }

        .bookmark-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #facc15;
            color: black;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .registered-card {
            border: 2px solid #059669;
        }
    </style>
</head>
<body class="bg-gray-900 text-white">

    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Carousel Section -->
    <div class="carousel-container">
        <div class="carousel" id="carousel">
            <?php while ($event = $popular_events->fetch_assoc()): ?>
                <div class="carousel-item" onclick="goToEventDetail(<?php echo $event['event_id']; ?>)">
                    <img src="../admin/upload/<?php echo $event['image_url']; ?>" 
                         alt="<?php echo $event['name']; ?>" 
                         class="w-full h-[400px] object-cover">

                    <!-- Bookmark Badge -->
                    <?php if (in_array($event['event_id'], $registered_events)): ?>
                        <div class="bookmark-badge">
                            <i data-feather="bookmark"></i>
                        </div>
                    <?php endif; ?>

                    <div class="carousel-content">
                        <h3 class="text-2xl font-bold"><?php echo $event['name']; ?></h3>
                        <p class="text-sm"><?php echo $event['location']; ?></p>
                        <div class="flex items-center space-x-2">
                            <i data-feather="clock"></i>
                            <span><?php echo date('h:i A', strtotime($event['time'])); ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-feather="calendar"></i>
                            <span><?php echo date('M j, Y', strtotime($event['date'])); ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Carousel Navigation -->
        <button class="carousel-button left" onclick="scrollLeft()">
            <i data-feather="chevron-left"></i>
        </button>
        <button class="carousel-button right" onclick="scrollRight()">
            <i data-feather="chevron-right"></i>
        </button>
    </div>

    <!-- Grid Section for All Events -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-4">All Events</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php while ($event = $all_events->fetch_assoc()): ?>
                <div class="event-card <?php echo in_array($event['event_id'], $registered_events) ? 'registered-card' : ''; ?>"
                     onclick="goToEventDetail(<?php echo $event['event_id']; ?>)">
                    <img src="../admin/upload/<?php echo $event['image_url']; ?>" 
                         alt="<?php echo $event['name']; ?>" 
                         class="w-full h-48 object-cover">

                    <!-- Bookmark Badge -->
                    <?php if (in_array($event['event_id'], $registered_events)): ?>
                        <div class="bookmark-badge">
                            <i data-feather="bookmark"></i>
                        </div>
                    <?php endif; ?>

                    <div class="p-4">
                        <h3 class="text-lg font-semibold"><?php echo $event['name']; ?></h3>
                        <p class="text-sm text-gray-400"><?php echo $event['location']; ?></p>
                        <div class="flex items-center space-x-2 my-2">
                            <i data-feather="clock"></i>
                            <span><?php echo date('h:i A', strtotime($event['time'])); ?></span>
                        </div>
                        <div class="flex items-center space-x-2 my-2">
                            <i data-feather="calendar"></i>
                            <span><?php echo date('M j, Y', strtotime($event['date'])); ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        feather.replace();  // Initialize Feather Icons

        function goToEventDetail(eventId) {
            window.location.href = `event_detail.php?event_id=${eventId}`;
        }

        const carousel = document.getElementById('carousel');
        let currentIndex = 0;
        const totalItems = carousel.children.length;

        function scrollRight() {
            currentIndex = (currentIndex + 1) % totalItems;
            updateCarousel();
        }

        function scrollLeft() {
            currentIndex = (currentIndex - 1 + totalItems) % totalItems;
            updateCarousel();
        }

        function updateCarousel() {
            const itemWidth = carousel.children[0].offsetWidth;
            carousel.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
        }

        setInterval(scrollRight, 3000);
        window.addEventListener('resize', updateCarousel);
    </script>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>
</html>
