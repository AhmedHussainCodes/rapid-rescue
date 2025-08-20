<?php
// Ambulance Management page - requires admin login
$page_title = "Manage Ambulances";
include '../includes/auth_check.php';
requireAdmin();
include '../includes/db_connect.php';

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_ambulance'])) {
        $vehicle_number = trim($_POST['vehicle_number']);
        $equipment_level = $_POST['equipment_level'];
        $status = $_POST['status'];
        
        if (empty($vehicle_number)) {
            $error_message = "Vehicle number is required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO ambulances (vehicle_number, equipment_level, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $vehicle_number, $equipment_level, $status);
            
            if ($stmt->execute()) {
                $success_message = "Ambulance added successfully!";
            } else {
                $error_message = "Failed to add ambulance. Vehicle number may already exist.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['update_ambulance'])) {
        $ambulance_id = $_POST['ambulance_id'];
        $vehicle_number = trim($_POST['vehicle_number']);
        $equipment_level = $_POST['equipment_level'];
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE ambulances SET vehicle_number = ?, equipment_level = ?, status = ? WHERE ambulanceid = ?");
        $stmt->bind_param("sssi", $vehicle_number, $equipment_level, $status, $ambulance_id);
        
        if ($stmt->execute()) {
            $success_message = "Ambulance updated successfully!";
        } else {
            $error_message = "Failed to update ambulance.";
        }
        $stmt->close();
    } elseif (isset($_POST['delete_ambulance'])) {
        $ambulance_id = $_POST['ambulance_id'];
        
        $stmt = $conn->prepare("DELETE FROM ambulances WHERE ambulanceid = ?");
        $stmt->bind_param("i", $ambulance_id);
        
        if ($stmt->execute()) {
            $success_message = "Ambulance deleted successfully!";
        } else {
            $error_message = "Failed to delete ambulance.";
        }
        $stmt->close();
    }
}

