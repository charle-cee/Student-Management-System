<?php
// Determine the base URL dynamically based on the server's document root
$base_url = '/projects/ngcss/studentms/'; // Adjust this to match your project's subdirectory

// Get the current file name to set the active class
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Define the active link based on the current page
function isActive($page, $current_page) {
    return $page === $current_page ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">

    <style>
        /* Custom styles for the navbar */
        .navbar {
            position: fixed !important;
            top: 0;
            width: 100%;
            z-index: 1000; /* Ensure it's above other content */
            background-color: #003366; /* Dark blue background */
            padding: 10px 0; /* Reduce navbar height */
        }
        .navbar-brand h3 {
            color: white !important; /* White color for the brand */
            margin: 0; /* Remove margin for better alignment */
            font-size: 1.5rem; /* Adjust font size if needed */
        }
        .navbar-nav .nav-link {
            color: white !important; /* White text color */
            transition: background-color 0.3s, color 0.3s; /* Smooth transition */
        }
        .navbar-nav .nav-link:hover {
            background-color: yellow; /* Yellow on hover */
            color: black; /* Change text color on hover */
        }
        .navbar-nav .nav-link.active {
            background-color: yellow; /* Yellow for the active link */
            color: black; /* Change text color for active link */
        }

        /* Styles for mobile navbar toggler icon */
        .navbar-toggler {
            border-color: transparent; /* Remove border */
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='rgba%28255, 255, 255, 1%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }
    </style>
</head>
<body>
<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $base_url; ?>">
            <span class="d-none d-md-inline">
                <img src="<?php echo $base_url; ?>images/logo.jpg" alt="STUDENT MANAGEMENT SYSTEM Logo" style="height: 40px;">
            </span>
            <span class="d-inline d-md-none">
                <img src="<?php echo $base_url; ?>images/logo.jpg" alt="SMS Logo" style="height: 40px;">
            </span>
        </a>
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('index', $current_page); ?>" href="<?php echo $base_url; ?>index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('about', $current_page); ?>" href="<?php echo $base_url; ?>about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('contact', $current_page); ?>" href="<?php echo $base_url; ?>contact.php">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('announcements', $current_page); ?>" href="<?php echo $base_url; ?>announcements.php">Announcements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('results', $current_page); ?>" href="<?php echo $base_url; ?>results.php">View Results</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('login', $current_page); ?>" href="<?php echo $base_url; ?>admin/login.php">Staff Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>