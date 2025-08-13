<?php
// User Management - View and manage all users
$page_title = "User Management";
include '../includes/auth_check.php';
requireAdmin();
include '../includes/db_connect.php';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['toggle_status'])) {
        $user_id = $_POST['user_id'];
        $new_status = $_POST['new_status'];
        
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE userid = ?");
        $stmt->bind_param("si", $new_status, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "User status updated successfully!";
        } else {
            $error_message = "Failed to update user status.";
        }
        $stmt->close();
    }
}

// Get all users with their request statistics
$users_query = "
    SELECT u.*, 
           COUNT(r.requestid) as total_requests,
           MAX(r.request_time) as last_request
    FROM users u 
    LEFT JOIN requests r ON u.userid = r.userid 
    WHERE u.role != 'admin'
    GROUP BY u.userid 
    ORDER BY u.created_at DESC
";
$users_result = $conn->query($users_query);

// Get user statistics
$user_stats = $conn->query("
    SELECT 
        COUNT(*) as total_users,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_users,
        SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as new_users
    FROM users WHERE role != 'admin'
")->fetch_assoc();

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
                        <a class="nav-link text-white bg-secondary rounded active" href="user_management.php">
                            <i class="bi bi-people-fill me-2"></i>User Management
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white mb-0"><i class="bi bi-people-fill me-2"></i>User Management</h2>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control" placeholder="Search users..." id="userSearch" style="width: 250px;">
                    <button class="btn btn-outline-light" onclick="exportUsers()">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
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
            
            <!-- User Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-grey-900 border-secondary slide-up">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-1">Total Users</h6>
                                    <h3 class="mb-0 text-white"><?php echo $user_stats['total_users']; ?></h3>
                                </div>
                                <i class="bi bi-people fs-1 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-grey-900 border-secondary slide-up">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-1">Active Users</h6>
                                    <h3 class="mb-0 text-success"><?php echo $user_stats['active_users']; ?></h3>
                                </div>
                                <i class="bi bi-person-check fs-1 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-grey-900 border-secondary slide-up">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-1">Inactive Users</h6>
                                    <h3 class="mb-0 text-danger"><?php echo $user_stats['inactive_users']; ?></h3>
                                </div>
                                <i class="bi bi-person-x fs-1 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-grey-900 border-secondary slide-up">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-1">New Users (30d)</h6>
                                    <h3 class="mb-0 text-info"><?php echo $user_stats['new_users']; ?></h3>
                                </div>
                                <i class="bi bi-person-plus fs-1 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Users Table -->
            <div class="card bg-grey-900 border-secondary">
                <div class="card-header">
                    <h5 class="mb-0 text-white"><i class="bi bi-table me-2"></i>All Users</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0" id="usersTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Requests</th>
                                    <th>Last Request</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($users_result->num_rows > 0): ?>
                                    <?php while ($user = $users_result->fetch_assoc()): ?>
                                        <tr class="fade-in">
                                            <td><strong>#<?php echo $user['userid']; ?></strong></td>
                                            <td>
                                                <div>
                                                    <strong class="text-white"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></strong><br>
                                                    <small class="text-muted">DOB: <?php echo date('M j, Y', strtotime($user['dob'])); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="text-info">
                                                    <?php echo htmlspecialchars($user['email']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="tel:<?php echo htmlspecialchars($user['phone']); ?>" class="text-info">
                                                    <?php echo htmlspecialchars($user['phone']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo $user['total_requests']; ?></span>
                                            </td>
                                            <td>
                                                <?php if ($user['last_request']): ?>
                                                    <small class="text-muted"><?php echo date('M j, Y', strtotime($user['last_request'])); ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">Never</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $user['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo ucfirst($user['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['userid']; ?>">
                                                        <input type="hidden" name="new_status" value="<?php echo $user['status'] == 'active' ? 'inactive' : 'active'; ?>">
                                                        <button type="submit" name="toggle_status" class="btn btn-sm <?php echo $user['status'] == 'active' ? 'btn-outline-danger' : 'btn-outline-success'; ?>" 
                                                                onclick="return confirm('Are you sure you want to <?php echo $user['status'] == 'active' ? 'deactivate' : 'activate'; ?> this user?')">
                                                            <i class="bi bi-<?php echo $user['status'] == 'active' ? 'person-x' : 'person-check'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-sm btn-outline-light" onclick="viewUserDetails(<?php echo $user['userid']; ?>)">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-people fs-1 mb-3 d-block"></i>
                                            No users found
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // GSAP animations
        gsap.fromTo('.slide-up', 
            { opacity: 0, y: 30 }, 
            { opacity: 1, y: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out' }
        );
        
        gsap.fromTo('.fade-in', 
            { opacity: 0 }, 
            { opacity: 1, duration: 0.8, stagger: 0.05, ease: 'power2.out', delay: 0.3 }
        );
        
        // Search functionality
        document.getElementById('userSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    gsap.fromTo(row, { opacity: 0 }, { opacity: 1, duration: 0.3 });
                } else {
                    gsap.to(row, { opacity: 0, duration: 0.2, onComplete: () => row.style.display = 'none' });
                }
            });
        });
        
        // Export users function
        function exportUsers() {
            // In a real implementation, this would generate a CSV/Excel file
            alert('Export functionality would be implemented here');
        }
        
        // View user details
        function viewUserDetails(userId) {
            // In a real implementation, this would open a modal with detailed user information
            alert('User details modal would open for user ID: ' + userId);
        }
    </script>
</body>
</html>
