<?php
// Admin Dashboard - requires admin login
$page_title = "Admin Dashboard";
include '../includes/auth_check.php';
requireAdmin(); // Require admin role
include '../includes/db_connect.php';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['status'];
    $ambulance_id = $_POST['ambulance_id'] ?? null;
    
    $stmt = $conn->prepare("UPDATE requests SET status = ?, ambulance_id = ? WHERE requestid = ?");
    $stmt->bind_param("sii", $new_status, $ambulance_id, $request_id);
    
    if ($stmt->execute()) {
        $success_message = "Request status updated successfully!";
        
        if ($ambulance_id && $new_status == 'En route') {
            $conn->query("UPDATE ambulances SET status = 'On call' WHERE ambulanceid = $ambulance_id");
        } elseif ($new_status == 'Completed' && $ambulance_id) {
            $conn->query("UPDATE ambulances SET status = 'Available' WHERE ambulanceid = $ambulance_id");
        }
    } else {
        $error_message = "Failed to update status.";
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
// Get available ambulances
$requests_query = "
    SELECT r.*, u.firstname, u.lastname, u.email, u.phone AS user_phone,
           a.vehicle_number, CONCAT(d.firstname, ' ', d.lastname) AS driver_name, d.phone AS driver_phone
    FROM requests r 
    JOIN users u ON r.userid = u.userid 
    LEFT JOIN ambulances a ON r.ambulanceid = a.ambulanceid
    LEFT JOIN drivers d ON a.driverid = d.driverid
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - Rapid Rescue Admin'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- GSAP Animation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">
    
    <!-- Added Leaflet for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-dark text-light">
    <div class="d-flex">
        <!-- Enhanced Sidebar Navigation -->
        <nav class="sidebar bg-black border-end border-secondary" style="width: 280px; min-height: 100vh;">
            <div class="p-4">
                <h4 class="text-white mb-4">
                    <i class="bi bi-shield-check-fill me-2"></i>Admin Panel
                </h4>
                
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link text-white bg-secondary rounded active" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="real_time_monitoring.php">
                            <i class="bi bi-geo-alt-fill me-2"></i>Real-time Monitoring
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="manage_ambulances.php">
                            <i class="bi bi-truck-front me-2"></i>Ambulance Management
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="manage_drivers.php">
                            <i class="bi bi-person-badge me-2"></i>Driver Management
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link text-light" href="user_management.php">
                            <i class="bi bi-people-fill me-2"></i>User Management
                        </a>
                    </li>
                </ul>
                
                <hr class="border-secondary my-4">
                
                <div class="dropdown">
                    <a class="nav-link text-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i><?php echo htmlspecialchars($_SESSION['firstname']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="../index.php"><i class="bi bi-house me-2"></i>View Site</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white mb-0"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h2>
                <div class="text-muted">
                    <i class="bi bi-clock me-1"></i>
                    <span id="current-time"></span>
                </div>
            </div>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success fade-in" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger fade-in" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-grey-900 border-secondary slide-up">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-1">Total Requests</h6>
                                    <h3 class="mb-0 text-white"><?php echo $stats['total_requests']; ?></h3>
                                </div>
                                <i class="bi bi-clipboard-data fs-1 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-grey-900 border-secondary slide-up">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-1">Pending</h6>
                                    <h3 class="mb-0 text-warning"><?php echo $stats['pending_requests']; ?></h3>
                                </div>
                                <i class="bi bi-clock-fill fs-1 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-grey-900 border-secondary slide-up">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-1">En Route</h6>
                                    <h3 class="mb-0 text-info"><?php echo $stats['enroute_requests']; ?></h3>
                                </div>
                                <i class="bi bi-truck-front-fill fs-1 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-grey-900 border-secondary slide-up">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-1">Completed</h6>
                                    <h3 class="mb-0 text-success"><?php echo $stats['completed_requests']; ?></h3>
                                </div>
                                <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Enhanced Ambulance Status with Quick Actions -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card bg-grey-900 border-secondary">
                        <div class="card-body">
                            <h6 class="text-muted">Available Ambulances</h6>
                            <h4 class="text-success mb-2"><?php echo $ambulance_stats['available_ambulances']; ?> / <?php echo $ambulance_stats['total_ambulances']; ?></h4>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?php echo ($ambulance_stats['available_ambulances'] / max($ambulance_stats['total_ambulances'], 1)) * 100; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-grey-900 border-secondary">
                        <div class="card-body">
                            <h6 class="text-muted">On Call</h6>
                            <h4 class="text-warning mb-2"><?php echo $ambulance_stats['oncall_ambulances']; ?></h4>
                            <small class="text-muted">Currently responding</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-grey-900 border-secondary">
                        <div class="card-body">
                            <h6 class="text-muted">Maintenance</h6>
                            <h4 class="text-danger mb-2"><?php echo $ambulance_stats['maintenance_ambulances']; ?></h4>
                            <small class="text-muted">Out of service</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Panel -->
            <div class="card bg-grey-900 border-secondary mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-white"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="real_time_monitoring.php" class="btn btn-outline-light w-100">
                                <i class="bi bi-geo-alt-fill me-2"></i>Live Map
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="dispatch_control.php" class="btn btn-outline-light w-100">
                                <i class="bi bi-broadcast me-2"></i>Dispatch
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="communication_center.php" class="btn btn-outline-light w-100">
                                <i class="bi bi-chat-dots-fill me-2"></i>Messages
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="user_management.php" class="btn btn-outline-light w-100">
                                <i class="bi bi-people-fill me-2"></i>Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Emergency Requests Table -->
            <div class="card bg-grey-900 border-secondary">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white"><i class="bi bi-list-ul me-2"></i>Emergency Requests</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-light" onclick="refreshTable()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                            </button>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                                <label class="form-check-label text-muted" for="autoRefresh">Auto-refresh</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Destination</th>
                                    <th>Pickup Address</th>
                                    <th>Assigned Ambulance</th>
                                    <th>Request Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($requests_result->num_rows > 0): ?>
                                    <?php while ($request = $requests_result->fetch_assoc()): ?>
                                        <tr class="fade-in">
                                            <td><strong>#<?php echo $request['requestid']; ?></strong></td>
                                            <td>
                                                <div>
                                                    <strong class="text-white"><?php echo htmlspecialchars($request['firstname'] . ' ' . $request['lastname']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($request['email']); ?></small><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($request['user_phone']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $request['type'] == 'Emergency' ? 'bg-danger' : 'bg-info'; ?>">
                                                    <?php echo $request['type']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong class="text-white"><?php echo htmlspecialchars($request['hospital_name']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($request['phone']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?php echo htmlspecialchars($request['pickup_address']); ?></small>
                                            </td>
                                            <td>
                                                <?php if ($request['vehicle_number']): ?>
                                                    <div>
                                                        <strong class="text-white"><?php echo htmlspecialchars($request['vehicle_number']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($request['driver_name']); ?></small>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">Not assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($request['request_time'])); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge <?php 
                                                    echo $request['status'] == 'Pending' ? 'status-pending' : 
                                                        ($request['status'] == 'En route' ? 'status-enroute' : 'status-completed'); 
                                                ?>">
                                                    <?php echo $request['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <!-- Enhanced status update with ambulance assignment -->
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="request_id" value="<?php echo $request['requestid']; ?>">
                                                        <div class="d-flex gap-1">
                                                            <select name="status" class="form-select form-select-sm" style="width: 120px;">
                                                                <option value="Pending" <?php echo $request['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="En route" <?php echo $request['status'] == 'En route' ? 'selected' : ''; ?>>En route</option>
                                                                <option value="Completed" <?php echo $request['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                            </select>
                                                            <?php if ($request['status'] == 'Pending'): ?>
                                                                <select name="ambulance_id" class="form-select form-select-sm" style="width: 120px;">
                                                                    <option value="">Select Ambulance</option>
                                                                    <?php 
                                                                    $available_ambulances->data_seek(0);
                                                                    while ($ambulance = $available_ambulances->fetch_assoc()): 
                                                                    ?>
                                                                        <option value="<?php echo $ambulance['ambulanceid']; ?>">
                                                                            <?php echo htmlspecialchars($ambulance['vehicle_number']); ?>
                                                                        </option>
                                                                    <?php endwhile; ?>
                                                                </select>
                                                            <?php endif; ?>
                                                            <button type="submit" name="update_status" class="btn btn-sm btn-outline-light">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                                            No requests found
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced JavaScript with GSAP animations -->
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
        
        // Update current time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleString();
        }
        updateTime();
        setInterval(updateTime, 1000);
        
        // Auto-refresh functionality
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
            // Show loading indicator
            const loadingToast = document.createElement('div');
            loadingToast.className = 'toast-container position-fixed top-0 end-0 p-3';
            loadingToast.innerHTML = `
                <div class="toast show" role="alert">
                    <div class="toast-body bg-dark text-white">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refreshing data...
                    </div>
                </div>
            `;
            document.body.appendChild(loadingToast);
            
            setTimeout(() => {
                location.reload();
            }, 1000);
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
        
        // Enhanced hover effects for sidebar
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('mouseenter', () => {
                gsap.to(link, { x: 5, duration: 0.2, ease: 'power2.out' });
            });
            
            link.addEventListener('mouseleave', () => {
                gsap.to(link, { x: 0, duration: 0.2, ease: 'power2.out' });
            });
        });
    </script>
</body>
</html>
