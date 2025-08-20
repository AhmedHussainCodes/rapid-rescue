<?php
// Medical Profile page - requires login
$page_title = "Medical Profile";
include 'includes/auth_check.php'; // Require login
include 'includes/header.php';
include 'includes/db_connect.php';

$success_message = '';
$error_message = '';

// Get existing medical profile
$stmt = $conn->prepare("SELECT * FROM medical_profiles WHERE userid = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$medical_profile = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $allergies = trim($_POST['allergies']);
    $medical_history = trim($_POST['medical_history']);
    $emergency_contact = trim($_POST['emergency_contact']);
    $emergency_contact_phone = trim($_POST['emergency_contact_phone']);
    
    // Basic validation
    if (empty($emergency_contact) && !empty($emergency_contact_phone)) {
        $error_message = "Please provide emergency contact name if phone number is provided.";
    } elseif (!empty($emergency_contact) && empty($emergency_contact_phone)) {
        $error_message = "Please provide emergency contact phone number if name is provided.";
    } elseif (!empty($emergency_contact_phone) && !preg_match('/^[\d\s\-\+$$$$]{10,}$/', $emergency_contact_phone)) {
        $error_message = "Please enter a valid emergency contact phone number.";
    } else {
        if ($medical_profile) {
            // Update existing profile
            $stmt = $conn->prepare("UPDATE medical_profiles SET allergies = ?, medical_history = ?, emergency_contact = ?, emergency_contact_phone = ?, updated_at = CURRENT_TIMESTAMP WHERE userid = ?");
            $stmt->bind_param("ssssi", $allergies, $medical_history, $emergency_contact, $emergency_contact_phone, $_SESSION['user_id']);
        } else {
            // Create new profile
            $stmt = $conn->prepare("INSERT INTO medical_profiles (userid, allergies, medical_history, emergency_contact, emergency_contact_phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $_SESSION['user_id'], $allergies, $medical_history, $emergency_contact, $emergency_contact_phone);
        }
        
        if ($stmt->execute()) {
            $success_message = "Medical profile " . ($medical_profile ? "updated" : "created") . " successfully!";
            
            // Refresh medical profile data
            $stmt2 = $conn->prepare("SELECT * FROM medical_profiles WHERE userid = ?");
            $stmt2->bind_param("i", $_SESSION['user_id']);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $medical_profile = $result2->fetch_assoc();
            $stmt2->close();
        } else {
            $error_message = "Failed to save medical profile. Please try again.";
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-heart-pulse-fill me-2"></i>Medical Profile</h4>
                </div>
                <div class="card-body">
                    <!-- Information Alert -->
                    <div class="alert alert-info mb-4" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle-fill fs-4 me-3 mt-1"></i>
                            <div>
                                <h6 class="alert-heading mb-2">Why provide medical information?</h6>
                                <p class="mb-0">This information helps our paramedics provide better care during emergencies. All data is encrypted and only accessible to authorized medical personnel during emergency situations.</p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <!-- Allergies Section -->
                        <div class="mb-4">
                            <label for="allergies" class="form-label">
                                <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                                <strong class="text-white">Allergies</strong>
                            </label>
                            <textarea class="form-control" id="allergies" name="allergies" rows="3" 
                                      placeholder="List any known allergies (medications, foods, environmental, etc.)&#10;Example: Penicillin, Peanuts, Shellfish, Latex"><?php echo $medical_profile ? htmlspecialchars($medical_profile['allergies']) : ''; ?></textarea>
                            <div class="form-text text-white">
                                <i class="bi bi-lightbulb text-warning me-1"></i>
                                Include severity if known (mild, moderate, severe). Write "None known" if you have no known allergies.
                            </div>
                        </div>
                        
                        <!-- Medical History Section -->
                        <div class="mb-4">
                            <label for="medical_history" class="form-label">
                                <i class="bi bi-clipboard-pulse text-primary me-2"></i>
                                <strong class="text-white">Medical History</strong>
                            </label>
                            <textarea class="form-control" id="medical_history" name="medical_history" rows="4" 
                                      placeholder="List significant medical conditions, surgeries, chronic illnesses, medications, etc.&#10;Example: Diabetes Type 2, High Blood Pressure, Heart Surgery (2020), Taking Metformin daily"><?php echo $medical_profile ? htmlspecialchars($medical_profile['medical_history']) : ''; ?></textarea>
                            <div class="form-text text-white">
                                <i class="bi bi-lightbulb text-warning me-1"></i>
                                Include current medications, past surgeries, chronic conditions, and any ongoing treatments.
                            </div>
                        </div>
                        
                        <!-- Emergency Contact Section -->
                        <div class="card bg-darker mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-white">
                                    <i class="bi bi-person-fill-exclamation text-gray-200 me-2"></i>Emergency Contact
                                </h6>
                                <p class="card-text small text-white mb-3">
                                    Person to contact in case of medical emergency (other than yourself)
                                </p>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="emergency_contact" class="form-label text-white">Contact Name</label>
                                        <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" 
                                               value="<?php echo $medical_profile ? htmlspecialchars($medical_profile['emergency_contact']) : ''; ?>" 
                                               placeholder="Full name of emergency contact">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="emergency_contact_phone" class="form-label text-white">Contact Phone</label>
                                        <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" 
                                               value="<?php echo $medical_profile ? htmlspecialchars($medical_profile['emergency_contact_phone']) : ''; ?>" 
                                               placeholder="(555) 123-4567">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Privacy Notice -->
                        <div class="card bg-darker mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-info text-white">
                                    <i class="bi bi-shield-lock-fill me-2"></i>Privacy & Security
                                </h6>
                                <ul class="small text-white mb-0">
                                    <li>Your medical information is encrypted and stored securely</li>
                                    <li>Only authorized medical personnel can access this data during emergencies</li>
                                    <li>Information is never shared with third parties without your consent</li>
                                    <li>You can update or delete this information at any time</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-outline-secondary me-md-2" onclick="clearForm()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset Form
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?php echo $medical_profile ? 'Update' : 'Save'; ?> Medical Profile
                            </button>
                        </div>
                    </form>
                    
                    <?php if ($medical_profile): ?>
                        <!-- Profile Summary -->
                        <hr class="my-4">
                        <div class="card bg-darker">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>Current Medical Profile Summary
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Last Updated:</strong></p>
                                        <p class="text-muted small"><?php echo date('F j, Y g:i A', strtotime($medical_profile['updated_at'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Profile Status:</strong></p>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Complete
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Tips Card -->
            <div class="card mt-4  mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-lightbulb-fill text-warning me-1"></i>Quick Tips</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-white">For Allergies:</h6>
                            <ul class="small text-white">
                                <li>Include medication names (generic and brand)</li>
                                <li>Specify reaction type (rash, swelling, breathing issues)</li>
                                <li>Note severity level if known</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-white">For Medical History:</h6>
                            <ul class="small text-white">
                                <li>List current medications with dosages</li>
                                <li>Include recent surgeries or procedures</li>
                                <li>Mention chronic conditions and treatments</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearForm() {
    if (confirm('Are you sure you want to reset the form? All unsaved changes will be lost.')) {
        document.getElementById('allergies').value = '<?php echo $medical_profile ? addslashes($medical_profile['allergies']) : ''; ?>';
        document.getElementById('medical_history').value = '<?php echo $medical_profile ? addslashes($medical_profile['medical_history']) : ''; ?>';
        document.getElementById('emergency_contact').value = '<?php echo $medical_profile ? addslashes($medical_profile['emergency_contact']) : ''; ?>';
        document.getElementById('emergency_contact_phone').value = '<?php echo $medical_profile ? addslashes($medical_profile['emergency_contact_phone']) : ''; ?>';
    }
}

// Auto-save draft functionality (optional enhancement)
let autoSaveTimer;
function autoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(function() {
        // Could implement auto-save to localStorage here
        console.log('Auto-save triggered');
    }, 30000); // Auto-save every 30 seconds
}

// Add event listeners for auto-save
document.getElementById('allergies').addEventListener('input', autoSave);
document.getElementById('medical_history').addEventListener('input', autoSave);
document.getElementById('emergency_contact').addEventListener('input', autoSave);
document.getElementById('emergency_contact_phone').addEventListener('input', autoSave);
</script>

<?php include 'includes/footer.php'; ?>
