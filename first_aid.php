<?php
// First Aid Instructions page
$page_title = "First Aid Instructions";
include 'includes/header.php';
?>

<div class="container mt-5">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold text-primary mb-4">First Aid Instructions</h1>
            <p class="lead">Essential first aid knowledge that could save a life. Learn basic emergency response techniques.</p>
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Important:</strong> These instructions are for educational purposes only. In case of serious emergency, call 911 immediately.
            </div>
        </div>
    </div>
    
    <!-- CPR Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0"><i class="bi bi-heart-pulse-fill me-2"></i>CPR (Cardiopulmonary Resuscitation)</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <h5 class="text-primary">When to use CPR:</h5>
                            <ul class="mb-4">
                                <li>Person is unconscious and not breathing normally</li>
                                <li>No pulse can be detected</li>
                                <li>Person is unresponsive to verbal or physical stimuli</li>
                            </ul>
                            
                            <h5 class="text-primary">Steps for Adult CPR:</h5>
                            <ol class="mb-4">
                                <li><strong>Check responsiveness:</strong> Tap shoulders and shout "Are you okay?"</li>
                                <li><strong>Call for help:</strong> Call 911 and ask for an AED if available</li>
                                <li><strong>Position:</strong> Place person on firm, flat surface. Tilt head back, lift chin</li>
                                <li><strong>Hand placement:</strong> Place heel of one hand on center of chest between nipples</li>
                                <li><strong>Compressions:</strong> Push hard and fast at least 2 inches deep, 100-120 per minute</li>
                                <li><strong>Rescue breaths:</strong> Give 2 breaths after every 30 compressions</li>
                                <li><strong>Continue:</strong> Keep going until emergency services arrive</li>
                            </ol>
                        </div>
                        <div class="col-lg-4">
                            <img src="/placeholder.svg?height=300&width=400" 
                                 alt="CPR demonstration" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Choking Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Choking (Heimlich Maneuver)</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <h5 class="text-primary">Signs of choking:</h5>
                            <ul class="mb-4">
                                <li>Cannot speak, cough, or breathe</li>
                                <li>Hands clutching throat</li>
                                <li>Skin turning blue or gray</li>
                                <li>Loss of consciousness</li>
                            </ul>
                            
                            <h5 class="text-primary">For conscious adults and children over 1 year:</h5>
                            <ol class="mb-4">
                                <li><strong>Stand behind the person</strong></li>
                                <li><strong>Place arms around waist</strong></li>
                                <li><strong>Make a fist</strong> with one hand, place thumb side against stomach above navel</li>
                                <li><strong>Grasp fist</strong> with other hand</li>
                                <li><strong>Give quick upward thrusts</strong> into the abdomen</li>
                                <li><strong>Continue</strong> until object is expelled or person becomes unconscious</li>
                            </ol>
                            
                            <div class="alert alert-info">
                                <strong>For infants under 1 year:</strong> Use back blows and chest thrusts instead of abdominal thrusts.
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <img src="/placeholder.svg?height=300&width=400" 
                                 alt="Heimlich maneuver" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bleeding Control Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0"><i class="bi bi-droplet-fill me-2"></i>Severe Bleeding Control</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <h5 class="text-primary">Steps to control bleeding:</h5>
                            <ol class="mb-4">
                                <li><strong>Protect yourself:</strong> Wear gloves if available</li>
                                <li><strong>Apply direct pressure:</strong> Use clean cloth or bandage directly on wound</li>
                                <li><strong>Maintain pressure:</strong> Don't remove cloth even if blood soaks through</li>
                                <li><strong>Elevate:</strong> Raise injured area above heart level if possible</li>
                                <li><strong>Apply pressure points:</strong> If bleeding continues, apply pressure to artery</li>
                                <li><strong>Treat for shock:</strong> Keep person warm and lying down</li>
                            </ol>
                            
                            <div class="alert alert-danger">
                                <strong>Never remove objects</strong> embedded in wounds. Stabilize them and seek immediate medical help.
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <img src="/placeholder.svg?height=300&width=400" 
                                 alt="Bleeding control" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Burns Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0"><i class="bi bi-fire me-2"></i>Burns Treatment</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="text-primary">Minor Burns (1st degree):</h5>
                            <ul class="mb-4">
                                <li>Cool with cold water for 10-15 minutes</li>
                                <li>Remove jewelry before swelling occurs</li>
                                <li>Apply aloe vera or moisturizer</li>
                                <li>Cover with sterile bandage</li>
                                <li>Take over-the-counter pain medication</li>
                            </ul>
                        </div>
                        <div class="col-lg-6">
                            <h5 class="text-primary">Severe Burns (2nd/3rd degree):</h5>
                            <ul class="mb-4">
                                <li><strong>Call 911 immediately</strong></li>
                                <li>Do NOT remove burned clothing</li>
                                <li>Do NOT apply ice or butter</li>
                                <li>Cover with clean, dry cloth</li>
                                <li>Treat for shock</li>
                                <li>Monitor breathing</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Emergency Numbers -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-5">
                    <h3 class="mb-4">Emergency Contact Numbers</h3>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <i class="bi bi-telephone-fill fs-1 mb-3"></i>
                            <h5>Emergency Services</h5>
                            <p class="fs-3 fw-bold">911</p>
                        </div>
                        <div class="col-md-4">
                            <i class="bi bi-info-circle-fill fs-1 mb-3"></i>
                            <h5>Poison Control</h5>
                            <p class="fs-5 fw-bold">1-800-222-1222</p>
                        </div>
                        <div class="col-md-4">
                            <i class="bi bi-truck-front-fill fs-1 mb-3"></i>
                            <h5>Rapid Rescue</h5>
                            <p class="fs-5 fw-bold">(555) 123-4567</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
