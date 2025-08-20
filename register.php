<?php
ob_start();
session_start();
// Registration page for Rapid Rescue
$page_title = "Register";
include 'includes/header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Handle form submission
$error_message = '';
$form_data = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'includes/db_connect.php';
    
    // Get and sanitize form data
    $form_data['firstname'] = trim($_POST['firstname']);
    $form_data['lastname'] = trim($_POST['lastname']);
    $form_data['email'] = trim($_POST['email']);
    $form_data['phone'] = trim($_POST['phone']);
    $form_data['dob'] = $_POST['dob'];
    $form_data['address'] = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($form_data['firstname']) || empty($form_data['lastname']) || empty($form_data['email']) || 
        empty($form_data['phone']) || empty($form_data['dob']) || empty($form_data['address']) || 
        empty($password) || empty($confirm_password)) {
        $error_message = "Please fill in all fields.";
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT userid FROM users WHERE email = ?");
        $stmt->bind_param("s", $form_data['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = "An account with this email already exists.";
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
       $stmt = $conn->prepare("
    INSERT INTO users (firstname, lastname, email, phone, password, dob, address, role, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'user', 'active')
");
$stmt->bind_param(
    "sssssss", 
    $form_data['firstname'], 
    $form_data['lastname'], 
    $form_data['email'], 
    $form_data['phone'], 
    $hashed_password, 
    $form_data['dob'], 
    $form_data['address']
);

            
            if ($stmt->execute()) {
                // Registration successful
                header('Location: login.php?registered=1');
                exit();
            } else {
                $error_message = "Registration failed. Please try again.";
            }
        }
        
        $stmt->close();
    }
    
    $conn->close();
}
ob_end_flush();
?>
<div class="container d-flex align-items-center justify-content-center mt-4 min-vh-100">
    <div class="col-md-6 col-lg-5">
        <div class="card register-card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header text-center bg-black text-white py-2">
                <h4 class="fw-bold mb-0"><i class="bi bi-person-plus me-2"></i>Create Account</h4>
                <small class="text-black opacity-75">Join Rapid Rescue today</small>
            </div>
            <div class="card-body p-4 bg-dark text-white">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-light border border-danger text-danger d-flex align-items-center small mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <span><?php echo htmlspecialchars($error_message); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" id="registerForm" class="small">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm bg-black text-white border border-light rounded-pill" 
                                   id="firstname" name="firstname" placeholder="First Name" 
                                   value="<?php echo isset($form_data['firstname']) ? htmlspecialchars($form_data['firstname']) : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm bg-black text-white border border-light rounded-pill" 
                                   id="lastname" name="lastname" placeholder="Last Name" 
                                   value="<?php echo isset($form_data['lastname']) ? htmlspecialchars($form_data['lastname']) : ''; ?>" required>
                        </div>
                    </div>

                    <input type="email" class="form-control form-control-sm bg-black text-white border border-light rounded-pill mt-2" 
                           id="email" name="email" placeholder="Email" 
                           value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>" required>

                    <input type="tel" class="form-control form-control-sm bg-black text-white border border-light rounded-pill mt-2" 
                           id="phone" name="phone" placeholder="Phone Number" 
                           value="<?php echo isset($form_data['phone']) ? htmlspecialchars($form_data['phone']) : ''; ?>" required>

                    <input type="date" class="form-control form-control-sm bg-black text-white border border-light rounded-pill mt-2" 
                           id="dob" name="dob" value="<?php echo isset($form_data['dob']) ? htmlspecialchars($form_data['dob']) : ''; ?>" required>

                    <textarea class="form-control form-control-sm bg-black text-white border border-light rounded-3 mt-2" 
                              id="address" name="address" rows="2" placeholder="Address" required><?php echo isset($form_data['address']) ? htmlspecialchars($form_data['address']) : ''; ?></textarea>
<div class="row g-2 mt-1">
  <!-- Password -->
  <div class="col-md-6 position-relative">
    <input type="password" 
           class="form-control form-control-sm bg-black text-white border border-light rounded-pill pe-5" 
           id="password" placeholder="Password" name="password" required>
    <i class="bi bi-eye position-absolute bottom-4 end-0 translate-middle-y me-3 fs-6 toggle-password" 
       data-target="password" style="cursor: pointer;"></i>
    <small class="text-secondary">Min 6 characters</small>
  </div>

  <!-- Confirm Password -->
  <div class="col-md-6 position-relative">
    <input type="password" 
           class="form-control form-control-sm bg-black text-white border border-light rounded-pill" 
           id="confirm_password" placeholder="Confirm Password" name="confirm_password" required>
    <i class="bi bi-eye position-absolute bottom-4 end-0 translate-middle-y me-3 fs-6 toggle-password" 
       data-target="confirm_password" style="cursor: pointer;"></i>
  </div>
</div>

                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-light text-black fw-bold rounded-pill py-2 shadow-sm">
                            <i class="bi bi-person-plus me-1"></i>Create Account
                        </button>
                    </div>
                </form>

                <hr class="my-3 border-light opacity-50">

                <div class="text-center small">
                    <p class=" text-light opacity-75"><i class="bi bi-box-arrow-in-right me-1"></i>Have an account?
                 <a href="login.php" class="fw-bold text-white register-link">
                        Login
                    </a>
                </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .register-card {
        animation: fadeIn 0.5s ease-in-out;
    }
    .register-link {
        text-decoration: none;
        transition: 0.3s ease-in-out;
    }
    .register-link:hover {
        text-decoration: underline;
        color: #ccc;
    }
    .form-control:focus {
        background-color: #111 !important;
        color: #fff;
        box-shadow: none;
        border-color: white;
    }
    .btn:hover {
        transform: translateY(-2px);
        transition: 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.98); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
<script>
  // Handle all password toggle icons
  document.querySelectorAll(".toggle-password").forEach(icon => {
    icon.addEventListener("click", function () {
      const targetId = this.getAttribute("data-target");
      const input = document.getElementById(targetId);

      const type = input.getAttribute("type") === "password" ? "text" : "password";
      input.setAttribute("type", type);

      this.classList.toggle("bi-eye");
      this.classList.toggle("bi-eye-slash");
    });
  });
</script>