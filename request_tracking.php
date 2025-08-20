<?php
// Request Tracking page - requires login
$page_title = "Track Request";
include 'includes/auth_check.php'; // Require login
include 'includes/header.php';
include 'includes/db_connect.php';

// Get user's requests
$stmt = $conn->prepare("SELECT * FROM requests WHERE userid = ? ORDER BY request_time DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

// Function to get status badge class
function getStatusBadgeClass($status) {
    switch($status) {
        case 'Pending': return 'status-pending';
        case 'En route': return 'status-enroute';
        case 'Completed': return 'status-completed';
        default: return 'bg-secondary';
    }
}

// Function to get status icon
function getStatusIcon($status) {
    switch($status) {
        case 'Pending': return 'bi-clock-fill';
        case 'En route': return 'bi-truck-front-fill';
        case 'Completed': return 'bi-check-circle-fill';
        default: return 'bi-question-circle-fill';
    }
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Track Your Requests</h2>
                <a href="emergency_request.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>New Request
                </a>
            </div>
            
            <?php if (empty($requests)): ?>
                <!-- No requests found -->
                <div class="card mb-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox fs-1 text-white mb-3"></i>
                        <h5 class="text-white">No Requests Found</h5>
                        <p class="text-white mb-4">You haven't submitted any ambulance requests yet.</p>
                        <a href="emergency_request.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Submit Your First Request
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Active/Recent Requests -->
                <?php 
                $active_requests = array_filter($requests, function($req) { 
                    return $req['status'] !== 'Completed'; 
                });
                $completed_requests = array_filter($requests, function($req) { 
                    return $req['status'] === 'Completed'; 
                });
                ?>
                
                <?php if (!empty($active_requests)): ?>
                    <div class="mb-5">
                        <h4 class="text-warning mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Active Requests</h4>
                        <?php foreach ($active_requests as $request): ?>
                            <div class="card mb-3 border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-0">
                                                <i class="bi bi-hash me-1"></i>Request ID: <?php echo $request['requestid']; ?>
                                                <span class="badge <?php echo getStatusBadgeClass($request['status']); ?> ms-2">
                                                    <i class="bi <?php echo getStatusIcon($request['status']); ?> me-1"></i>
                                                    <?php echo $request['status']; ?>
                                                </span>
                                            </h6>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <small><?php echo date('M j, Y g:i A', strtotime($request['request_time'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Request Details</h6>
                                            <p class="mb-1"><strong>Type:</strong> 
                                                <span class="badge <?php echo $request['type'] == 'Emergency' ? 'bg-danger' : 'bg-info'; ?>">
                                                    <?php echo $request['type']; ?>
                                                </span>
                                            </p>
                                            <p class="mb-1"><strong>Destination:</strong> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                                            <p class="mb-0"><strong>Pickup:</strong> <?php echo htmlspecialchars($request['pickup_address']); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Status Information</h6>
                                            <?php if ($request['status'] == 'Pending'): ?>
                                                <div class="alert alert-warning py-2">
                                                    <i class="bi bi-clock-fill me-2"></i>
                                                    <small>Your request is being processed. An ambulance will be dispatched shortly.</small>
                                                </div>
                                            <?php elseif ($request['status'] == 'En route'): ?>
                                                <div class="alert alert-info py-2">
                                                    <i class="bi bi-truck-front-fill me-2"></i>
                                                    <small>Ambulance is on the way to your location. Estimated arrival: 5-10 minutes.</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Map placeholder for active requests -->
                                    <?php if ($request['status'] == 'En route'): ?>
                                        <div class="mt-3">
                                            <h6 class="text-primary">Live Tracking</h6>
                                            <div class="map-container">
                                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.1!2d-74.0059!3d40.7128!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDQyJzQ2LjEiTiA3NMKwMDAnMjEuMiJX!5e0!3m2!1sen!2sus!4v1234567890" 
                                                        allowfullscreen="" loading="lazy"></iframe>
                                            </div>
                                            <div class="mt-2 text-center">
                                                <small class="text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Map shows approximate ambulance location. Updates every 30 seconds.
                                                </small>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Completed Requests -->
                <?php if (!empty($completed_requests)): ?>
                    <div class="mb-4">
                        <h4 class="text-success mb-3"><i class="bi bi-check-circle-fill me-2"></i>Recent Completed Requests</h4>
                        <?php foreach (array_slice($completed_requests, 0, 5) as $request): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1">
                                                <i class="bi bi-hash me-1"></i>Request ID: <?php echo $request['requestid']; ?>
                                                <span class="badge <?php echo getStatusBadgeClass($request['status']); ?> ms-2">
                                                    <i class="bi <?php echo getStatusIcon($request['status']); ?> me-1"></i>
                                                    <?php echo $request['status']; ?>
                                                </span>
                                            </h6>
                                            <p class="mb-0 text-muted">
                                                <strong><?php echo $request['type']; ?></strong> - 
                                                <?php echo htmlspecialchars($request['hospital_name']); ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <small class="text-muted">
                                                <?php echo date('M j, Y g:i A', strtotime($request['request_time'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($completed_requests) > 5): ?>
                            <div class="text-center">
                                <button class="btn btn-outline-primary" onclick="toggleOlderRequests()">
                                    <i class="bi bi-chevron-down me-2"></i>Show Older Requests
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- Help Section -->
            <div class="card bg-darker mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-white">Need Help?</h6>
                            <p class="mb-0 text-white">If you have questions about your request or need to make changes, contact our support team.</p>
                        </div>
                       <div class="col-md-4 text-end">
   <i class="bi bi-headset text-white"></i> <a href="contact.php" class="contact-link">
        Contact Support
    </a>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .contact-link {
        color: white;
        text-decoration: none;
        position: relative;
        transition: color 0.3s ease;
        font-weight: 500;
    }

    /* Underline effect */
    .contact-link::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -3px;
        width: 0;
        height: 2px;
        background-color: white;
        transition: width 0.3s ease;
    }

    /* Hover effects */
    .contact-link:hover {
        color: #f5f5f5; /* Slightly brighter white */
    }
    .contact-link:hover::after {
        width: 100%;
    }
    .contact-link i {
        transition: transform 0.3s ease;
    }
    .contact-link:hover i {
        transform: scale(1.15);
    }
</style>

<script>
// Auto-refresh page every 30 seconds for active requests
<?php if (!empty($active_requests)): ?>
setTimeout(function() {
    location.reload();
}, 30000);
<?php endif; ?>

function toggleOlderRequests() {
    // This would show/hide older completed requests
    alert('Feature to show older requests would be implemented here');
}
</script>

<?php include 'includes/footer.php'; ?>
