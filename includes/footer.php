</div> <!-- Close padding div from header -->

<!-- Enhanced Footer -->
<footer class="bg-black text-white border-t border-white py-5 position-relative overflow-hidden">
    <!-- Background decoration -->
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10">
        <div class="position-absolute" style="top: 20%; left: 10%; width: 100px; height: 100px; background: linear-gradient(135deg, #ffffff, #000000); border-radius: 50%; filter: blur(40px);"></div>
        <div class="position-absolute" style="top: 60%; right: 15%; width: 80px; height: 80px; background: linear-gradient(135deg, #000000, #ffffff); border-radius: 50%; filter: blur(30px);"></div>
    </div>
    
    <div class="container position-relative">
        <div class="row g-4">
            <!-- About Section -->
            <div class="col-lg-4 col-md-6">
                <div class="mb-4">
                    <h5 class="bg-gradient-to-r from-white to-black text-transparent bg-clip-text font-bold mb-3">
                        <i class="bi bi-truck-front-fill me-2"></i>Rapid Rescue
                    </h5>
                    <p class="text-white mb-4">Emergency ambulance service available 24/7. Your health and safety is our priority. Professional medical transportation with certified paramedics and advanced equipment.</p>
                    
                    <!-- Social links -->
                  <div class="d-flex gap-3">
    <a href="https://www.facebook.com/yourpage" target="_blank" class="text-gray-400 hover:text-white hover:underline transition-all">
        <i class="bi bi-facebook fs-5"></i>
    </a>
    <a href="https://twitter.com/yourprofile" target="_blank" class="text-gray-400 hover:text-white hover:underline transition-all">
        <i class="bi bi-twitter fs-5"></i>
    </a>
    <a href="https://www.instagram.com/yourprofile" target="_blank" class="text-gray-400 hover:text-white hover:underline transition-all">
        <i class="bi bi-instagram fs-5"></i>
    </a>
    <a href="https://www.linkedin.com/in/yourprofile" target="_blank" class="text-gray-400 hover:text-white hover:underline transition-all">
        <i class="bi bi-linkedin fs-5"></i>
    </a>
</div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-white font-semibold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="index.php" class="text-gray-400 hover:text-white hover:underline transition-all">
                            <i class="bi bi-house me-2"></i>Home
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="about.php" class="text-gray-400 hover:text-white hover:underline transition-all">
                            <i class="bi bi-info-circle me-2"></i>About Us
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="first_aid.php" class="text-gray-400 hover:text-white hover:underline transition-all">
                            <i class="bi bi-heart-pulse me-2"></i>First Aid
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="contact.php" class="text-gray-400 hover:text-white hover:underline transition-all">
                            <i class="bi bi-envelope me-2"></i>Contact
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Services -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white font-semibold mb-3">Services</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="emergency_request.php" class="text-gray-400 hover:text-white hover:underline transition-all">
                            <i class="bi bi-lightning-charge me-2"></i>Emergency Response
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="request_tracking.php" class="text-gray-400 hover:text-white hover:underline transition-all">
                            <i class="bi bi-geo-alt me-2"></i>Request Tracking
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="medical_profile.php" class="text-gray-400 hover:text-white hover:underline transition-all">
                            <i class="bi bi-person-heart me-2"></i>Medical Profile
                        </a>
                    </li>
                    <li class="mb-2">
                        <span class="text-gray-400 hover:text-white">
                            <a href=""><i class="bi bi-truck me-2"></i>Medical Transport
                            </a>
                        </span>
                    </li>
                </ul>
            </div>
            
            <!-- Emergency Contact -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white font-semibold mb-3">Emergency Contact</h6>
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-black border border-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-telephone-fill text-white"></i>
                        </div>
                        <div>
                            <div class="text-white font-semibold">Emergency</div>
                            <div class="text-white fs-5 font-bold">911</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-black border border-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-headset text-white"></i>
                        </div>
                        <div>
                            <div class="text-white font-semibold">Non-Emergency</div>
                            <div class="text-white">(555) 123-4567</div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="bg-black border border-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-clock-fill text-white"></i>
                        </div>
                        <div>
                            <div class="text-white font-semibold">Availability</div>
                            <div class="text-white font-semibold">24/7 Available</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="border-white my-4">
        
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-white mb-0">
                    &copy; <?php echo date('Y'); ?> Rapid Rescue. All rights reserved. 
                    <span class="text-white">|</span> 
                    <a href="#" class="text-white hover:text-black hover:underline transition-all">Privacy Policy</a>
                    <span class="text-white">|</span>
                    <a href="#" class="text-white hover:text-black hover:underline transition-all">Terms of Service</a>
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-white mb-0">
                    <i class="bi bi-shield-check text-white me-2"></i>
                    Licensed & Certified Emergency Medical Services
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<!-- Custom JS -->
<script src="js/main.js"></script>

<!-- Footer CSS -->
<style>
    footer a {
        text-decoration: none;
        transition: all 0.3s ease;
    }

    footer a:hover {
        text-decoration: underline;
    }

    footer a i {
        transition: transform 0.3s ease;
    }

    footer a:hover i {
        transform: scale(1.2);
    }

    .navbar.scrolled {
        background: rgba(15, 23, 42, 0.95) !important;
        backdrop-filter: blur(20px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }
</style>
