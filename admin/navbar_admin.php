<nav class="bg-black p-4 shadow-lg">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo Admin Dashboard -->
        <a href="index.php" class="text-white text-2xl font-bold">
            Admin <span class="text-yellow-500">Dashboard</span>
        </a>

        <!-- Hamburger Button (Mobile) -->
        <button 
            id="hamburger-button" 
            class="block md:hidden text-white focus:outline-none"
            onclick="toggleNavbar()">
            <i data-feather="menu" class="w-6 h-6"></i>
        </button>

        <!-- Navbar Links (Desktop) -->
        <div id="navbar-links" class="hidden md:flex space-x-6 items-center">
            <a href="create_event.php" class="text-white hover:text-yellow-500 transition-colors">Create Event</a>
            <a href="manage_users.php" class="text-white hover:text-yellow-500 transition-colors">Manage Users</a>
            <a href="../logout.php" class="text-white hover:text-red-500 transition-colors">Logout</a>
        </div>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-60 hidden transition-opacity duration-300" 
         onclick="toggleNavbar()"></div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" 
         class="fixed top-0 left-0 w-64 h-full bg-gray-900 text-white transform -translate-x-full 
                transition-transform duration-500 ease-in-out">
        <div class="p-4 flex justify-between items-center border-b border-gray-700">
            <span class="text-2xl font-bold">Menu</span>
            <button onclick="toggleNavbar()" class="focus:outline-none">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="flex flex-col space-y-4 px-6 py-8">
            <a href="create_event.php" class="hover:text-yellow-500 transition-colors">Create Event</a>
            <a href="manage_users.php" class="hover:text-yellow-500 transition-colors">Manage Users</a>
            <a href="../logout.php" class="hover:text-red-500 transition-colors">Logout</a>
        </div>
    </div>
</nav>

<script>
    function toggleNavbar() {
        const mobileMenu = document.getElementById('mobile-menu');
        const overlay = document.getElementById('overlay');

        // Toggle slide-in menu and overlay visibility
        mobileMenu.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Auto-close menu on resize to desktop size
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            document.getElementById('mobile-menu').classList.add('-translate-x-full');
            document.getElementById('overlay').classList.add('hidden');
        }
    });

    feather.replace(); // Initialize Feather Icons
</script>
