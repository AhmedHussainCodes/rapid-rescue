<?php
$currentPage = basename($_SERVER['PHP_SELF']);

if (!function_exists('isActive')) {
    function isActive($page) {
        global $currentPage;
        return $currentPage === $page
            ? 'bg-grey-700 text-white border-grey-500'
            : 'text-grey-300 hover:bg-grey-700 hover:text-white';
    }
}
?>

<style>
    #sidebar.collapsed {
        width: 5rem; /* 80px */
    }

    #sidebar {
        transition: width 0.3s ease;
        overflow-x: hidden;
        overflow-y: auto;
    }

    #sidebar.collapsed .nav-text {
        opacity: 0;
        visibility: hidden;
        position: absolute;
        left: calc(100% + 0.75rem);
        top: 50%;
        transform: translateY(-50%);
        background: #171717; /* grey-900 */
        color: #ffffff; /* white */
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #404040; /* grey-700 */
        white-space: nowrap;
        pointer-events: none;
        z-index: 1000; /* High z-index to ensure visibility */
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }

    #sidebar.collapsed .nav-link:hover .nav-text {
        opacity: 1;
        visibility: visible;
    }

    /* Ensure icons are always visible */
    .nav-link iconify-icon {
        color: #ffffff;
        font-size: 1.5rem;
        width: 24px;
        text-align: center;
    }

    #sidebar.collapsed .nav-link {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0.75rem;
    }

    #sidebar.collapsed .nav-link iconify-icon {
        font-size: 1.75rem; /* Larger icons in collapsed mode */
    }

    /* Hide logo when sidebar is open, show when collapsed */
    #sidebar:not(.collapsed) .header-logo {
        display: none;
    }

    #sidebar.collapsed .header-logo {
        display: block;
    }

    @media (max-width: 639px) {
        #sidebar {
            width: 5rem; /* Always collapsed on mobile */
        }

        #sidebar .nav-text {
            opacity: 0;
            visibility: hidden;
            position: absolute;
            left: calc(100% + 0.75rem);
            top: 50%;
            transform: translateY(-50%);
            background: #171717;
            color: #ffffff;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #404040;
            white-space: nowrap;
            pointer-events: none;
            z-index: 1000;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }

        #sidebar .nav-link:hover .nav-text {
            opacity: 1;
            visibility: visible;
        }

        .header-logo {
            display: block !important;
        }
    }
</style>

<!-- Header -->
<header class="bg-grey-900 border-b border-grey-700 shadow-md fixed top-0 right-0 z-10 w-full h-14 flex items-center px-4 sm:px-6 scroll-animate">
    <nav class="flex items-center justify-between w-full">
        <!-- Logo -->
        <div class="flex items-center">
            <img src="../images/logo.png" width="100" alt="Rapid Rescue Logo" class="mx-16 header-logo">
        </div>

        <!-- User Info -->
        <div class="flex items-center gap-2 ml-auto relative" x-data="{ open: false }">
            <!-- Theme Toggle -->
            <button id="theme-toggle" 
                class="w-10 h-10 rounded-full bg-grey-800 border border-grey-600 flex items-center justify-center text-grey-300 hover:bg-grey-700 hover:text-white transition-all duration-300">
                <iconify-icon icon="ri:moon-line" class="text-lg"></iconify-icon>
            </button>

            <!-- User Button with Down Arrow -->
            <button @click="open = !open"
                class="flex items-center gap-2 bg-grey-700 border border-grey-500 rounded px-3 py-1 text-white font-semibold hover:bg-grey-600 transition-all duration-300">
                <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                <iconify-icon icon="ri:arrow-down-s-line" class="text-white"></iconify-icon>
            </button>

            <!-- Dropdown -->
            <div 
                x-show="open"
                x-transition
                @click.away="open = false"
                class="absolute right-0 mt-28 w-48 bg-grey-800 border border-grey-600 rounded-lg shadow-lg z-50"
            >
            <a href="../index.php" target="_blank" class="block px-4 py-2 text-sm text-white hover:bg-grey-700 rounded-b-lg">
                <i class="ri-earth-line mr-2"></i> Visit Website
            </a>
            <a href="../logout.php" class="block px-4 py-2 text-sm text-white hover:bg-red-500 rounded-t-lg">
                <i class="ri-logout-box-line mr-2"></i> Logout
            </a>
            </div>
        </div>
    </nav>
</header>

