<?php
// Admin Dashboard - requires admin login
$page_title = "Admin Dashboard";
include '../includes/auth_check.php';
requireAdmin(); // Require admin role
include '../includes/db_connect.php';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $requestid = intval($_POST['request_id']);
    $status = $_POST['status'];
    $ambulanceid = !empty($_POST['ambulance_id']) ? intval($_POST['ambulance_id']) : null;

    // Update request
    $stmt = $conn->prepare("UPDATE requests SET ambulanceid = ?, status = ? WHERE requestid = ?");
    $stmt->bind_param("isi", $ambulanceid, $status, $requestid);

    if ($stmt->execute()) {
        $success_message = "Request status updated successfully!";

        // Update ambulance status if needed
        if ($ambulanceid && $status === 'En route') {
            $stmt2 = $conn->prepare("UPDATE ambulances SET status = 'On call' WHERE ambulanceid = ?");
            $stmt2->bind_param("i", $ambulanceid);
            $stmt2->execute();
            $stmt2->close();
        } elseif ($ambulanceid && $status === 'Completed') {
            $stmt2 = $conn->prepare("UPDATE ambulances SET status = 'Available' WHERE ambulanceid = ?");
            $stmt2->bind_param("i", $ambulanceid);
            $stmt2->execute();
            $stmt2->close();
        }
    } else {
        $error_message = "Failed to update status: " . $stmt->error;
    }

    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $request_id = $_POST['request_id'];
    $message = $_POST['message'];
    $admin_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO messages (request_id, sender_id, message, sender_type, created_at) VALUES (?, ?, ?, 'admin', NOW())");
    $stmt->bind_param("iis", $request_id, $admin_id, $message);
    
    if ($stmt->execute()) {
        $success_message = "Message sent successfully!";
    } else {
        $error_message = "Failed to send message.";
    }
    $stmt->close();
}

// Get all requests with user and ambulance information
$requests_query = "
    SELECT r.*, 
           u.firstname, 
           u.lastname, 
           u.email, 
           u.phone AS user_phone,
           a.vehicle_number, 
           CONCAT(d.firstname, ' ', d.lastname) AS driver_name, 
           d.phone AS driver_phone
    FROM requests r 
    JOIN users u 
        ON r.userid = u.userid 
    LEFT JOIN ambulances a 
        ON r.ambulanceid = a.ambulanceid
    LEFT JOIN drivers d 
        ON a.driverid = d.driverid
    ORDER BY r.request_time DESC
";

$requests_result = $conn->query($requests_query);

// Get statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_requests,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_requests,
        SUM(CASE WHEN status = 'En route' THEN 1 ELSE 0 END) as enroute_requests,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_requests,
        SUM(CASE WHEN type = 'Emergency' THEN 1 ELSE 0 END) as emergency_requests
    FROM requests
";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Get ambulance statistics
$ambulance_stats = $conn->query("
    SELECT 
        COUNT(*) as total_ambulances,
        SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) as available_ambulances,
        SUM(CASE WHEN status = 'On call' THEN 1 ELSE 0 END) as oncall_ambulances,
        SUM(CASE WHEN status = 'Maintenance' THEN 1 ELSE 0 END) as maintenance_ambulances
    FROM ambulances
")->fetch_assoc();

