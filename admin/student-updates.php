<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
} else {
    if (isset($_GET['delid'])) {
        $rid = intval($_GET['delid']);

        // Init admin log vars
        $adminId = $_SESSION['sturecmsaid'];
        $adminUsername = $_SESSION['username'] ?? 'Unknown';
        $action = 'Delete Student';
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUrl = $_SERVER['REQUEST_URI'];
        $statusLog = 'FAILED';
        $actionDetails = '';
        $affectedTable = 'tblstudent';
        $affectedId = $rid;

        // Fetch student info before deletion
        $stmt = $dbh->prepare("SELECT StudentName, StudentClass, Section, Gender, DOB FROM tblstudent WHERE ID = :rid");
        $stmt->bindParam(':rid', $rid, PDO::PARAM_INT);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $actionDetails = "Deleted Student - Name: {$student['StudentName']}, Class: {$student['StudentClass']}, Section: {$student['Section']}, Gender: {$student['Gender']}, DOB: {$student['DOB']}";

            // Delete record
            $sql = "DELETE FROM tblstudent WHERE ID = :rid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':rid', $rid, PDO::PARAM_INT);
            $success = $query->execute();

            $statusLog = $success ? 'SUCCESS' : 'FAILED';

            // Redirect with success or error message
            if ($success) {
                $redirectMsg = 'Student deleted successfully.';
                $msgType = 'success';
            } else {
                $redirectMsg = 'Failed to delete student.';
                $msgType = 'error';
            }
        } else {
            $actionDetails = "Attempted to delete non-existent student ID: $rid";
            $redirectMsg = 'Student not found.';
            $msgType = 'error';
        }

        // Push to admin_logs
        $log_sql = "INSERT INTO admin_logs (admin_id, username, action, action_details, ip_address, user_agent, request_method, request_url, timestamp, status, affected_table, affected_id)
                    VALUES (:admin_id, :username, :action, :action_details, :ip_address, :user_agent, :request_method, :request_url, NOW(), :status, :affected_table, :affected_id)";
        $log_query = $dbh->prepare($log_sql);
        $log_query->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        $log_query->bindParam(':username', $adminUsername, PDO::PARAM_STR);
        $log_query->bindParam(':action', $action, PDO::PARAM_STR);
        $log_query->bindParam(':action_details', $actionDetails, PDO::PARAM_STR);
        $log_query->bindParam(':ip_address', $ipAddress, PDO::PARAM_STR);
        $log_query->bindParam(':user_agent', $userAgent, PDO::PARAM_STR);
        $log_query->bindParam(':request_method', $requestMethod, PDO::PARAM_STR);
        $log_query->bindParam(':request_url', $requestUrl, PDO::PARAM_STR);
        $log_query->bindParam(':status', $statusLog, PDO::PARAM_STR);
        $log_query->bindParam(':affected_table', $affectedTable, PDO::PARAM_STR);
        $log_query->bindParam(':affected_id', $affectedId, PDO::PARAM_INT);
        $log_query->execute();

        // Redirect to student-updates.php with SweetAlert-compatible parameters
        header("Location: student-updates.php?{$msgType}=" . urlencode($redirectMsg));
        exit();
    }
}
 ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/ico" href="sms.ICO">
        <title>Student Management System|||Manage Students</title>
        <!-- plugins:css -->
        <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
        <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
        <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
        <!-- endinject -->
        <!-- Plugin css for this page -->
        <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
        <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
        <!-- End plugin css for this page -->
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Custom JavaScript for search filter -->
    <style> .primary-search {
    color: #007bff; /* Primary color - blue */
    border-color: #007bff; /* Border color */
    /* Add any additional styling here */
}
</style>
        <!-- Layout styles -->
        <link rel="stylesheet" href="./css/style.css">
        <!-- End layout styles -->
    </head>

    <script>
        $(document).ready(function(){
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
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
                        <h3 class="page-title"> Manage Students </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Manage Students</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                <div class="d-sm-flex align-items-center mb-4" style="background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <h4 class="card-title mb-sm-0" style="color: #003366; font-size: 24px; font-weight: bold;">Manage Students</h4>
    
    <div class="col-md-4" style="margin-left: 20px;">
        <input type="text" id="myInput" class="form-control primary-search" placeholder="Start typing..." 
               style="border-radius: 30px; border: 1px solid #003366; padding: 10px 15px; font-size: 16px;">
    </div>
    
    <a href="add-students.php" class="ml-auto mb-3 mb-sm-0" 
       style="color: white; background-color: #003366; padding: 10px 20px; border-radius: 30px; font-size: 16px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
        Add New Student
    </a>
</div>

                                 <div class="table-responsive border rounded p-3" style="background-color: white;">
                                 
    <table class="table table-bordered table-striped">
        <thead style="background-color: #003366; color: white; text-transform : uppercase;">
            <tr>
                <th class="font-weight-bold" style="padding: 12px;">#</th>
                <th class="font-weight-bold" style="padding: 12px;">Student Number</th>
                <th class="font-weight-bold" style="padding: 12px;">Student Name</th>
                <th class="font-weight-bold" style="padding: 12px;">Class</th>
                <th class="font-weight-bold" style="padding: 12px;">Date Of Birth</th>
                <th class="font-weight-bold" style="padding: 12px;">Status</th>
                <th class="font-weight-bold" style="padding: 12px;">Action</th>
            </tr>
        </thead>
        <tbody id="myTable">
            <?php
            $sql = "SELECT * FROM tblstudent ORDER BY ID DESC";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            $cnt = 1;
            if ($query->rowCount() > 0) {
                foreach ($results as $row) { ?>
                    <tr style="border-bottom: 1px solid #003366;">
                        <td style="padding: 10px;"><?php echo htmlentities($cnt); ?></td>
                        <td style="padding: 10px;"><?php echo htmlentities($row->ID); ?></td>
                        <td style="padding: 10px;"><?php echo htmlentities($row->StudentName); ?></td>
                        <td style="padding: 10px;"><?php echo htmlentities($row->StudentClass); ?>
                            <?php echo htmlentities($row->Section); ?></td>
                        <td style="padding: 10px;"><?php echo htmlentities($row->DOB); ?></td>
                        <td style="padding: 10px;"><?php echo htmlentities($row->status); ?></td>
                        <td style="padding: 10px;">
                            <div style="display: flex; gap: 10px;">
                                <a href="edit-student-detail.php?editid=<?php echo htmlentities($row->ID); ?>" 
                                   style="background: #003366; color :white; padding : 10px; border-radius :40px;">
                                    <i class="icon-pencil"></i> Edit
                                </a>
                                <a href="student-updates.php?delid=<?php echo htmlentities($row->ID); ?>" 
                                   onclick="return confirm('Are you sure you want to remove this student from the system permanently?')"
                                   style="background: yellow; color :#003366;  padding : 10px; border-radius :40px;">
                                    <i class="icon-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php $cnt = $cnt + 1;
                }
            } else { ?>
                <tr><td class="text-center text-primary" colspan='6' style="padding: 20px;">Sorry! No records found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <?php include_once('includes/footer.php'); ?>
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
    <script src="./vendors/chart.js/Chart.min.js"></script>
    <script src="./vendors/daterangepicker/daterangepicker.js"></script>
    <script src="./vendors/chartist/chartist.min.js"></script>
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
    <?php if (isset($_GET['success']) || isset($_GET['error'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: "<?php echo isset($_GET['success']) ? 'success' : 'error'; ?>",
        title: "<?php echo isset($_GET['success']) ? 'Success!' : 'Error!'; ?>",
        text: "<?php echo isset($_GET['success']) ? $_GET['success'] : $_GET['error']; ?>",
        confirmButtonColor: '#003366'
    });
</script>
<?php endif; ?>

    <!-- Custom js for this page -->
    <script src="./js/dashboard.js"></script>
    <!-- End custom js for this page -->
    </body>
    </html>