<!-- Sidebar -->
<aside id="sidebar" class="w-20 sm:w-64 bg-grey-900 border-r border-grey-700 shadow-2xl h-screen fixed left-0 top-0 z-20 flex flex-col scroll-animate">
    <div class="p-4 sm:p-4 flex items-center justify-between border-b border-grey-700">
        <div class="flex items-center gap-2 overflow-hidden">
            <a href="dashboard.php" class="group">
                <img src="../images/logo.png" width="100" alt="Rapid Rescue Logo" class="group-hover:scale-105 transition-all duration-300">
            </a>
        </div>
        <div class="flex justify-end p-2">
            <button id="toggle-sidebar" class="w-10 h-10 flex items-center justify-center text-grey-400 hover:bg-grey-800 hover:text-white rounded-full transition-all duration-300">
                <iconify-icon icon="ri:arrow-right-s-line" class="ri-xl transition-transform duration-300"></iconify-icon>
            </button>
        </div>
    </div>

    <div id="sidebar-content-hide" class="py-4 flex-1">
        <a href="dashboard.php" class="nav-link flex items-center gap-3 px-4 py-3 font-medium border-l-4 border-transparent <?php echo isActive('dashboard.php'); ?> transition-all duration-300 group relative">
            <div class="w-12 h-8 flex items-center justify-center">
                <iconify-icon icon="ri:dashboard-line" class="text-white group-hover:scale-110 transition-transform duration-300"></iconify-icon>
            </div>
            <span class="text-white nav-text tooltip">Dashboard</span>
        </a>

        <a href="manage_ambulances.php" class="nav-link flex items-center gap-3 px-4 py-3 font-medium border-l-4 border-transparent <?php echo isActive('manage_ambulances.php'); ?> transition-all duration-300 group relative">
            <div class="w-12 h-8 flex items-center justify-center">
                <iconify-icon icon="ri:truck-line" class="text-white group-hover:scale-110 transition-transform duration-300"></iconify-icon>
            </div>
            <span class="text-white nav-text tooltip">Ambulances</span>
        </a>

        <a href="manage_drivers.php" class="nav-link flex items-center gap-3 px-4 py-3 font-medium border-l-4 border-transparent <?php echo isActive('manage_drivers.php'); ?> transition-all duration-300 group relative">
            <div class="w-12 h-8 flex items-center justify-center">
                <iconify-icon icon="ri:user-line" class="text-white group-hover:scale-110 transition-transform duration-300"></iconify-icon>
            </div>
            <span class="text-white nav-text tooltip">Drivers</span>
        </a>

        <a href="real_time_monitoring.php" class="nav-link flex items-center gap-3 px-4 py-3 font-medium border-l-4 border-transparent <?php echo isActive('real_time_monitoring.php'); ?> transition-all duration-300 group relative">
            <div class="w-12 h-8 flex items-center justify-center">
                <iconify-icon icon="ri:map-pin-line" class="text-white group-hover:scale-110 transition-transform duration-300"></iconify-icon>
            </div>
            <span class="text-white nav-text tooltip">Real-time Monitoring</span>
        </a>

        <a href="user_management.php" class="nav-link flex items-center gap-3 px-4 py-3 font-medium border-l-4 border-transparent <?php echo isActive('user_management.php'); ?> transition-all duration-300 group relative">
            <div class="w-12 h-8 flex items-center justify-center">
                <iconify-icon icon="ri:group-line" class="text-white group-hover:scale-110 transition-transform duration-300"></iconify-icon>
            </div>
            <span class="text-white nav-text tooltip">User Management</span>
        </a>

    </div>
   

</aside>

<script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("toggle-sidebar");
        const mainContent = document.getElementById("main-content");
        const header = document.querySelector("header");
        const icon = toggleBtn.querySelector("iconify-icon");
        const themeToggle = document.getElementById("theme-toggle");

        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            html.className = newTheme;
            
            const themeIcon = themeToggle.querySelector('iconify-icon');
            themeIcon.setAttribute('icon', newTheme === 'dark' ? 'ri:moon-line' : 'ri:sun-line');
            
            localStorage.setItem('theme', newTheme);
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.documentElement.className = savedTheme;
        const themeIcon = themeToggle.querySelector('iconify-icon');
        themeIcon.setAttribute('icon', savedTheme === 'dark' ? 'ri:moon-line' : 'ri:sun-line');

        themeToggle.addEventListener('click', toggleTheme);

        function updateSidebar() {
            const isMobile = window.matchMedia("(max-width: 639px)").matches;

            if (isMobile) {
                sidebar.classList.add("collapsed");
                sidebar.classList.remove("w-64");
                sidebar.classList.add("w-20");
                header.classList.add("sidebar-collapsed");

                if (mainContent) {
                    mainContent.classList.remove("ml-64");
                    mainContent.classList.add("ml-20");
                }

                icon.setAttribute('icon', 'ri:arrow-right-s-line');
            } else {
                sidebar.classList.remove("collapsed");
                sidebar.classList.remove("w-20");
                sidebar.classList.add("w-64");
                header.classList.remove("sidebar-collapsed");

                if (mainContent) {
                    mainContent.classList.remove("ml-20");
                    mainContent.classList.add("ml-64");
                }

                icon.setAttribute('icon', 'ri:arrow-left-s-line');
            }
        }

        updateSidebar();
        window.addEventListener("resize", updateSidebar);

        toggleBtn.addEventListener("click", function () {
            const isMobile = window.matchMedia("(max-width: 639px)").matches;
            if (isMobile) return;

            const isCollapsed = sidebar.classList.toggle("collapsed");
            sidebar.classList.toggle("w-20");
            sidebar.classList.toggle("w-64");
            header.classList.toggle("sidebar-collapsed");

            if (mainContent) {
                mainContent.classList.toggle("ml-20");
                mainContent.classList.toggle("ml-64");
            }

            icon.setAttribute('icon', isCollapsed ? 'ri:arrow-right-s-line' : 'ri:arrow-left-s-line');
        });

        // GSAP animations for sidebar items
        gsap.registerPlugin(ScrollTrigger);
        gsap.fromTo('.nav-link', 
            { opacity: 0, x: -20 }, 
            { opacity: 1, x: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out' }
        );

        // Animate tooltips on hover
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('mouseenter', function() {
                if (sidebar.classList.contains('collapsed')) {
                    const tooltip = this.querySelector('.nav-text');
                    gsap.to(tooltip, { opacity: 1, visibility: 'visible', duration: 0.2, ease: 'power2.out' });
                }
            });
            link.addEventListener('mouseleave', function() {
                if (sidebar.classList.contains('collapsed')) {
                    const tooltip = this.querySelector('.nav-text');
                    gsap.to(tooltip, { opacity: 0, visibility: 'hidden', duration: 0.2, ease: 'power2.out' });
                }
            });
        });
    });
</script>