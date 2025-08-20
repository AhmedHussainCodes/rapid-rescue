<?php
// User Management - View, manage, update, and delete users
$page_title = "User Management";
include '../includes/auth_check.php';
requireAdmin();
include '../includes/db_connect.php';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Toggle status
    if (isset($_POST['toggle_status'])) {
        $user_id = $_POST['user_id'];
        $new_status = $_POST['new_status'];
        
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE userid = ?");
        $stmt->bind_param("si", $new_status, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "User status updated successfully!";
        } else {
            $error_message = "Failed to update user status.";
        }
        $stmt->close();
    }
    
    // Add user
    if (isset($_POST['add_user'])) {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dob = $_POST['dob'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, phone, dob, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'user', 'active', NOW())");
        $stmt->bind_param("ssssss", $firstname, $lastname, $email, $phone, $dob, $password);
        
        if ($stmt->execute()) {
            $success_message = "User added successfully!";
        } else {
            $error_message = "Failed to add user. Email might already exist.";
        }
        $stmt->close();
    }
    
    // Update user
    if (isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dob = $_POST['dob'];
        
        $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, phone = ?, dob = ? WHERE userid = ?");
        $stmt->bind_param("sssssi", $firstname, $lastname, $email, $phone, $dob, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "User updated successfully!";
        } else {
            $error_message = "Failed to update user. Email might already exist.";
        }
        $stmt->close();
    }
    
    // Delete user
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        
        $stmt = $conn->prepare("DELETE FROM users WHERE userid = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $success_message = "User deleted successfully!";
        } else {
            $error_message = "Failed to delete user.";
        }
        $stmt->close();
    }
}

// Get all users with their request statistics
$users_query = "
    SELECT u.*, 
           COUNT(r.requestid) as total_requests,
           MAX(r.request_time) as last_request
    FROM users u 
    LEFT JOIN requests r ON u.userid = r.userid 
    WHERE u.role != 'admin'
    GROUP BY u.userid 
    ORDER BY u.created_at DESC
";
$users_result = $conn->query($users_query);

