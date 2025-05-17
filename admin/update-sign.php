<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
}

// Fetch sign from the database
$sid = $_SESSION['sturecmsaid'];
$sql = "SELECT sign FROM tbladmin WHERE ID = :sid";
$query = $dbh->prepare($sql);
$query->bindParam(':sid', $sid, PDO::PARAM_INT);
$query->execute();
$row = $query->fetch(PDO::FETCH_ASSOC);
$currentSign = $row['sign'];

if (isset($_POST['submit'])) {
    // Update sign in the database
    $new_sign = $_POST['new_sign'];
    $sql_update = "UPDATE tbladmin SET sign = :new_sign WHERE ID = :sid";
    $query_update = $dbh->prepare($sql_update);
    $query_update->bindParam(':sid', $sid, PDO::PARAM_INT);
    $query_update->bindParam(':new_sign', $new_sign, PDO::PARAM_STR);
    
    if ($query_update->execute()) {
        header('location: update-sign.php?success= Your signature has been updated successfully.');
        exit();
    } else {
        header('location: update-sign.php?error= Failed to update signature.');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/jpg" href="logo.jpg">
     <title>Student Management System || Update Sign</title>
    <!-- Import custom signature font -->
    <link rel="icon" type="images/ico" href="images/sms.ICO">
    <link href="https://fonts.googleapis.com/css2?family=Handlee&display=swap" rel="stylesheet">
    <style>
        .signature {
            font-family: 'Handlee', cursive; /* Custom signature font */
            font-size: 24px;
            color: #333; /* Signature color */
        }
    </style>
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <?php include_once('includes/header.php');?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <?php include_once('includes/sidebar.php');?>
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="page-header">
                    <h3 class="page-title"> Update Sign </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Update Sign</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                           <div class="card-body">
    <h4 class="card-title" style="text-align: center; background-color: #003366; color: white; padding: 10px; font-weight: bold;">
        Update Signature
    </h4>
    <div id="current_sign" style="margin-top: 20px;">
        <h5 class="text-center" style="color: #003366;">Current Sign:</h5>
        <?php if (!empty($currentSign)) : ?>
            <p class="text-center signature" style="font-style: italic;"><?php echo $currentSign; ?></p>
        <?php else : ?>
            <p class="text-center" style="color: #003366;">No sign available.</p>
        <?php endif; ?>
    </div>
    <form class="forms-sample" method="post" enctype="form-data">
        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger alert-dismissible text-center" style="background-color: #f8d7da; color: #721c24;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <b><?php echo $_GET['error']; ?></b>
            </div> <br>
        <?php } ?>
        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success alert-dismissible text-center" style="background-color: #d4edda; color: #155724;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <b><?php echo $_GET['success']; ?></b>
            </div>
        <?php } ?>
        <div class="form-group">
            <label for="new_sign" style="font-weight: bold;">New Sign</label>
            <input type="text" class="form-control" id="new_sign" name="new_sign" value="<?php echo $currentSign; ?>" required style="border: 2px solid #003366; border-radius: 5px;">
        </div>
        <div class="form-group d-flex justify-content-between" style="margin-top: 20px;">
            <button type="submit" class="btn" style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 40px;" name="submit">
                Update
            </button>
            <a href="javascript:history.back()" class="btn" style="background-color: yellow; color: #003366; padding: 10px 20px; border-radius: 40px;">
                Cancel
            </a>
        </div>
    </form>
</div>
                       </div>
                    </div>
                </div>
            </div> <!-- partial:partials/_footer.html -->
        <?php include_once('includes/footer.php');?>
       
        </div>
        <!-- content-wrapper ends -->
        <!-- partial -->
    </div>
    <!-- main-panel ends -->
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
<script>
        // Auto-hide alerts after 6 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 150); // Remove after fade out
            });
        }, 6000);
    </script>
<!-- Custom js for this page -->
<script src="js/typeahead.js"></script>
<script src="js/select2.js"></script>
<!-- End custom js for this page -->
</body>
</html>
