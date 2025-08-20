<?php
// About Us page for Rapid Rescue
$page_title = "About Us";
include 'includes/header.php';
?>

<div class="container mt-5">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 font-bold text-white mb-4">About Rapid Rescue</h1>
            <p class="lead text-white">Dedicated to providing exceptional emergency medical services and saving lives in our community.</p>
        </div>
    </div>
    
    <!-- Mission Section -->
    <div class="row mb-5">
        <div class="col-lg-6">
            <img src="public/about/ChatGPT Image Aug 14, 2025, 02_06_24 PM.png" 
                 alt="Paramedics helping patient" class="img-fluid rounded">
        </div>
        <div class="col-lg-6">
            <h2 class="text-white mb-4">Our Mission</h2>
            <p class="mb-4 text-white">At Rapid Rescue, our mission is to provide fast, reliable, and professional emergency medical services to our community. We are committed to delivering the highest quality pre-hospital care with compassion, integrity, and excellence.</p>
            <p class="mb-4 text-white">Founded in 2020, we have been serving the community with state-of-the-art ambulances, highly trained paramedics, and a commitment to saving lives. Our team works around the clock to ensure that help is always just a call away.</p>
            <div class="row g-3">
                <div class="col-6">
                    <div class="text-center p-3 bg-black border border-white rounded">
                        <h4 class="text-white font-bold">500+</h4>
                        <p class="mb-0 text-white">Lives Saved</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-3 bg-black border border-white rounded">
                        <h4 class="text-white font-bold">24/7</h4>
                        <p class="mb-0 text-white">Availability</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Values Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center text-white mb-5">Our Core Values</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 bg-black text-white border border-white">
                        <div class="card-body text-center">
                            <i class="bi bi-heart-fill text-white fs-1 mb-3"></i>
                            <h5 class="card-title text-white">Compassion</h5>
                            <p class="card-text text-white">We treat every patient with empathy, respect, and dignity during their most vulnerable moments.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 bg-black text-white border border-white">
                        <div class="card-body text-center">
                            <i class="bi bi-award-fill text-white fs-1 mb-3"></i>
                            <h5 class="card-title text-white">Excellence</h5>
                            <p class="card-text text-white">We strive for the highest standards in medical care, equipment, and professional service delivery.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 bg-black text-white border border-white">
                        <div class="card-body text-center">
                            <i class="bi bi-shield-fill-check text-white fs-1 mb-3"></i>
                            <h5 class="card-title text-white">Reliability</h5>
                            <p class="card-text text-white">Our community can depend on us to be there when needed, with fast response times and professional care.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Team Section -->
  <div class="py-12 px-4" style="height: 70vh; display: flex; align-items: center;">
    <div class="max-w-2xl mx-auto w-full">
        
        <!-- Simple Header -->
        <div class="text-center mb-8">
            <h2 class="text-4xl font-bold text-white mb-2">Our Team</h2>
            <div class="w-16 h-0.5 bg-white mx-auto"></div>
        </div>

        <!-- Ahmed's Clean Card -->
        <div class="flex justify-center">
            <div class="bg-black border border-white rounded-2xl p-8 max-w-sm w-full text-center group hover:border-gray-300 transition-colors duration-300">
                
                <!-- Simple Profile Circle -->
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-105 transition-transform duration-300">
                    <span class="text-3xl font-bold text-black">A</span>
                </div>
                
                <!-- Content -->
                <h3 class="text-2xl font-semibold text-white mb-2">SYED AHMED</h3>
                <p class="text-gray-300 text-lg mb-3">Founder & Lead</p>
                <p class="text-gray-400 text-sm">Building exceptional digital experiences</p>
                
                <!-- Contact Line -->
                <div class="flex justify-center space-x-4 mt-6">
                    <div class="w-2 h-2 bg-white rounded-full opacity-60 hover:opacity-100 transition-opacity cursor-pointer"></div>
                    <div class="w-2 h-2 bg-white rounded-full opacity-60 hover:opacity-100 transition-opacity cursor-pointer"></div>
                    <div class="w-2 h-2 bg-white rounded-full opacity-60 hover:opacity-100 transition-opacity cursor-pointer"></div>
                </div>
            </div>
        </div>
        
    </div>
</div>
    <!-- Certifications Section -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-black text-white border border-white mb-6">
                <div class="card-body text-center py-5">
                    <h3 class="text-white mb-4">Certifications & Accreditations</h3>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <i class="bi bi-patch-check-fill text-white fs-1 mb-2"></i>
                            <h6 class="text-white">State Licensed</h6>
                            <p class="small text-white">Licensed by State Health Department</p>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-award-fill text-white fs-1 mb-2"></i>
                            <h6 class="text-white">CAAS Accredited</h6>
                            <p class="small text-white">Commission on Accreditation of Ambulance Services</p>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-heart-pulse-fill text-white fs-1 mb-2"></i>
                            <h6 class="text-white">AHA Certified</h6>
                            <p class="small text-white">American Heart Association Training Center</p>
                        </div>
                        <div class="col-md-3">
                            <i class="bi bi-shield-fill-check text-white fs-1 mb-2"></i>
                            <h6 class="text-white">HIPAA Compliant</h6>
                            <p class="small text-white">Full patient privacy protection</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>