<?php
session_start();
// Emergency Request page - requires login
$page_title = "Request Ambulance";
include 'includes/auth_check.php'; // Require login
include 'includes/header.php';
include 'includes/db_connect.php';

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hospital_name = trim($_POST['hospital_name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $pickup_address = trim($_POST['pickup_address']);
    $type = $_POST['type'];
    $notes = trim($_POST['notes']);
    
    // Basic validation
    if (empty($hospital_name) || empty($address) || empty($phone) || empty($pickup_address) || empty($type)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!preg_match('/^[\d\s\-\+$$$$]{10,}$/', $phone)) {
        $error_message = "Please enter a valid phone number.";
    } else {
        // Insert emergency request
        $stmt = $conn->prepare("INSERT INTO requests (userid, hospital_name, address, phone, pickup_address, type, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("isssss", $_SESSION['user_id'], $hospital_name, $address, $phone, $pickup_address, $type);
        
        if ($stmt->execute()) {
            $request_id = $conn->insert_id;
            $success_message = "Emergency request submitted successfully! Request ID: #" . $request_id;
            
            // Clear form data after successful submission
            $_POST = array();
            
            // Redirect to tracking page after 3 seconds
            header("refresh:3;url=request_tracking.php");
        } else {
            $error_message = "Failed to submit request. Please try again.";
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Emergency Alert -->
            <div class="alert alert-danger mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Life-Threatening Emergency?</h5>
                        <p class="mb-0">If this is a life-threatening emergency, <strong>call 911 immediately</strong> instead of using this form.</p>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Request Ambulance Service</h4>
                </div>
                <div class="card-body">
                   <?php if (!empty($success_message)): ?>
    <div class="alert alert-success" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success_message); ?>
        <div class="mt-2">
            <small>You will be redirected to the tracking page in a few seconds...</small>
        </div>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "request_tracking.php";
        }, 3000); // 3 seconds
    </script>
<?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="emergencyForm">
                        <!-- Request Type -->
                        <div class="mb-4">
                            <label for="type" class="form-label text-white">Request Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select request type...</option>
                                <option value="Emergency" <?php echo (isset($_POST['type']) && $_POST['type'] == 'Emergency') ? 'selected' : ''; ?>>
                                    Emergency (Life-threatening condition)
                                </option>
                                <option value="Non-Emergency" <?php echo (isset($_POST['type']) && $_POST['type'] == 'Non-Emergency') ? 'selected' : ''; ?>>
                                    Non-Emergency (Medical transport)
                                </option>
                            </select>
                        </div>
                        
                        <!-- Hospital Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hospital_name" class="form-label text-white">Hospital/Destination Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="hospital_name" name="hospital_name" 
                                       value="<?php echo isset($_POST['hospital_name']) ? htmlspecialchars($_POST['hospital_name']) : ''; ?>" 
                                       placeholder="e.g., City General Hospital" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label text-white">Hospital Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" 
                                       placeholder="(555) 123-4567" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label text-white">Hospital/Destination Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="2" 
                                      placeholder="Full address including street, city, state, zip code" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                        </div>
                        
                        <!-- Pickup Information -->
                        <div class="mb-3">
                            <label for="pickup_address" class="form-label text-white">Pickup Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="pickup_address" name="pickup_address" rows="2" 
                                      placeholder="Where should the ambulance pick you up?" required><?php echo isset($_POST['pickup_address']) ? htmlspecialchars($_POST['pickup_address']) : ''; ?></textarea>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Provide detailed pickup location including apartment/unit number if applicable
                            </div>
                        </div>
                        
                        <!-- Additional Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label text-white">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Any special instructions, medical conditions, or accessibility requirements..."><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                        </div>
                        
                        <!-- Patient Information Display -->
                        <div class="card bg-darker mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-white">Patient Information (from your profile)</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1 text-white"><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']); ?></p>
                                        <p class="mb-0 text-white"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-0">
                                            <a href="profile.php" class="btn btn-sm hover:text-white text-gray-300">
                                                <i class="bi bi-pencil me-1 text-gray-300"></i>Update Profile
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Terms and Submit -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label text-white" for="terms">
                                    I understand that this request will be processed as quickly as possible and I agree to the 
                                    <a href="#" class="text-gray-400">terms of service</a>.
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send-fill me-2"></i>Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Information Cards -->
            <div class="row mt-4">
                <div class="col-md-6 mb-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-fill text-white fs-1 mb-3"></i>
                            <h6 class="text-white">Average Response Time</h6>
                            <p class="text-gray-500 mb-0">Emergency: 6-8 minutes<br>Non-Emergency: 15-20 minutes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="bi bi-geo-alt-fill text-white fs-1 mb-3"></i>
                            <h6 class="text-white">Real-Time Tracking</h6>
                            <p class="text-gray-500 mb-0">Track your ambulance location and estimated arrival time</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