// Get all ambulances
$ambulances_result = $conn->query("SELECT * FROM ambulances ORDER BY vehicle_number");

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
    <main id="main-content" class="min-h-screen pt-14 transition-all duration-300 sm:ml-64 ml-20 max-w-full">
        <div class="p-4 sm:p-6" x-data="{ addOpen: false, editOpen: false, editId: '', editVehicleNumber: '', editEquipmentLevel: '', editStatus: '' }">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h2 class="text-xl sm:text-2xl font-semibold text-white mb-4 sm:mb-0">
                    <iconify-icon icon="ri:truck-line" class="mr-2"></iconify-icon>Manage Ambulances
                </h2>
                <button @click="addOpen = true" class="px-4 py-2 bg-grey-800 hover:bg-grey-700 text-white rounded-lg transition-colors duration-200 slide-up text-sm sm:text-base">
                    <iconify-icon icon="ri:add-circle-line" class="mr-2"></iconify-icon>Add Ambulance
                </button>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if (!empty($success_message)): ?>
                <div class="mb-6 p-4 bg-grey-800 border border-grey-600 rounded-lg text-white max-w-full slide-up">
                    <div class="flex items-center">
                        <iconify-icon icon="ri:check-line" class="mr-2"></iconify-icon>
                        <?php echo $success_message; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="mb-6 p-4 bg-grey-800 border border-grey-600 rounded-lg text-white max-w-full slide-up">
                    <div class="flex items-center">
                        <iconify-icon icon="ri:error-warning-line" class="mr-2"></iconify-icon>
                        <?php echo $error_message; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Ambulances Table -->
            <div class="bg-grey-900 border border-grey-700 rounded-lg overflow-x-auto">
                <div class="px-4 sm:px-6 py-4 border-b border-grey-700">
                    <h3 class="text-lg font-semibold text-white"><iconify-icon icon="ri:table-line" class="mr-2"></iconify-icon>All Ambulances</h3>
                </div>
                <div class="min-w-full">
                    <table class="w-full table-auto">
                        <thead class="bg-grey-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">Vehicle Number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">Equipment Level</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">Created</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-grey-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-grey-700">
                            <?php if ($ambulances_result->num_rows > 0): ?>
                                <?php while ($ambulance = $ambulances_result->fetch_assoc()): ?>
                                    <tr class="hover:bg-grey-800 transition-colors duration-200 fade-in">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-white"><?php echo $ambulance['ambulanceid']; ?></td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-white"><?php echo htmlspecialchars($ambulance['vehicle_number']); ?></td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-grey-800 text-white border border-grey-600"><?php echo $ambulance['equipment_level']; ?></span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-grey-800 text-white border border-grey-600"><?php echo $ambulance['status']; ?></span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-grey-400"><?php echo date('M j, Y', strtotime($ambulance['created_at'])); ?></td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <button @click="editOpen = true; editId = <?php echo $ambulance['ambulanceid']; ?>; editVehicleNumber = '<?php echo htmlspecialchars($ambulance['vehicle_number']); ?>'; editEquipmentLevel = '<?php echo $ambulance['equipment_level']; ?>'; editStatus = '<?php echo $ambulance['status']; ?>'" class="px-3 py-1 bg-grey-800 hover:bg-grey-700 text-white rounded transition-colors duration-200 text-sm">
                                                    <iconify-icon icon="ri:pencil-line" class="mr-1"></iconify-icon>Edit
                                                </button>
                                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this ambulance?')">
                                                    <input type="hidden" name="ambulance_id" value="<?php echo $ambulance['ambulanceid']; ?>">
                                                    <button type="submit" name="delete_ambulance" class="px-3 py-1 bg-grey-800 hover:bg-grey-700 text-white rounded transition-colors duration-200 text-sm">
                                                        <iconify-icon icon="ri:delete-bin-line" class="mr-1"></iconify-icon>Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <div class="text-grey-400">
                                            <iconify-icon icon="ri:truck-line" class="text-4xl mb-4"></iconify-icon>
                                            <p class="text-lg font-medium">No ambulances found</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Ambulance Modal -->
            <div x-show="addOpen" @click.away="addOpen = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
                <div class="bg-grey-900 rounded-lg p-4 sm:p-6 w-full max-w-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-white">Add New Ambulance</h3>
                        <button @click="addOpen = false" class="text-grey-400 hover:text-white">
                            <iconify-icon icon="ri:close-line" class="text-xl"></iconify-icon>
                        </button>
                    </div>
                    <form method="POST">
                        <div class="mb-4">
                            <label for="vehicle_number" class="block text-sm font-medium text-grey-300">Vehicle Number</label>
                            <input type="text" id="vehicle_number" name="vehicle_number" class="mt-1 px-4 py-2 bg-grey-800 border border-grey-600 rounded-lg text-white w-full focus:ring-2 focus:ring-grey-400 focus:border-transparent" required>
                        </div>
                        <div class="mb-4">
                            <label for="equipment_level" class="block text-sm font-medium text-grey-300">Equipment Level</label>
                            <select id="equipment_level" name="equipment_level" class="mt-1 px-4 py-2 bg-grey-800 border border-grey-600 rounded-lg text-white w-full focus:ring-2 focus:ring-grey-400 focus:border-transparent" required>
                                <option value="Basic">Basic</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-grey-300">Status</label>
                            <select id="status" name="status" class="mt-1 px-4 py-2 bg-grey-800 border border-grey-600 rounded-lg text-white w-full focus:ring-2 focus:ring-grey-400 focus:border-transparent" required>
                                <option value="Available">Available</option>
                                <option value="On call">On call</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="addOpen = false" class="px-4 py-2 bg-grey-800 hover:bg-grey-700 text-white rounded-lg text-sm">Cancel</button>
                            <button type="submit" name="add_ambulance" class="px-4 py-2 bg-grey-700 hover:bg-grey-600 text-white rounded-lg text-sm">Add Ambulance</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Ambulance Modal -->
            <div x-show="editOpen" @click.away="editOpen = false" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
                <div class="bg-grey-900 rounded-lg p-4 sm:p-6 w-full max-w-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-white">Edit Ambulance</h3>
                        <button @click="editOpen = false" class="text-grey-400 hover:text-white">
                            <iconify-icon icon="ri:close-line" class="text-xl"></iconify-icon>
                        </button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="ambulance_id" x-model="editId">
                        <div class="mb-4">
                            <label for="edit_vehicle_number" class="block text-sm font-medium text-grey-300">Vehicle Number</label>
                            <input type="text" id="edit_vehicle_number" name="vehicle_number" x-model="editVehicleNumber" class="mt-1 px-4 py-2 bg-grey-800 border border-grey-600 rounded-lg text-white w-full focus:ring-2 focus:ring-grey-400 focus:border-transparent" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_equipment_level" class="block text-sm font-medium text-grey-300">Equipment Level</label>
                            <select id="edit_equipment_level" name="equipment_level" x-model="editEquipmentLevel" class="mt-1 px-4 py-2 bg-grey-800 border border-grey-600 rounded-lg text-white w-full focus:ring-2 focus:ring-grey-400 focus:border-transparent" required>
                                <option value="Basic">Basic</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_status" class="block text-sm font-medium text-grey-300">Status</label>
                            <select id="edit_status" name="status" x-model="editStatus" class="mt-1 px-4 py-2 bg-grey-800 border border-grey-600 rounded-lg text-white w-full focus:ring-2 focus:ring-grey-400 focus:border-transparent" required>
                                <option value="Available">Available</option>
                                <option value="On call">On call</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="editOpen = false" class="px-4 py-2 bg-grey-800 hover:bg-grey-700 text-white rounded-lg text-sm">Cancel</button>
                            <button type="submit" name="update_ambulance" class="px-4 py-2 bg-grey-700 hover:bg-grey-600 text-white rounded-lg text-sm">Update Ambulance</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript with GSAP animations and sidebar sync -->
    <script>
        // Initialize GSAP animations
        gsap.registerPlugin(ScrollTrigger);
        
        gsap.fromTo('.slide-up', 
            { opacity: 0, y: 30 }, 
            { opacity: 1, y: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out' }
        );
        
        gsap.fromTo('.fade-in', 
            { opacity: 0 }, 
            { opacity: 1, duration: 0.8, stagger: 0.05, ease: 'power2.out', delay: 0.3 }
        );

        // Fallback to sync main content with sidebar state
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.getElementById('toggle-sidebar');
            
            if (!sidebar || !mainContent || !toggleBtn) {
                console.error('Sidebar, main content, or toggle button not found.');
                return;
            }

            // Function to update main content margin based on sidebar state
            function updateMainContentMargin() {
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

            // Initial margin update
            updateMainContentMargin();

            // Listen for sidebar toggle
            toggleBtn.addEventListener('click', updateMainContentMargin);

            // Listen for window resize
            window.addEventListener('resize', updateMainContentMargin);

            // Debug Alpine.js initialization
            document.addEventListener('alpine:init', () => {
                console.log('Alpine.js initialized for manage_ambulances.php');
            });
        });
    </script>
</body>
</html>