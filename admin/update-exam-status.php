<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
} else {
    $status = 'Active';
    // Initialize variables
    $activeTerm = '';
    $academicYear = '';
    $exam = '';
    $examstatus = '';
    $examentrystatus = '';
    
    // Fetch active term data
    $sql = "SELECT * FROM tblcalendar WHERE Status = :status";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $activeTerm = $result['ActiveTerm'] ?? '';
        $academicYear = $result['AcademicYear'] ?? '';
        $exam = $result['ExaType'] ?? '';
        $examstatus = $result['ExaViewStatus'] ?? '';
        $examentrystatus = $result['ExamEntry'] ?? '';
    }

    if (isset($_POST['submit'])) {
        $academicYear = $_POST['academicYear'];
        $examview = $_POST['examview'];
        $examentry = $_POST['examentry'];

        $sql = "UPDATE tblcalendar SET ExamEntry = :examentry, ExaViewStatus = :examview WHERE AcademicYear = :academicYear";
        $query = $dbh->prepare($sql);
        $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
        $query->bindParam(':examentry', $examentry, PDO::PARAM_STR);
        $query->bindParam(':examview', $examview, PDO::PARAM_STR);
        
        if ($query->execute()) {
            // Log the action to admin_logs
            $adminId = $_SESSION['sturecmsaid'];
            $adminUsername = $_SESSION['username'] ?? 'unknown';
            $action = "Updated Exam Status";
            $actionDetails = "Updated exam status for Academic Year: $academicYear, Term: $activeTerm";
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $requestUrl = $_SERVER['REQUEST_URI'];
            $status = 'SUCCESS';
            $affectedTable = 'tblcalendar';
            $affectedId = $academicYear; // Use the AcademicYear as affected ID

            // Insert into admin_logs table
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
            $log_query->bindParam(':status', $status);
            $log_query->bindParam(':affected_table', $affectedTable);
            $log_query->bindParam(':affected_id', $affectedId);

            $log_query->execute();

            // Redirect with success message
            header('location: update-exam-status.php?academic_year=' . urlencode($academicYear) . '&success=' . urlencode($academicYear) . ', Term ' . urlencode($activeTerm) . ' exam status has been updated successfully.');
            exit();
        } else {
            // Log the failed attempt
            $adminId = $_SESSION['sturecmsaid'];
            $adminUsername = $_SESSION['username'] ?? 'unknown';
            $action = "Failed to Update Exam Status";
            $actionDetails = "Failed to update exam status for Academic Year: $academicYear, Term: $activeTerm";
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $requestUrl = $_SERVER['REQUEST_URI'];
            $status = 'FAILED';
            $affectedTable = 'tblcalendar';
            $affectedId = $academicYear; // Use the AcademicYear as affected ID

            // Insert into admin_logs table
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
            $log_query->bindParam(':status', $status);
            $log_query->bindParam(':affected_table', $affectedTable);
            $log_query->bindParam(':affected_id', $affectedId);

            $log_query->execute();

            // Redirect with error message
            header('location: update-exam-status.php?academic_year=' . urlencode($academicYear) . '&error=' . urlencode($academicYear) . ' Term ' . urlencode($activeTerm) . ' Failed to update Exam status.');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Student Management System || Update Exam Status</title>
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
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .toggle-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .toggle-label {
            margin-right: 15px;
            font-weight: 500;
            color: #003366;
            min-width: 250px;
        }
        .toggle-btn {
            position: relative;
            display: inline-block;
            width: 80px;
            height: 40px;
            margin: 0 10px;
        }
        .toggle-btn input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 10px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 32px;
            width: 32px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background-color: yellow;
        }
        input:focus + .toggle-slider {
            box-shadow: 0 0 1px yellow;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(40px);
        }
        .toggle-icon {
            color: white;
            font-size: 18px;
            z-index: 1;
        }
        .toggle-icon.active {
            opacity: 1;
        }
        .toggle-icon.inactive {
            opacity: 0.5;
        }
    </style>
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
                    <h3 class="page-title"> Update Exam Status </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Update Current Term Exam Status</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                           <div class="card-body" style="background-color: white; border-radius: 10px; padding: 20px;">
    <h4 class="card-title" style="text-align: center; background : #003366; color :white; padding : 10px;">Update Current Term Exam Status</h4>
    
    <form class="forms-sample" method="post" enctype="multipart/form-data">
        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger alert-dismissible text-center" style="background-color: #f8d7da; color: #721c24;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <b><?php echo htmlspecialchars($_GET['error']); ?></b>
            </div> <br>
        <?php } ?>
        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success alert-dismissible text-center" style="background-color: #d4edda; color: #155724;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <b><?php echo htmlspecialchars($_GET['success']); ?></b>
            </div>
        <?php } ?>
        
        <div class="form-group">
            <label for="academicYear" style="color: #003366;">Academic Year</label>
            <input type="text" class="form-control" id="academicYear" name="academicYear" placeholder="Enter academic year (e.g., 2023/2024)" readonly value="<?php echo htmlspecialchars($academicYear); ?>" style="border-radius: 5px; border: 1px solid #003366;">
        </div>
        
        <div class="toggle-container">
            <span class="toggle-label">Exam Results Entry Status</span>
            <label class="toggle-btn">
                <input type="checkbox" name="examentry" value="Yes" <?php echo ($examentrystatus == 'Yes') ? 'checked' : ''; ?>>
                <span class="toggle-slider">
                    <i class="fas fa-pencil-alt toggle-icon <?php echo ($examentrystatus == 'Yes') ? 'active' : 'inactive'; ?>"></i>
                    <i class="fas fa-times toggle-icon <?php echo ($examentrystatus == 'No') ? 'active' : 'inactive'; ?>"></i>
                </span>
                <input type="hidden" name="examentry" value="No">
            </label>
            <span><?php echo ($examentrystatus == 'Yes') ? 'Enabled' : 'Disabled'; ?></span>
        </div>
        
        <div class="toggle-container">
            <span class="toggle-label">Exam Results View Status</span>
            <label class="toggle-btn">
                <input type="checkbox" name="examview" value="viewable" <?php echo ($examstatus == 'viewable') ? 'checked' : ''; ?>>
                <span class="toggle-slider">
                    <i class="fas fa-eye toggle-icon <?php echo ($examstatus == 'viewable') ? 'active' : 'inactive'; ?>"></i>
                    <i class="fas fa-eye-slash toggle-icon <?php echo ($examstatus == 'not viewable') ? 'active' : 'inactive'; ?>"></i>
                </span>
                <input type="hidden" name="examview" value="not viewable">
            </label>
            <span><?php echo ($examstatus == 'viewable') ? 'Viewable' : 'Not Viewable'; ?></span>
        </div>

        <div class="form-group text-center mt-4">
            <button type="submit" class="btn" style="background-color: #003366; color: white; border-radius: 30px; padding: 10px 20px;" name="submit">
                <i class=""></i> Update Status
            </button>
            <a href="javascript:history.back()" class="btn" style="background-color: yellow; color: #003366; border-radius: 30px; padding: 10px 20px; margin-left: 10px;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        
    </div>


        <!-- partial -->
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
<script src="js/typeahead.js"></script>
<script src="js/select2.js"></script>
<script>
    // Update the hidden input fields when toggles are changed
    document.querySelectorAll('.toggle-btn input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const hiddenInput = this.parentElement.querySelector('input[type="hidden"]');
            if (this.checked) {
                hiddenInput.disabled = true;
            } else {
                hiddenInput.disabled = false;
            }
        });
    });
</script>
</body>
</html>