<nav class="bg-black p-4 fixed top-0 left-0 w-full z-50 shadow-lg">
    <div class="container mx-auto flex justify-between items-center px-4 md:px-8">
        <!-- Logo -->
        <a href="index.php" class="flex items-center space-x-3">
            <!-- Adjust the height and width as needed -->
            <img src="../assets/img/joyfent.png" alt="JoyFent Logo" class="h-12"> 
            <span class="text-white text-2xl font-bold">
                Joy<span class="text-[#FFC107]">Fent</span>
            </span>
        </a>

        <!-- Hamburger Button (Mobile) -->
        <button 
            id="hamburger-button" 
            class="block md:hidden text-white focus:outline-none"
            onclick="toggleNavbar()">
            <i data-feather="menu" class="w-6 h-6"></i>
        </button>

        <!-- Navbar Links (Desktop) -->
        <div class="hidden md:flex space-x-6 items-center">
            <a href="my_events.php" class="text-white hover:text-[#FFC107] transition-colors">My Events</a>
            <a href="profile.php" class="text-white hover:text-[#FFC107] transition-colors">Profile</a>
            <a href="../logout.php" class="text-white hover:text-red-500 transition-colors">Logout</a>
        </div>
    </div>

    <!-- Overlay -->
    <div id="overlay" 
         class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 transition-opacity duration-300 ease-in-out"></div>

    <!-- Slide-in Menu (Mobile) -->
    <div id="mobile-menu" 
         class="fixed top-0 left-0 w-64 h-full bg-gradient-to-b from-gray-900 to-gray-800 text-white shadow-xl transform -translate-x-full transition-transform duration-500 ease-in-out z-50">
        <div class="p-6 flex justify-between items-center border-b border-gray-700">
            <span class="text-2xl font-bold">Menu</span>
            <button onclick="closeNavbar()" class="focus:outline-none">
                <i data-feather="x" class="w-6 h-6"></i> <!-- Close Icon -->
            </button>
        </div>
        <div class="flex flex-col space-y-6 px-6 py-8">
            <a href="my_events.php" class="hover:underline hover:text-gray-400 transition-colors">My Events</a>
            <a href="profile.php" class="hover:underline hover:text-gray-400 transition-colors">Profile</a>
            <a href="../logout.php" class="hover:underline hover:text-gray-400 transition-colors">Logout</a>
        </div>
    </div>
</nav>
