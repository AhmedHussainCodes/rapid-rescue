<?php
// Home page for Rapid Rescue
$page_title = "Home";
include 'includes/header.php';

// Check for logout message
$logout_message = '';
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $logout_message = "You have been successfully logged out.";
}
?>

<!-- Hero Section -->
<section class="hero-section text-center text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="display-4 fw-bold mb-4">Rapid Rescue</h1>
                <p class="lead mb-4">Emergency ambulance service available 24/7. Fast, reliable, and professional medical transportation when you need it most.</p>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="emergency_request.php" class="btn btn-light btn-lg me-3">
                        <i class="bi bi-plus-circle-fill me-2"></i>Request Ambulance
                    </a>
                    <a href="request_tracking.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-geo-alt-fill me-2"></i>Track Request
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-light btn-lg me-3">
                        <i class="bi bi-person-plus-fill me-2"></i>Get Started
                    </a>
                    <a href="login.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($logout_message)): ?>
    <div class="container mt-4">
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($logout_message); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Services Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="display-6 fw-bold text-primary mb-3">Our Services</h2>
                <p class="lead text-muted">Professional emergency medical services designed to save lives and provide peace of mind.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card info-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-lightning-charge-fill"></i>
                        <h5 class="card-title text-primary">Emergency Response</h5>
                        <p class="card-text">Immediate response to life-threatening emergencies with advanced life support equipment and trained paramedics.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card info-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-heart-pulse-fill"></i>
                        <h5 class="card-title text-primary">Medical Transport</h5>
                        <p class="card-text">Safe and comfortable non-emergency medical transportation for routine appointments and transfers.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card info-card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-fill"></i>
                        <h5 class="card-title text-primary">24/7 Availability</h5>
                        <p class="card-text">Round-the-clock service with GPS tracking and real-time updates on ambulance location and status.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-darker">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h3 class="display-6 fw-bold text-primary mb-4">Why Choose Rapid Rescue?</h3>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                            <div>
                                <h6 class="fw-bold">Certified Paramedics</h6>
                                <p class="text-muted mb-0">Highly trained medical professionals with years of emergency experience.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                            <div>
                                <h6 class="fw-bold">Advanced Equipment</h6>
                                <p class="text-muted mb-0">State-of-the-art medical equipment and life support systems in every ambulance.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                            <div>
                                <h6 class="fw-bold">Fast Response Time</h6>
                                <p class="text-muted mb-0">Average response time of under 8 minutes for emergency calls.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                            <div>
                                <h6 class="fw-bold">GPS Tracking</h6>
                                <p class="text-muted mb-0">Real-time tracking so you know exactly when help will arrive.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="/placeholder.svg?height=400&width=600" 
                     alt="Modern ambulance with medical equipment" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Emergency Contact Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-8 mx-auto">
                <h3 class="display-6 fw-bold mb-4">Need Emergency Help?</h3>
                <p class="lead mb-4">In case of a life-threatening emergency, call 911 immediately. For non-emergency medical transport, use our online booking system.</p>
                <div class="row g-3 justify-content-center">
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="bi bi-telephone-fill fs-1 mb-3"></i>
                            <h5>Emergency</h5>
                            <p class="fs-4 fw-bold">911</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="bi bi-headset fs-1 mb-3"></i>
                            <h5>Non-Emergency</h5>
                            <p class="fs-4 fw-bold">(555) 123-4567</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="bi bi-envelope-fill fs-1 mb-3"></i>
                            <h5>Email</h5>
                            <p class="fs-6 fw-bold">help@rapidrescue.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
