<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    $status = 'Active';

    // Fetch active term based on the academic year
    $sql = "SELECT * FROM tblcalendar WHERE Status = :status";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $activeTerm = $result['ActiveTerm'];
        $academicYear = $result['AcademicYear'];
        $exam = $result['ExaType'];
    }

    if (isset($_POST['submit'])) {
        $academicYear = $_POST['academicYear'];
        $term = $_POST['term'];
        $exam = $_POST['exam'];

        $sql = "UPDATE tblcalendar SET ActiveTerm = :term, ExaType = :exam WHERE AcademicYear = :academicYear";
        $query = $dbh->prepare($sql);
        $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
        $query->bindParam(':term', $term, PDO::PARAM_INT);
        $query->bindParam(':exam', $exam, PDO::PARAM_STR);

        $executed = $query->execute();

        // Prepare log values
        $adminId = $_SESSION['sturecmsaid'];
        $adminUsername = $_SESSION['username'] ?? 'unknown';
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUrl = $_SERVER['REQUEST_URI'];
        $action = $executed ? "Updated Active Term" : "Failed to Update Active Term";
        $statusLog = $executed ? 'SUCCESS' : 'FAILED';
        $affectedTable = 'tblcalendar';
        $affectedId = $academicYear;
        $actionDetails = $executed
            ? "Updated Active Term to Term: $term and Exam Type: $exam for Academic Year: $academicYear"
            : "Failed to update Active Term to Term: $term and Exam Type: $exam for Academic Year: $academicYear";

        // Insert into admin_logs
        $log_sql = "INSERT INTO admin_logs (admin_id, username, action, action_details, ip_address, user_agent, request_method, request_url, timestamp, status, affected_table, affected_id)
                    VALUES (:admin_id, :username, :action, :action_details, :ip_address, :user_agent, :request_method, :request_url, NOW(), :status, :affected_table, :affected_id)";
        $log_query = $dbh->prepare($log_sql);
        $log_query->bindParam(':admin_id', $adminId);
        $log_query->bindParam(':username', $adminUsername);
        $log_query->bindParam(':action', $action);
        $log_query->bindParam(':action_details', $actionDetails);
        $log_query->bindParam(':ip_address', $ipAddress);
        $log_query->bindParam(':user_agent', $userAgent);
        $log_query->bindParam(':request_method', $requestMethod);
        $log_query->bindParam(':request_url', $requestUrl);
        $log_query->bindParam(':status', $statusLog);
        $log_query->bindParam(':affected_table', $affectedTable);
        $log_query->bindParam(':affected_id', $affectedId);
        $log_query->execute();

        // Redirect with appropriate message
        if ($executed) {
            header('location: update-active-term.php?academic_year=' . urlencode($academicYear) . '&success=' . urlencode($academicYear) . ', Term ' . urlencode($term) . ' has been made active term successfully.');
        } else {
            header('location: update-active-term.php?academic_year=' . urlencode($academicYear) . '&error=' . urlencode($academicYear) . ' Term ' . urlencode($term) . ' Failed to update active term.');
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
                    <h3 class="page-title"> Update Active Term </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Update Active Term</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                           <div class="card-body" style="background-color: white; border-radius: 10px; padding: 20px;">
    <h4 class="card-title" style="text-align: center; background : #003366; color :white; padding : 10px;">Update Active Term</h4>
    
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
            <label for="term" style="color: #003366;">Select Term</label>
            <select class="form-control" id="term" name="term" required style="border-radius: 5px; border: 1px solid #003366;">
                <option selected disabled>Select Term</option>
                <option value="1" <?php if ($activeTerm == 1) echo 'selected'; ?>>Term 1</option>
                <option value="2" <?php if ($activeTerm == 2) echo 'selected'; ?>>Term 2</option>
                <option value="3" <?php if ($activeTerm == 3) echo 'selected'; ?>>Term 3</option>
            </select>
        </div>
    <input type="hidden" id="exam" name="exam" value="End" required>


        <button type="submit" class="btn" style="background-color: #003366; color: white; border-radius: 30px; padding: 10px 20px;" name="submit">Update</button>
        
        <!-- Cancel Button -->
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
    <?php include_once('includes/footer.php'); ?>
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
