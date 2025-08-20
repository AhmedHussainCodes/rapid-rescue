<?php
// Real-time Monitoring - Live map and tracking
$page_title = "Real-time Monitoring";
include '../includes/auth_check.php';
requireAdmin();
include '../includes/db_connect.php';

// Get active requests with location data
$active_requests = $conn->query("
    SELECT r.*, u.firstname, u.lastname, a.vehicle_number,
           d.firstname AS driver_firstname, d.lastname AS driver_lastname, d.phone AS driver_phone
    FROM requests r 
    JOIN users u ON r.userid = u.userid 
    LEFT JOIN ambulances a ON r.ambulanceid = a.ambulanceid
    LEFT JOIN drivers d ON a.driverid = d.driverid
    WHERE r.status IN ('Pending', 'En route')
    ORDER BY r.request_time DESC
");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - Rapid Rescue Admin'; ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🚑</text></svg>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: ['class', '[data-theme="dark"]'],
            theme: {
                extend: {
                    colors: {
                        'black': '#000000',
                        'white': '#ffffff',
                        'grey': {
                            100: '#f5f5f5',
                            200: '#e5e5e5',
                            300: '#d4d4d4',
                            400: '#a3a3a3',
                            500: '#737373',
                            600: '#525252',
                            700: '#404040',
                            800: '#262626',
                            900: '#171717'
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- GSAP Animation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Iconify for icons -->
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>
<body class="bg-black text-white font-sans">
    <!-- Include admin header and sidebar -->
    <?php include '../includes/admin_header.php'; ?>
    <?php include 'sider.php'; ?>
    
    <!-- Main Content -->
    <main id="main-content" class="min-h-screen pt-14 transition-all duration-300 sm:ml-64 ml-20 max-w-full">
        <div class="p-4 sm:p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h2 class="text-xl sm:text-2xl font-semibold text-white mb-4 sm:mb-0">
                    <iconify-icon icon="ri:map-pin-line" class="mr-2"></iconify-icon>Real-time Monitoring
                </h2>
                <div class="flex items-center gap-4">
                    <button onclick="refreshMap()" class="px-4 py-2 bg-grey-800 hover:bg-grey-700 text-white rounded-lg transition-colors duration-200 slide-up text-sm sm:text-base">
                        <iconify-icon icon="ri:refresh-line" class="mr-2"></iconify-icon>Refresh
                    </button>
                    <div class="flex items-center">
                        <input type="checkbox" id="liveTracking" class="peer h-4 w-4 text-grey-400 focus:ring-grey-400 border-grey-600 rounded" checked>
                        <label for="liveTracking" class="ml-2 text-sm text-grey-400">Live Tracking</label>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 max-w-full">
                <!-- Live Map -->
                <div class="lg:col-span-2">
                    <div class="bg-grey-900 border border-grey-700 rounded-lg overflow-x-auto">
                        <div class="px-4 sm:px-6 py-4 border-b border-grey-700">
                            <h3 class="text-lg font-semibold text-white"><iconify-icon icon="ri:map-line" class="mr-2"></iconify-icon>Live Map</h3>
                        </div>
                        <div class="p-0">
                            <div id="liveMap" class="w-full h-[50vh] sm:h-[600px]"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Active Requests Panel -->
                <div class="lg:col-span-1">
                    <div class="bg-grey-900 border border-grey-700 rounded-lg overflow-x-auto">
                        <div class="px-4 sm:px-6 py-4 border-b border-grey-700">
                            <h3 class="text-lg font-semibold text-white"><iconify-icon icon="ri:pulse-line" class="mr-2"></iconify-icon>Active Requests</h3>
                        </div>
                        <div class="p-4 max-h-[50vh] sm:max-h-[600px] overflow-y-auto">
                            <?php if ($active_requests->num_rows > 0): ?>
                                <?php while ($request = $active_requests->fetch_assoc()): ?>
                                    <div class="border border-grey-700 rounded-lg p-4 mb-4 bg-grey-800 hover:bg-grey-700 transition-colors duration-200 fade-in">
                                        <div class="flex justify-between items-start mb-2">
                                            <h6 class="text-white text-sm font-semibold">#<?php echo $request['requestid']; ?></h6>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-grey-900 text-white border border-grey-600"><?php echo $request['status']; ?></span>
                                        </div>
                                        <p class="text-grey-400 text-sm mb-2">
                                            <strong class="text-white"><?php echo htmlspecialchars($request['firstname'] . ' ' . $request['lastname']); ?></strong><br>
                                            <?php echo htmlspecialchars($request['pickup_address']); ?>
                                        </p>
                                        <?php if ($request['vehicle_number']): ?>
                                            <div class="bg-grey-900 rounded-lg p-2 mb-2">
                                                <p class="text-sm text-grey-400 mb-1">Assigned Ambulance:</p>
                                                <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($request['vehicle_number']); ?></p>
                                                <p class="text-sm text-grey-400">Driver: <?php echo htmlspecialchars($request['driver_firstname'] . ' ' . $request['driver_lastname']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <p class="text-sm text-grey-400">
                                            <iconify-icon icon="ri:time-line" class="mr-1"></iconify-icon>
                                            <?php echo date('g:i A', strtotime($request['request_time'])); ?>
                                        </p>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center text-grey-400 py-12">
                                    <iconify-icon icon="ri:checkbox-circle-line" class="text-4xl mb-4"></iconify-icon>
                                    <p class="text-lg font-medium">No active requests</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript with GSAP animations, Leaflet map, and sidebar sync -->
    <script>
        // Initialize map
        let map = L.map('liveMap').setView([40.7128, -74.0060], 12); // Default to NYC
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add markers for active requests and ambulances
        function updateMapMarkers() {
            // Sample ambulance marker
            L.marker([40.7589, -73.9851])
                .addTo(map)
                .bindPopup('<strong>Ambulance A001</strong><br>Status: En route<br>Driver: John Doe');
                
            // Sample request marker
            L.marker([40.7505, -73.9934])
                .addTo(map)
                .bindPopup('<strong>Emergency Request #123</strong><br>Patient: Jane Smith<br>Status: Pending');
        }
        
        updateMapMarkers();
        
        // Auto-refresh map data
        function refreshMap() {
            console.log('Refreshing map data...');
        }
        
        // Live tracking toggle
        let liveTrackingInterval;
        
        document.getElementById('liveTracking').addEventListener('change', function() {
            if (this.checked) {
                liveTrackingInterval = setInterval(refreshMap, 10000); // Update every 10 seconds
            } else {
                clearInterval(liveTrackingInterval);
            }
        });
        
        // Start live tracking
        liveTrackingInterval = setInterval(refreshMap, 10000);
        
        // GSAP animations
        gsap.registerPlugin(ScrollTrigger);
        
        gsap.fromTo('.slide-up', 
            { opacity: 0, y: 30 }, 
            { opacity: 1, y: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out' }
        );
        
        gsap.fromTo('.fade-in', 
            { opacity: 0 }, 
            { opacity: 1, duration: 0.8, stagger: 0.05, ease: 'power2.out', delay: 0.3 }
        );

        // Fallback to sync main content with sidebar state
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.getElementById('toggle-sidebar');
            
            if (!sidebar || !mainContent || !toggleBtn) {
                console.error('Sidebar, main content, or toggle button not found.');
                return;
            }

            // Function to update main content margin based on sidebar state
            function updateMainContentMargin() {
                const isCollapsed = sidebar.classList.contains('collapsed');
                const isMobile = window.matchMedia("(max-width: 639px)").matches;

                if (isMobile || isCollapsed) {
                    mainContent.classList.remove('sm:ml-64');
                    mainContent.classList.add('ml-20');
                } else {
                    mainContent.classList.remove('ml-20');
                    mainContent.classList.add('sm:ml-64');
                }
            }

            // Initial margin update
            updateMainContentMargin();

            // Listen for sidebar toggle
            toggleBtn.addEventListener('click', updateMainContentMargin);

            // Listen for window resize
            window.addEventListener('resize', updateMainContentMargin);
        });
    </script>
</body>
</html>