<?php
// Ambulance Management page - requires admin login
$page_title = "Manage Ambulances";
include '../includes/auth_check.php';
requireAdmin(); // Require admin role
include '../includes/db_connect.php';

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_ambulance'])) {
        // Add new ambulance
        $vehicle_number = trim($_POST['vehicle_number']);
        $equipment_level = $_POST['equipment_level'];
        $status = $_POST['status'];
        
        if (empty($vehicle_number)) {
            $error_message = "Vehicle number is required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO ambulances (vehicle_number, equipment_level, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $vehicle_number, $equipment_level, $status);
            
            if ($stmt->execute()) {
                $success_message = "Ambulance added successfully!";
            } else {
                $error_message = "Failed to add ambulance. Vehicle number may already exist.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['update_ambulance'])) {
        // Update ambulance
        $ambulance_id = $_POST['ambulance_id'];
        $vehicle_number = trim($_POST['vehicle_number']);
        $equipment_level = $_POST['equipment_level'];
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE ambulances SET vehicle_number = ?, equipment_level = ?, status = ? WHERE ambulanceid = ?");
        $stmt->bind_param("sssi", $vehicle_number, $equipment_level, $status, $ambulance_id);
        
        if ($stmt->execute()) {
            $success_message = "Ambulance updated successfully!";
        } else {
            $error_message = "Failed to update ambulance.";
        }
        $stmt->close();
    } elseif (isset($_POST['delete_ambulance'])) {
        // Delete ambulance
        $ambulance_id = $_POST['ambulance_id'];
        
        $stmt = $conn->prepare("DELETE FROM ambulances WHERE ambulanceid = ?");
        $stmt->bind_param("i", $ambulance_id);
        
        if ($stmt->execute()) {
            $success_message = "Ambulance deleted successfully!";
        } else {
            $error_message = "Failed to delete ambulance.";
        }
        $stmt->close();
    }
}

// Get all ambulances
$ambulances_result = $conn->query("SELECT * FROM ambulances ORDER BY vehicle_number");

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
                        <a class="nav-link active" href="manage_ambulances.php">
                            <i class="bi bi-truck-front me-1"></i>Ambulances
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_drivers.php">
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
                    <h2 class="text-primary mb-0"><i class="bi bi-truck-front me-2"></i>Manage Ambulances</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAmbulanceModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Ambulance
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
                
                <!-- Ambulances Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Vehicle Number</th>
                                        <th>Equipment Level</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambulances_result->num_rows > 0): ?>
                                        <?php while ($ambulance = $ambulances_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $ambulance['ambulanceid']; ?></td>
                                                <td><strong><?php echo htmlspecialchars($ambulance['vehicle_number']); ?></strong></td>
                                                <td>
                                                    <span class="badge <?php echo $ambulance['equipment_level'] == 'Advanced' ? 'bg-success' : 'bg-info'; ?>">
                                                        <?php echo $ambulance['equipment_level']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?php 
                                                        echo $ambulance['status'] == 'Available' ? 'bg-success' : 
                                                            ($ambulance['status'] == 'On call' ? 'bg-warning text-dark' : 'bg-danger'); 
                                                    ?>">
                                                        <?php echo $ambulance['status']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($ambulance['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary me-2" 
                                                            onclick="editAmbulance(<?php echo htmlspecialchars(json_encode($ambulance)); ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this ambulance?')">
                                                        <input type="hidden" name="ambulance_id" value="<?php echo $ambulance['ambulanceid']; ?>">
                                                        <button type="submit" name="delete_ambulance" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="bi bi-truck-front fs-1 mb-3"></i><br>
                                                No ambulances found
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

    <!-- Add Ambulance Modal -->
    <div class="modal fade" id="addAmbulanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Ambulance</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="vehicle_number" class="form-label">Vehicle Number</label>
                            <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="equipment_level" class="form-label">Equipment Level</label>
                            <select class="form-select" id="equipment_level" name="equipment_level" required>
                                <option value="Basic">Basic</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Available">Available</option>
                                <option value="On call">On call</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_ambulance" class="btn btn-primary">Add Ambulance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Ambulance Modal -->
    <div class="modal fade" id="editAmbulanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Ambulance</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_ambulance_id" name="ambulance_id">
                        <div class="mb-3">
                            <label for="edit_vehicle_number" class="form-label">Vehicle Number</label>
                            <input type="text" class="form-control" id="edit_vehicle_number" name="vehicle_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_equipment_level" class="form-label">Equipment Level</label>
                            <select class="form-select" id="edit_equipment_level" name="equipment_level" required>
                                <option value="Basic">Basic</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="Available">Available</option>
                                <option value="On call">On call</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_ambulance" class="btn btn-primary">Update Ambulance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function editAmbulance(ambulance) {
            document.getElementById('edit_ambulance_id').value = ambulance.ambulanceid;
            document.getElementById('edit_vehicle_number').value = ambulance.vehicle_number;
            document.getElementById('edit_equipment_level').value = ambulance.equipment_level;
            document.getElementById('edit_status').value = ambulance.status;
            
            new bootstrap.Modal(document.getElementById('editAmbulanceModal')).show();
        }
    </script>
</body>
</html>
