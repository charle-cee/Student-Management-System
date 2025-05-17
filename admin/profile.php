<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <title>Student Management System|| Profile</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
<div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <?php include_once('includes/header.php'); ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <?php include_once('includes/sidebar.php'); ?>
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="page-header">
                    <h3 class="page-title"> My Profile </h3> <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align: center;">My Profile</h4>
                                <?php
                                $aid = $_SESSION['sturecmsaid'];
                                $sql = "SELECT * FROM tbladmin WHERE ID=:aid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':aid', $aid, PDO::PARAM_STR);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                $cnt = 1;
                                if ($query->rowCount() > 0) {
                                    foreach ($results as $row) {
                                        ?>
<div class="text-center p-3" style="background-color: #f8f9fa; border: 2px solid #003366; border-radius: 10px;">
    <!-- Profile Picture -->
    <div class="mb-3">
        <?php if (!empty($row->Image)) { ?>
            <img src="images/<?php echo $row->Image; ?>" 
                 alt="Profile image" 
                 style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #003366; border-radius: 50%; padding: 3px;">
        <?php } else { ?>
            <img src="images/faces/face8.jpg" 
                 alt="Profile image" 
                 style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #003366; border-radius: 50%; padding: 3px;">
        <?php } ?>
    </div>

    <!-- User Details -->
    <p class="mb-1 mt-3" style="font-weight: bold; text-transform: uppercase; color: #003366;">
        <?php echo htmlentities($row->Name); ?>
    </p>
    <?php if (!empty($row->UserType)): ?>
        <p style="color: #666;  margin-bottom: 5px;">
            <?php echo htmlentities($row->UserType); ?>
        </p>
    <?php endif; ?>
    <?php if (!empty($row->Role1)): ?>
        <p style="color: #666;  margin-bottom: 15px;">
            <?php echo htmlentities($row->Role1); ?>
        </p>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div style="display: flex; justify-content: center; gap: 10px;">
        <a href="view-profile.php" 
           style="background-color: #003366; color: white; padding: 8px 15px; border-radius: 5px; font-weight: bold;  text-decoration: none;">
           <i class="icon-eye"></i> View Profile
        </a>
        <a href="change-password.php" 
           style="background-color: #f0ad4e; color: white; padding: 8px 15px; border-radius: 5px; font-weight: bold;  text-decoration: none;">
           <i class="icon-pencil"></i> Change Password
        </a>
    </div>
</div>

                                    <?php }
                                }
                                ?>
                            </div>
                        </div>
                    </div><!-- partial:partials/_footer.html -->
            <?php include_once('includes/footer.php'); ?>
                </div>
            </div>
            <!-- content-wrapper ends -->
            
            <!-- partial -->
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<!-- plugins:js -->
<script src="vendors/js/vendor.bundle.base.js"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="vendors/select2/select2.min.js"></script>
<script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="js/off-canvas.js"></script>
<script src="js/misc.js"></script>
<!-- endinject -->
<!-- Custom js for this page -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('userDropdown').classList.add('show');
    });
</script>
<script src="js/typeahead.js"></script>
<script src="js/select2.js"></script>
<!-- End custom js for this page -->
</body>
</html>
<?php } ?>
