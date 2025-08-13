<?php
// Driver Management page - requires admin login
$page_title = "Manage Drivers";
include '../includes/auth_check.php';
requireAdmin(); // Require admin role
include '../includes/db_connect.php';

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_driver'])) {
        // Add new driver
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $phone = trim($_POST['phone']);
        
        if (empty($firstname) || empty($lastname) || empty($phone)) {
            $error_message = "All fields are required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO drivers (firstname, lastname, phone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $firstname, $lastname, $phone);
            
            if ($stmt->execute()) {
                $success_message = "Driver added successfully!";
            } else {
                $error_message = "Failed to add driver.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['update_driver'])) {
        // Update driver
        $driver_id = $_POST['driver_id'];
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $phone = trim($_POST['phone']);
        
        $stmt = $conn->prepare("UPDATE drivers SET firstname = ?, lastname = ?, phone = ? WHERE driverid = ?");
        $stmt->bind_param("sssi", $firstname, $lastname, $phone, $driver_id);
        
        if ($stmt->execute()) {
            $success_message = "Driver updated successfully!";
        } else {
            $error_message = "Failed to update driver.";
        }
        $stmt->close();
    } elseif (isset($_POST['delete_driver'])) {
        // Delete driver
        $driver_id = $_POST['driver_id'];
        
        $stmt = $conn->prepare("DELETE FROM drivers WHERE driverid = ?");
        $stmt->bind_param("i", $driver_id);
        
        if ($stmt->execute()) {
            $success_message = "Driver deleted successfully!";
        } else {
            $error_message = "Failed to delete driver.";
        }
        $stmt->close();
    }
}

// Get all drivers
$drivers_result = $conn->query("SELECT * FROM drivers ORDER BY lastname, firstname");

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
    
    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <!-- Admin Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="bi bi-shield-check-fill me-2"></i>Rapid Rescue Admin
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_ambulances.php">
                            <i class="bi bi-truck-front me-1"></i>Ambulances
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_drivers.php">
                            <i class="bi bi-person-badge me-1"></i>Drivers
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['firstname']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="../index.php"><i class="bi bi-house me-2"></i>View Site</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-primary mb-0"><i class="bi bi-person-badge me-2"></i>Manage Drivers</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDriverModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Driver
                    </button>
                </div>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Drivers Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($drivers_result->num_rows > 0): ?>
                                        <?php while ($driver = $drivers_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $driver['driverid']; ?></td>
                                                <td><strong><?php echo htmlspecialchars($driver['firstname'] . ' ' . $driver['lastname']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($driver['phone']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($driver['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary me-2" 
                                                            onclick="editDriver(<?php echo htmlspecialchars(json_encode($driver)); ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this driver?')">
                                                        <input type="hidden" name="driver_id" value="<?php echo $driver['driverid']; ?>">
                                                        <button type="submit" name="delete_driver" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-person-badge fs-1 mb-3"></i><br>
                                                No drivers found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Driver Modal -->
    <div class="modal fade" id="addDriverModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Driver</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_driver" class="btn btn-primary">Add Driver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Driver Modal -->
    <div class="modal fade" id="editDriverModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Driver</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_driver_id" name="driver_id">
                        <div class="mb-3">
                            <label for="edit_firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit_firstname" name="firstname" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_lastname" name="lastname" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_driver" class="btn btn-primary">Update Driver</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function editDriver(driver) {
            document.getElementById('edit_driver_id').value = driver.driverid;
            document.getElementById('edit_firstname').value = driver.firstname;
            document.getElementById('edit_lastname').value = driver.lastname;
            document.getElementById('edit_phone').value = driver.phone;
            
            new bootstrap.Modal(document.getElementById('editDriverModal')).show();
        }
    </script>
</body>
</html>