$available_ambulances = $conn->query("
    SELECT a.*, CONCAT(d.firstname, ' ', d.lastname) AS driver_name
    FROM ambulances a 
    LEFT JOIN drivers d ON a.driverid = d.driverid 
    WHERE a.status = 'Available'
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
    
    <!-- GSAP Animation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Alpine.js for interactivity -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Leaflet for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Iconify for icons (use version 2.1.0 to match sidebar.php) -->
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>
<body class="bg-black text-white font-sans">
    <!-- Include admin header and sidebar -->
    <?php include '../includes/admin_header.php'; ?>
    <?php include 'sider.php'; ?>
    
    <!-- Main Content -->
    <main id="main-content" class="min-h-screen pt-14 transition-all duration-300 sm:ml-64 ml-20 max-w-full">
        <div class="p-4 sm:p-6">
            <!-- Success/Error messages -->
            <?php if (isset($success_message)): ?>
                <div class="mb-6 p-4 bg-grey-800 border border-grey-600 rounded-lg text-white max-w-full">
                    <div class="flex items-center">
                        <i class="ri-check-line mr-2"></i>
                        <?php echo $success_message; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="mb-6 p-4 bg-grey-800 border border-grey-600 rounded-lg text-white max-w-full">
                    <div class="flex items-center">
                        <i class="ri-error-warning-line mr-2"></i>
                        <?php echo $error_message; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8 max-w-full">
                <div class="bg-grey-900 border border-grey-700 rounded-lg p-4 sm:p-6 slide-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-grey-400 text-sm font-medium">Total Requests</p>
                            <p class="text-2xl sm:text-3xl font-bold text-white mt-2"><?php echo $stats['total_requests']; ?></p>
                        </div>
                        <div class="p-3 bg-grey-800 rounded-full">
                            <i class="ri-file-list-3-line text-xl sm:text-2xl text-white"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-grey-900 border border-grey-700 rounded-lg p-4 sm:p-6 slide-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-grey-400 text-sm font-medium">Pending</p>
                            <p class="text-2xl sm:text-3xl font-bold text-white mt-2"><?php echo $stats['pending_requests']; ?></p>
                        </div>
                        <div class="p-3 bg-grey-800 rounded-full">
                            <i class="ri-time-line text-xl sm:text-2xl text-white"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-grey-900 border border-grey-700 rounded-lg p-4 sm:p-6 slide-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-grey-400 text-sm font-medium">En Route</p>
                            <p class="text-2xl sm:text-3xl font-bold text-white mt-2"><?php echo $stats['enroute_requests']; ?></p>
                        </div>
                        <div class="p-3 bg-grey-800 rounded-full">
                            <i class="ri-truck-line text-xl sm:text-2xl text-white"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-grey-900 border border-grey-700 rounded-lg p-4 sm:p-6 slide-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-grey-400 text-sm font-medium">Completed</p>
                            <p class="text-2xl sm:text-3xl font-bold text-white mt-2"><?php echo $stats['completed_requests']; ?></p>
                        </div>
                        <div class="p-3 bg-grey-800 rounded-full">
                            <i class="ri-check-line text-xl sm:text-2xl text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Emergency Requests Table -->
            <div class="bg-grey-900 border border-grey-700 rounded-lg overflow-x-auto">
                <div class="px-4 sm:px-6 py-4 border-b border-grey-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-lg font-semibold text-white">Emergency Requests</h3>
                        <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                            <button onclick="refreshTable()" class="px-4 py-2 bg-grey-800 hover:bg-grey-700 text-white rounded-lg transition-colors duration-200 text-sm sm:text-base">
                                <i class="ri-refresh-line mr-2"></i>
                                Refresh
                            </button>
                            <label class="flex items-center space-x-2 text-grey-400">
                                <input type="checkbox" id="autoRefresh" checked class="rounded border-grey-600 bg-grey-800 text-white focus:ring-grey-400">
                                <span class="text-sm">Auto-refresh</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="min-w-full">
                    <table class="w-full table-auto">
                        <thead class="bg-grey-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">Patient</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">Type</th>
                                <th class="py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-grey-700">
                            <?php if ($requests_result->num_rows > 0): ?>
                                <?php while ($request = $requests_result->fetch_assoc()): ?>
                                    <tr class="hover:bg-grey-800 transition-colors duration-200 fade-in">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-white">#<?php echo $request['requestid']; ?></td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-white"><?php echo htmlspecialchars($request['firstname'] . ' ' . $request['lastname']); ?></div>
                                            <div class="text-sm text-grey-400"><?php echo htmlspecialchars($request['email']); ?></div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-grey-800 text-white border border-grey-600">
                                                <?php echo $request['type']; ?>
                                            </span>
                                        </td>
                                        <td class="py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-grey-800 text-white border border-grey-600">
                                                <?php echo $request['status']; ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <form method="POST" class="flex flex-col sm:flex-row sm:items-center sm:space-x-2">
                                                <input type="hidden" name="request_id" value="<?php echo $request['requestid']; ?>">
                                                <select name="status" class="text-sm bg-grey-800 border border-grey-600 rounded px-3 py-1 text-white focus:ring-2 focus:ring-grey-400 focus:border-transparent">
                                                    <option value="Pending" <?php echo $request['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="En route" <?php echo $request['status'] == 'En route' ? 'selected' : ''; ?>>En route</option>
                                                    <option value="Completed" <?php echo $request['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                                <button type="submit" name="update_status" class="mt-2 sm:mt-0 px-3 py-1 bg-grey-700 hover:bg-grey-600 text-white rounded transition-colors duration-200 text-sm">
                                                    Update
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center">
                                        <div class="text-grey-400">
                                            <i class="ri-file-list-3-line text-4xl mb-4"></i>
                                            <p class="text-lg font-medium">No requests found</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
    <!-- JavaScript with GSAP animations and sidebar sync -->
    <script>
        // Initialize GSAP animations
        gsap.registerPlugin(ScrollTrigger);
        
        // Animate elements on page load
        gsap.fromTo('.slide-up', 
            { opacity: 0, y: 30 }, 
            { opacity: 1, y: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out' }
        );
        
        gsap.fromTo('.fade-in', 
            { opacity: 0 }, 
            { opacity: 1, duration: 0.8, stagger: 0.05, ease: 'power2.out', delay: 0.3 }
        );
        
        let autoRefreshInterval;
        
        function startAutoRefresh() {
            if (document.getElementById('autoRefresh').checked) {
                autoRefreshInterval = setInterval(() => {
                    refreshTable();
                }, 30000);
            }
        }
        
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        }
        
        function refreshTable() {
            location.reload();
        }
        
        // Auto-refresh toggle
        document.getElementById('autoRefresh').addEventListener('change', function() {
            if (this.checked) {
                startAutoRefresh();
            } else {
                stopAutoRefresh();
            }
        });
        
        // Start auto-refresh on page load
        startAutoRefresh();

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