// Get user statistics
$user_stats = $conn->query("
    SELECT 
        COUNT(*) as total_users,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_users,
        SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as new_users
    FROM users WHERE role != 'admin'
")->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - Rapid Rescue Admin'; ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🚑</text></svg>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: ['class', '[data-theme="dark"]'],
            theme: {
                extend: {
                    colors: {
                        'black': '#000000',
                        'white': '#ffffff',
                        'grey': {
                            100: '#f5f5f5',
                            200: '#e5e5e5',
                            300: '#d4d4d4',
                            400: '#a3a3a3',
                            500: '#737373',
                            600: '#525252',
                            700: '#404040',
                            800: '#262626',
                            900: '#171717'
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- GSAP Animation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Alpine.js for interactivity -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Iconify for icons -->
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>
<body class="bg-black text-white font-sans">
    <!-- Include admin header and sidebar -->
    <?php include '../includes/admin_header.php'; ?>
    <?php include 'sider.php'; ?>
    
    <!-- Main Content -->
    <main id="main-content" class="min-h-screen pt-14 transition-all duration-300 sm:ml-64 ml-20">
        <div class="p-3">
            <!-- Compact Header -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-white mb-2 sm:mb-0">
                    <iconify-icon icon="ri:group-line" class="mr-2"></iconify-icon>User Management
                </h2>
                <div class="flex items-center gap-2">
                    <input type="text" id="userSearch" class="px-3 py-1.5 bg-grey-800 border border-grey-600 rounded text-white placeholder-grey-400 focus:ring-1 focus:ring-grey-400 text-sm w-48" placeholder="Search users...">
                    <button onclick="openAddUserModal()" class="px-3 py-1.5 bg-blue-black hover:bg-gray-600 text-white border border-light rounded transition-colors duration-200 text-sm">
                        <iconify-icon icon="ri:user-add-line text-white" class="mr-1"></iconify-icon>Add User
                    </button>
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="mb-3 p-3 bg-green-900 border border-green-600 rounded text-white text-sm">
                    <iconify-icon icon="ri:check-line" class="mr-2"></iconify-icon><?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="mb-3 p-3 bg-red-900 border border-red-600 rounded text-white text-sm">
                    <iconify-icon icon="ri:error-warning-line" class="mr-2"></iconify-icon><?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Compact Statistics -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="bg-grey-900 border border-grey-700 rounded p-3 text-center">
                    <div class="text-lg font-bold text-white"><?php echo $user_stats['total_users']; ?></div>
                    <div class="text-xs text-grey-400">Total Users</div>
                </div>
                <div class="bg-grey-900 border border-grey-700 rounded p-3 text-center">
                    <div class="text-lg font-bold text-green-400"><?php echo $user_stats['active_users']; ?></div>
                    <div class="text-xs text-grey-400">Active</div>
                </div>
                <div class="bg-grey-900 border border-grey-700 rounded p-3 text-center">
                    <div class="text-lg font-bold text-red-400"><?php echo $user_stats['inactive_users']; ?></div>
                    <div class="text-xs text-grey-400">Inactive</div>
                </div>
                <div class="bg-grey-900 border border-grey-700 rounded p-3 text-center">
                    <div class="text-lg font-bold text-blue-400"><?php echo $user_stats['new_users']; ?></div>
                    <div class="text-xs text-grey-400">New (30d)</div>
                </div>
            </div>
            
            <!-- Compact Users Table -->
            <div class="bg-grey-900 border border-grey-700 rounded overflow-hidden">
                <div class="px-4 py-2 border-b border-grey-700 bg-grey-800">
                    <h3 class="text-sm font-semibold text-white">All Users</h3>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table id="usersTable" class="w-full table-auto text-xs">
                        <thead class="bg-grey-800 sticky top-0">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-grey-300 uppercase">ID</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-grey-300 uppercase">Name</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-grey-300 uppercase">Email</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-grey-300 uppercase">Phone</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-grey-300 uppercase">Requests</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-grey-300 uppercase">Status</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-grey-300 uppercase">Joined</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-grey-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-grey-700">
                            <?php if ($users_result->num_rows > 0): ?>
                                <?php while ($user = $users_result->fetch_assoc()): ?>
                                    <tr class="hover:bg-grey-800 transition-colors duration-200">
                                        <td class="px-2 py-2 text-xs font-medium text-white">#<?php echo $user['userid']; ?></td>
                                        <td class="px-2 py-2">
                                            <div class="text-xs font-medium text-white"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></div>
                                            <div class="text-xs text-grey-400"><?php echo date('M j, Y', strtotime($user['dob'])); ?></div>
                                        </td>
                                        <td class="px-2 py-2">
                                            <div class="text-xs text-grey-300 truncate max-w-32" title="<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </td>
                                        <td class="px-2 py-2">
                                            <div class="text-xs text-grey-300"><?php echo htmlspecialchars($user['phone']); ?></div>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="inline-flex px-1 py-0.5 text-xs font-semibold rounded bg-grey-800 text-white"><?php echo $user['total_requests']; ?></span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="inline-flex px-1 py-0.5 text-xs font-semibold rounded <?php echo $user['status'] == 'active' ? 'bg-green-800 text-green-200' : 'bg-red-800 text-red-200'; ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="text-xs text-grey-400"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <div class="flex gap-1">
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['userid']; ?>">
                                                    <input type="hidden" name="new_status" value="<?php echo $user['status'] == 'active' ? 'inactive' : 'active'; ?>">
                                                    <button type="submit" name="toggle_status" class="px-2 py-1 bg-grey-800 hover:bg-grey-700 text-white rounded transition-colors duration-200 text-xs" 
                                                            onclick="return confirm('Toggle user status?')">
                                                        <iconify-icon icon="ri:<?php echo $user['status'] == 'active' ? 'user-unfollow-line' : 'user-follow-line'; ?>"></iconify-icon>
                                                    </button>
                                                </form>
                                                <button onclick='openUpdateUserModal(<?php echo json_encode($user); ?>)' class="px-2 py-1 bg-grey-800 hover:bg-grey-700 text-white rounded transition-colors duration-200 text-xs">
                                                    <iconify-icon icon="ri:edit-line"></iconify-icon>
                                                </button>
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['userid']; ?>">
                                                    <button type="submit" name="delete_user" class="px-2 py-1 bg-red-800 hover:bg-red-700 text-white rounded transition-colors duration-200 text-xs" 
                                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                                        <iconify-icon icon="ri:delete-bin-line"></iconify-icon>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center">
                                        <div class="text-grey-400">
                                            <iconify-icon icon="ri:group-line" class="text-2xl mb-2"></iconify-icon>
                                            <p class="text-sm">No users found</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" onclick="closeAddUserModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-grey-900 border border-grey-700 rounded-lg p-6 w-full max-w-md" onclick="event.stopPropagation()">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">Add New User</h3>
                    <button onclick="closeAddUserModal()" class="text-grey-400 hover:text-white">
                        <iconify-icon icon="ri:close-line" class="text-xl"></iconify-icon>
                    </button>
                </div>
                <form method="POST" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-grey-300 mb-1">First Name</label>
                            <input type="text" name="firstname" required class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-grey-300 mb-1">Last Name</label>
                            <input type="text" name="lastname" required class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-grey-300 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-grey-300 mb-1">Phone</label>
                        <input type="tel" name="phone" required class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-grey-300 mb-1">Date of Birth</label>
                        <input type="date" name="dob" required class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-grey-300 mb-1">Password</label>
                        <input type="password" name="password" required minlength="6" class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" name="add_user" class="flex-1 px-4 py-2 bg-black border border-light hover:bg-gray-700 text-white rounded transition-colors duration-200 text-sm">
                            <iconify-icon icon="ri:user-add-line text-white" class="mr-1"></iconify-icon>Add User
                        </button>
                        <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 bg-grey-700 hover:bg-grey-600 text-white rounded transition-colors duration-200 text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update User Modal -->
    <div id="updateUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" onclick="closeUpdateUserModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-grey-900 border border-grey-700 rounded-lg p-6 w-full max-w-md" onclick="event.stopPropagation()">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">Update User</h3>
                    <button onclick="closeUpdateUserModal()" class="text-grey-400 hover:text-white">
                        <iconify-icon icon="ri:close-line" class="text-xl"></iconify-icon>
                    </button>
                </div>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="user_id" id="update_user_id">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-grey-300 mb-1">First Name</label>
                            <input type="text" name="firstname" id="update_firstname" required class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-grey-300 mb-1">Last Name</label>
                            <input type="text" name="lastname" id="update_lastname" required class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-grey-300 mb-1">Email</label>
                        <input type="email" name="email" id="update_email" required class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-grey-300 mb-1">Phone</label>
                        <input type="tel" name="phone" id="update_phone" required class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-grey-300 mb-1">Date of Birth</label>
                        <input type="date" name="dob" id="update_dob" required class="w-full px-3 py-2 bg-grey-800 border border-grey-600 rounded text-white text-sm focus:ring-1 focus:ring-blue-400">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" name="update_user" class="flex-1 px-4 py-2 bg-black border border-light hover:bg-gray-700 text-white rounded transition-colors duration-200 text-sm">
                            <iconify-icon icon="ri:edit-line" class="mr-1"></iconify-icon>Update User
                        </button>
                        <button type="button" onclick="closeUpdateUserModal()" class="px-4 py-2 bg-grey-700 hover:bg-grey-600 text-white rounded transition-colors duration-200 text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // GSAP animations
        gsap.registerPlugin(ScrollTrigger);
        
        // Sidebar sync
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.getElementById('toggle-sidebar');
            
            function updateMainContentMargin() {
                if (!sidebar || !mainContent) return;
                
                const isCollapsed = sidebar.classList.contains('collapsed');
                const isMobile = window.matchMedia("(max-width: 639px)").matches;

                if (isMobile || isCollapsed) {
                    mainContent.classList.remove('sm:ml-64');
                    mainContent.classList.add('ml-20');
                } else {
                    mainContent.classList.remove('ml-20');
                    mainContent.classList.add('sm:ml-64');
                }
            }

            updateMainContentMargin();
            if (toggleBtn) toggleBtn.addEventListener('click', updateMainContentMargin);
            window.addEventListener('resize', updateMainContentMargin);
        });

        // Search functionality
        document.getElementById('userSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
        
        // Modal functions
        function openAddUserModal() {
            document.getElementById('addUserModal').classList.remove('hidden');
        }
        
        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.add('hidden');
        }
        
        function openUpdateUserModal(user) {
            document.getElementById('update_user_id').value = user.userid;
            document.getElementById('update_firstname').value = user.firstname;
            document.getElementById('update_lastname').value = user.lastname;
            document.getElementById('update_email').value = user.email;
            document.getElementById('update_phone').value = user.phone;
            document.getElementById('update_dob').value = user.dob;
            document.getElementById('updateUserModal').classList.remove('hidden');
        }
        
        function closeUpdateUserModal() {
            document.getElementById('updateUserModal').classList.add('hidden');
        }
        
        // View user details
        function viewUserDetails(userId) {
            alert('User details modal would open for user ID: ' + userId);
        }

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddUserModal();
                closeUpdateUserModal();
            }
        });
    </script>
</body>
</html>