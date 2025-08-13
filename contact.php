<?php
// Contact Us page
$page_title = "Contact Us";
include 'includes/header.php';
include 'includes/db_connect.php';

$success_message = '';
$error_message = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Insert contact query into database
        $stmt = $conn->prepare("INSERT INTO contact_queries (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            $success_message = "Thank you for your message! We will get back to you within 24 hours.";
            // Clear form data
            $_POST = array();
        } else {
            $error_message = "Failed to send message. Please try again.";
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<div class="container mt-5">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold text-primary mb-4">Contact Us</h1>
            <p class="lead">Get in touch with our team. We're here to help with any questions or concerns.</p>
        </div>
    </div>
    
    <div class="row">
        <!-- Contact Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-envelope-fill me-2"></i>Send us a Message</h4>
                </div>
                <div class="card-body">
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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <select class="form-select" id="subject" name="subject" required>
                                <option value="">Choose a subject...</option>
                                <option value="General Inquiry" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                                <option value="Service Feedback" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Service Feedback') ? 'selected' : ''; ?>>Service Feedback</option>
                                <option value="Billing Question" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Billing Question') ? 'selected' : ''; ?>>Billing Question</option>
                                <option value="Partnership Opportunity" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Partnership Opportunity') ? 'selected' : ''; ?>>Partnership Opportunity</option>
                                <option value="Technical Support" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
                                <option value="Other" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" 
                                      placeholder="Please provide details about your inquiry..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send-fill me-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-primary"><i class="bi bi-geo-alt-fill me-2"></i>Address</h6>
                        <p class="mb-0">123 Emergency Drive<br>Medical City, MC 12345</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-primary"><i class="bi bi-telephone-fill me-2"></i>Phone Numbers</h6>
                        <p class="mb-1"><strong>Emergency:</strong> 911</p>
                        <p class="mb-1"><strong>Non-Emergency:</strong> (555) 123-4567</p>
                        <p class="mb-0"><strong>Administration:</strong> (555) 123-4568</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-primary"><i class="bi bi-envelope-fill me-2"></i>Email</h6>
                        <p class="mb-1">info@rapidrescue.com</p>
                        <p class="mb-0">emergency@rapidrescue.com</p>
                    </div>
                    
                    <div class="mb-0">
                        <h6 class="text-primary"><i class="bi bi-clock-fill me-2"></i>Hours</h6>
                        <p class="mb-1"><strong>Emergency Services:</strong> 24/7</p>
                        <p class="mb-0"><strong>Office Hours:</strong> Mon-Fri 8AM-6PM</p>
                    </div>
                </div>
            </div>
            
            <!-- Emergency Alert -->
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle-fill fs-1 mb-3"></i>
                    <h5>Medical Emergency?</h5>
                    <p class="mb-3">Don't use this form for emergencies. Call immediately:</p>
                    <h3 class="fw-bold">911</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
