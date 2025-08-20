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
<section class="bg-black text-white py-16">
    <div class="container mx-auto px-4">
        <div class="flex justify-center">
            <div class="max-w-2xl text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">Rapid Rescue</h1>
                <p class="text-lg md:text-xl text-gray-300 mb-8">Emergency ambulance service available 24/7. Fast, reliable, and professional medical transportation when you need it most.</p>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex justify-center gap-4">
                        <a href="emergency_request.php" class="bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
                            <i class="bi bi-plus-circle-fill mr-2"></i>Request Ambulance
                        </a>
                        <a href="request_tracking.php" class="border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-black transition">
                            <i class="bi bi-geo-alt-fill mr-2"></i>Track Request
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex justify-center gap-4">
                        <a href="register.php" class="bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
                            <i class="bi bi-person-plus-fill mr-2"></i>Get Started
                        </a>
                        <a href="login.php" class="border border-white text-white px-6 py-3 rounded-lg font-semibold transition">
                            <i class="bi bi-box-arrow-in-right mr-2"></i>Login
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($logout_message)): ?>
    <div class="container mx-auto px-4 mt-6">
        <div class="bg-green-100 text-green-800 p-4 rounded-lg flex items-center">
            <i class="bi bi-check-circle-fill mr-2"></i><?php echo htmlspecialchars($logout_message); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Services Section -->
<section class="py-16 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-white mb-4">Our Services</h2>
            <p class="text-lg text-white">Professional emergency medical services designed to save lives and provide peace of mind.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-700 p-6 rounded-lg shadow-lg text-center">
                <i class="bi bi-lightning-charge-fill text-4xl text-white mb-4"></i>
                <h5 class="text-xl font-bold text-white mb-3">Emergency Response</h5>
                <p class="text-white">Immediate response to life-threatening emergencies with advanced life support equipment and trained paramedics.</p>
            </div>
            
            <div class="bg-gray-700 p-6 rounded-lg shadow-lg text-center">
                <i class="bi bi-heart-pulse-fill text-4xl text-white mb-4"></i>
                <h5 class="text-xl font-bold text-white mb-3">Medical Transport</h5>
                <p class="text-white">Safe and comfortable non-emergency medical transportation for routine appointments and transfers.</p>
            </div>
            
            <div class="bg-gray-700 p-6 rounded-lg shadow-lg text-center">
                <i class="bi bi-clock-fill text-4xl text-white mb-4"></i>
                <h5 class="text-xl font-bold text-white mb-3">24/7 Availability</h5>
                <p class="text-white">Round-the-clock service with GPS tracking and real-time updates on ambulance location and status.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-black text-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h3 class="text-4xl font-bold mb-6">Why Choose Rapid Rescue?</h3>
                <div class="space-y-6">
                    <div class="flex items-start">
                        <i class="bi bi-check-circle-fill text-green-400 mr-3 text-2xl"></i>
                        <div>
                            <h6 class="font-bold text-lg">Certified Paramedics</h6>
                            <p class="text-gray-300">Highly trained medical professionals with years of emergency experience.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="bi bi-check-circle-fill text-green-400 mr-3 text-2xl"></i>
                        <div>
                            <h6 class="font-bold text-lg">Advanced Equipment</h6>
                            <p class="text-gray-300">State-of-the-art medical equipment and life support systems in every ambulance.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="bi bi-check-circle-fill text-green-400 mr-3 text-2xl"></i>
                        <div>
                            <h6 class="font-bold text-lg">Fast Response Time</h6>
                            <p class="text-gray-300">Average response time of under 8 minutes for emergency calls.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="bi bi-check-circle-fill text-green-400 mr-3 text-2xl"></i>
                        <div>
                            <h6 class="font-bold text-lg">GPS Tracking</h6>
                            <p class="text-gray-300">Real-time tracking so you know exactly when help will arrive.</p>
                        </div>
                    </div>
                </div>
            </div>
          <div class="relative w-full max-w-4xl mx-auto">
    <img src="public/ChatGPT Image Aug 14, 2025, 01_46_09 PM.png" 
         alt="Modern ambulance with medical equipment" 
         class="w-full h-auto rounded-lg shadow-2xl border-4 border-white hover:scale-105 transition-transform duration-300">

    <!-- Optional glowing overlay -->
    <div class="absolute inset-0 rounded-lg ring-4 ring-white opacity-20 pointer-events-none"></div>
</div>

        </div>
    </div>
</section>

<!-- Emergency Contact Section -->
<section class="py-16 bg-gray-900 text-white">
    <div class="container mx-auto px-4">
        <div class="text-center">
            <h3 class="text-4xl font-bold mb-6">Need Emergency Help?</h3>
            <p class="text-lg text-gray-300 mb-8">In case of a life-threatening emergency, call 911 immediately. For non-emergency medical transport, use our online booking system.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6">
                    <i class="bi bi-telephone-fill text-5xl mb-4"></i>
                    <h5 class="text-xl font-bold">Emergency</h5>
                    <p class="text-2xl font-bold">911</p>
                </div>
                <div class="p-6">
                    <i class="bi bi-headset text-5xl mb-4"></i>
                    <h5 class="text-xl font-bold">Non-Emergency</h5>
                    <p class="text-2xl font-bold">(555) 123-4567</p>
                </div>
                <div class="p-6">
                    <i class="bi bi-envelope-fill text-5xl mb-4"></i>
                    <h5 class="text-xl font-bold">Email</h5>
                    <p class="text-lg font-bold">help@rapidrescue.com</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>