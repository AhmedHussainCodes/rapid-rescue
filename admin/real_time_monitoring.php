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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - Rapid Rescue Admin'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body class="bg-dark text-light">
    <div class="d-flex">
        <!-- Sidebar Navigation -->
        <nav class="sidebar bg-black border-end border-secondary" style="width: 280px; min-height: 100vh;">
            <div class="p-4">
                <h4 class="text-white mb-4">
                    <i class="bi bi-shield-check-fill me-2"></i>Admin Panel
                </h4>
                
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white bg-secondary rounded active" href="real_time_monitoring.php">
                            <i class="bi bi-geo-alt-fill me-2"></i>Real-time Monitoring
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Real-time Monitoring</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-light" onclick="refreshMap()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="liveTracking" checked>
                        <label class="form-check-label text-muted" for="liveTracking">Live Tracking</label>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Live Map -->
                <div class="col-lg-8 mb-4">
                    <div class="card bg-grey-900 border-secondary">
                        <div class="card-header">
                            <h5 class="mb-0 text-white"><i class="bi bi-map me-2"></i>Live Map</h5>
                        </div>
                        <div class="card-body p-0">
                            <div id="liveMap" style="height: 600px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Active Requests Panel -->
                <div class="col-lg-4">
                    <div class="card bg-grey-900 border-secondary">
                        <div class="card-header">
                            <h5 class="mb-0 text-white"><i class="bi bi-activity me-2"></i>Active Requests</h5>
                        </div>
                        <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                            <?php if ($active_requests->num_rows > 0): ?>
                                <?php while ($request = $active_requests->fetch_assoc()): ?>
                                    <div class="border border-secondary rounded p-3 mb-3 fade-in">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="text-white mb-0">#<?php echo $request['requestid']; ?></h6>
                                            <span class="badge <?php echo $request['status'] == 'Pending' ? 'status-pending' : 'status-enroute'; ?>">
                                                <?php echo $request['status']; ?>
                                            </span>
                                        </div>
                                        
                                        <p class="text-muted mb-2">
                                            <strong><?php echo htmlspecialchars($request['firstname'] . ' ' . $request['lastname']); ?></strong><br>
                                            <?php echo htmlspecialchars($request['pickup_address']); ?>
                                        </p>
                                        
                                        <?php if ($request['vehicle_number']): ?>
                                            <div class="bg-black rounded p-2 mb-2">
                                                <small class="text-muted">Assigned Ambulance:</small><br>
                                                <strong class="text-white"><?php echo htmlspecialchars($request['vehicle_number']); ?></strong><br>
                                                <small class="text-muted">Driver: <?php echo htmlspecialchars($request['driver_name']); ?></small>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo date('g:i A', strtotime($request['request_time'])); ?>
                                        </small>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle fs-1 mb-3 d-block"></i>
                                    No active requests
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize map
        let map = L.map('liveMap').setView([40.7128, -74.0060], 12); // Default to NYC
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add markers for active requests and ambulances
        function updateMapMarkers() {
            // This would be populated with real data from the database
            // For demo purposes, adding sample markers
            
            // Sample ambulance marker
            L.marker([40.7589, -73.9851])
                .addTo(map)
                .bindPopup('<strong>Ambulance A001</strong><br>Status: En route<br>Driver: John Doe')
                .openPopup();
                
            // Sample request marker
            L.marker([40.7505, -73.9934])
                .addTo(map)
                .bindPopup('<strong>Emergency Request #123</strong><br>Patient: Jane Smith<br>Status: Pending');
        }
        
        updateMapMarkers();
        
        // Auto-refresh map data
        function refreshMap() {
            // In a real implementation, this would fetch updated coordinates
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
        gsap.fromTo('.fade-in', 
            { opacity: 0, x: 20 }, 
            { opacity: 1, x: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out' }
        );
    </script>
</body>
</html>
