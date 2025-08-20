<?php
session_start();
ob_start();

$page_title = "Login";
include 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'includes/db_connect.php';
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT userid, firstname, lastname, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['userid'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] == 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
        
        $stmt->close();
    }
    
    $conn->close();
}

if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success_message = "Registration successful! Please login with your credentials.";
}

ob_end_flush();
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-lg border-0 rounded-4 p-4 bg-dark text-white">
                <div class="card-header bg-white text-dark text-center py-3 rounded-top-4">
                    <h3 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>Login</h3>
                </div>
                <div class="card-body mt-3">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="loginForm" class="mt-3">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control form-control-lg bg-light text-dark" id="email" name="email" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" placeholder="Enter your email" required>
                        </div>
                        
                       <div class="mb-4 position-relative">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                            class="form-control form-control-lg bg-light text-dark pe-5" 
                            id="password" name="password" placeholder="Enter your password" required>

                            <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3" 
                                id="togglePassword" style="cursor: pointer; font-size: 17px; margin-top:20px;"></i>
                            </div>    
                        <div class="d-grid">
                            <button type="submit" class="btn btn-light btn-lg fw-bold text-dark">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Don't have an account? 
<a href="register.php" class="fw-bold text-light register-link">Register</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        min-height: 100vh;
    }
    .card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .card:hover {
        transform: translateY(-7px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    }
    .form-control:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }
     .register-link {
        text-decoration: none; 
         transition: text-decoration 0.4s ease-in;
    }
    .register-link:hover {
        text-decoration: underline;
    }
</style>

<script>
      const passwordInput = document.getElementById("password");
  const togglePassword = document.getElementById("togglePassword");

  togglePassword.addEventListener("click", function () {
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);

    // toggle the icon
    this.classList.toggle("bi-eye");
    this.classList.toggle("bi-eye-slash");
  });
</script>