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

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i>Create Account</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="registerForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" 
                                       value="<?php echo isset($form_data['firstname']) ? htmlspecialchars($form_data['firstname']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" 
                                       value="<?php echo isset($form_data['lastname']) ? htmlspecialchars($form_data['lastname']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo isset($form_data['phone']) ? htmlspecialchars($form_data['phone']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" 
                                   value="<?php echo isset($form_data['dob']) ? htmlspecialchars($form_data['dob']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo isset($form_data['address']) ? htmlspecialchars($form_data['address']) : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Minimum 6 characters</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus me-2"></i>Create Account
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account?</p>
                        <a href="login.php" class="btn btn-outline-primary mt-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
