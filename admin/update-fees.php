<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_GET['academic_year'])) {
        $academicYear = $_GET['academic_year'];
    
    if (isset($_POST['submit'])) {
        $academicYear = $_POST['academicYear'];
        $fees = $_POST['fees'];
        $term = $_POST['term'];

        $termFeesColumn = "";
        switch ($term) {
            case 1:
                $termFeesColumn = "Term1Fees";
                break;
            case 2:
                $termFeesColumn = "Term2Fees";
                break;
            case 3:
                $termFeesColumn = "Term3Fees";
                break;
            default:
                // Handle if necessary
                break;
        }

        $sql = "UPDATE tblcalendar SET $termFeesColumn = :fees WHERE AcademicYear = :academicYear";
        $query = $dbh->prepare($sql);
        $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
        $query->bindParam(':fees', $fees, PDO::PARAM_INT);
        if ($query->execute()) {
            header('location: update-fees.php?academic_year='. urlencode($academicYear) .'&success=' . urlencode($academicYear) . ' Term ' . urlencode($term) . ' Academic year fees updated successfully.');
            exit();
        } else {
            header('location: update-fees.php?academic_year='. urlencode($academicYear) .'&error=' . urlencode($academicYear) . ' Term '. urlencode($term) .' Failed to update academic year fees.');
            exit();
        }
        
    }
} else{
    header('location: update-fees.php?academic_year='. urlencode($academicYear) .'&error=' . urlencode($academicYear) . ' is not active academic year.');
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
    <title>Student Management System || Update Fees</title>
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
    <link rel="stylesheet" href="css/style.css" />
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
                    <h3 class="page-title"> Update Fees </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Update Fees</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body" style="background-color: white; border-radius: 10px; padding: 20px;">
    <h4 class="card-title" style="text-align: center; background-color: #003366; color:white; padding: 10px;">Update Fees</h4>
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
            <label for="academicYear" style="color: #003366;">Academic Year</label>
            <input type="text" class="form-control" id="academicYear" name="academicYear" placeholder="Enter academic year (e.g., 2023/2024)" readonly value="<?php echo htmlspecialchars($academicYear); ?>" style="border-radius: 5px; border: 1px solid #003366;">
        </div>
        <div class="form-group">
            <label for="fees" style="color: #003366;">Fees</label>
            <input type="number" class="form-control" id="fees" name="fees" placeholder="Enter fees" required style="border-radius: 5px; border: 1px solid #003366;">
        </div>
        <div class="form-group">
            <label for="term" style="color: #003366;">Select Term</label>
            <select class="form-control" id="term" name="term" required style="border-radius: 5px; border: 1px solid #003366;">
                <option selected disabled>Select Term</option>
                <option value="1">Term 1</option>
                <option value="2">Term 2</option>
                <option value="3">Term 3</option>
            </select>
        </div>
        <button type="submit" class="btn" style="background-color: #003366; color: white; border-radius: 30px; padding: 10px 20px;" name="submit">Update</button>
        <a href="javascript:history.back()" class="btn" style="background-color: yellow; color: #003366; border-radius: 30px; padding: 10px 20px; margin-left: 10px;">Cancel</a>
    </form>
</div>
                       </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
       
        <!-- partial -->
    </div>
    <!-- main-panel ends -->
    <?php include_once('includes/footer.php');?>
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
<script src="js/typeahead.js"></script>
<script src="js/select2.js"></script>
<!-- End custom js for this page -->
</body>
</html>
