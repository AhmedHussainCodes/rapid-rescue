<?php
$currentPage = basename($_SERVER['PHP_SELF']);

if (!function_exists('isActive')) {
    function isActive($page) {
        global $currentPage;
        return $currentPage === $page
            ? 'bg-slate-700/50 text-emerald-400 border-emerald-500/30'
            : 'text-slate-300 hover:bg-slate-700/50 hover:text-emerald-400';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Rapid Rescue Admin' : 'Rapid Rescue Admin'; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: ['class', '[data-theme="dark"]'],
            theme: {
                extend: {
                    colors: {
                        'black': '#000000',
                        'white': '#ffffff'
                    }
                }
            }
        }
    </script>
    
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- GSAP Animation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Alpine.js for interactivity -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Leaflet for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        /* Adding sidebar styles for collapsible functionality */
        #sidebar.collapsed .nav-text {
            opacity: 0;
            visibility: hidden;
            position: absolute;
            left: calc(100% + 0.5rem);
            top: 50%;
            transform: translateY(-50%);
            background: #1e293b;
            color: #cbd5e1;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            white-space: nowrap;
            pointer-events: none;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            z-index: 30;
        }

        #sidebar.collapsed .nav-link:hover .nav-text {
            opacity: 1;
            visibility: visible;
        }

        #sidebar.collapsed {
            width: 5rem;
        }

        #sidebar {
            transition: width 0.3s ease;
            overflow-x: hidden;
            overflow-y: hidden;
        }

        #sidebar-content-hide {
            overflow-x: hidden;
            overflow-y: hidden;
        }

        .nav-text {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        #sidebar.collapsed .nav-text:not(.tooltip) {
            transform: translateX(-10px);
        }

        #sidebar:not(.collapsed) .header-logo {
            display: none;
        }

        #sidebar.collapsed .header-logo {
            display: block;
        }

        @media (max-width: 639px) {
            #sidebar {
                width: 5rem;
            }

            #sidebar:not(.collapsed) .nav-text {
                opacity: 0;
                visibility: hidden;
            }

            .header-logo {
                display: block !important;
            }
        }

        /* Adding theme toggle styles */
        .theme-toggle {
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
