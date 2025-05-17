<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Check if the session is active
if (strlen($_SESSION['sturecmsaid'] == 0)) {
  header('location:logout.php');
} else {
  $id = $_SESSION['sturecmsaid'];

  // Fetch the user type
  $sql12 = "SELECT UserType FROM tbladmin WHERE ID=:id";
  $query12 = $dbh->prepare($sql12);
  $query12->bindParam(':id', $id, PDO::PARAM_STR);
  $query12->execute();
  $usertype = $query12->fetchColumn();

  // Code for deletion
  if (isset($_GET['delid'])) {
    $rid = intval($_GET['delid']);
    
    // Step 1: Delete from tbladmin
    $sql = "DELETE FROM tbladmin WHERE ID=:rid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':rid', $rid, PDO::PARAM_STR);
    $query->execute();

    // Step 2: Log the action to the admin_logs table
    $adminUsername = $_SESSION['username'] ?? 'unknown';
    $action = "Deleted staff record";
    $actionDetails = "Deleted staff with ID: $rid";
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestUrl = $_SERVER['REQUEST_URI'];
    $status = 'SUCCESS';
    $affectedTable = 'tbladmin';
    $affectedId = $rid;

    $log_sql = "INSERT INTO admin_logs (admin_id, username, action, action_details, ip_address, user_agent, request_method, request_url, timestamp, status, affected_table, affected_id)
                VALUES (:admin_id, :username, :action, :action_details, :ip_address, :user_agent, :request_method, :request_url, NOW(), :status, :affected_table, :affected_id)";
    $log_query = $dbh->prepare($log_sql);
    $log_query->bindParam(':admin_id', $id);
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

    // Redirect after deletion and logging
    header('location:manage-staff.php?success=Deleted successfully.');
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Student  Management System|||Manage Staff</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
     <?php include_once('includes/header.php');?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <?php include_once('includes/sidebar.php');?>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
             <div class="page-header">
              <h3 class="page-title"> Manage Staff </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Manage Staff</li>
                </ol>
              </nav>
            </div>
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                      <!-- Search Input -->
    <div class="col-md-8">
        <input type="text" id="myInput" class="form-control " placeholder="Start typing..." style=" color: #003366; width: 100%; padding: 10px 15px; border-radius: 40px;">
    </div>
    
                 <div class="d-sm-flex align-items-center mb-4" style="background: #003366; padding: 10px; border-radius: 5px;">
    <h4 class="card-title mb-sm-0" style="color: white; margin: 0;">Manage Staff</h4>
    <a href="register.php" class="ml-auto mb-3 mb-sm-0" style="color: yellow; text-decoration: none; font-weight: bold;">
        <i class="icon-plus" style="color: yellow;"></i> Register New Staff
    </a>
</div>

                  <?php if (isset($_GET['error'])) { ?>
                            <div class="alert alert-danger alert-dismissible text-center">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <b><?php echo $_GET['error']; ?></b>
                            </div> <br>
                        <?php } ?>
                        <?php if (isset($_GET['success'])) { ?>
                            <div class="alert alert-success alert-dismissible text-center">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <b><?php echo $_GET['success']; ?></b>
                            </div>
                        <?php } ?>
                    <div class="table-responsive border rounded p-1">
                      <table class="table table-striped">
                        <thead style="background: #003366; color: white; text-transform: uppercase;">
                            <tr>
                                <th class="font-weight-bold" style="padding: 10px;">#</th>
                                <th class="font-weight-bold" style="padding: 10px;">Staff Name</th>
                                <th class="font-weight-bold" style="padding: 10px;">User Type</th>
                                <th class="font-weight-bold" style="padding: 10px;">Role</th>
                                <th class="font-weight-bold" style="padding: 10px;">Gender</th>
                                <th class="font-weight-bold" style="padding: 10px;">Phone</th>
                                <th class="font-weight-bold" style="padding: 10px;">Email</th>
                                          <?php if ($usertype === 'Admin'): ?><th class="text-center font-weight-bold" style="padding: 10px;">Action</th>

<?php endif; ?>  
                                
                            </tr>
                        </thead>

                        <tbody id="myTable">
                                    <?php
                                    $sql = "SELECT * FROM tbladmin ORDER BY ID DESC";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                                    $cnt = 1;
                                    foreach ($results as $row) {
                                        ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt); ?></td>
                                            <td><?php echo htmlentities($row->Name); ?></td>
                                            <td><?php echo htmlentities($row->UserType); ?></td>
                                            <td><?php echo htmlentities($row->Role1); ?></td>
                                            <td><?php echo htmlentities($row->Gender); ?></td>
                                            <td><?php echo htmlentities($row->MobileNumber); ?></td>
                                            <td><?php echo htmlentities($row->Email); ?></td>
                                            <td>
                                            <?php if ($usertype === 'Admin'): ?>
<div class="text-center">
    <a href="edit-staff.php?id=<?php echo htmlentities($row->ID); ?>" class="btn btn-sm" style="background-color: #003366; color: white; margin-right: 5px;">
        <i class="icon-pencil"></i> Edit
    </a>
    <a href="reset-password.php?id=<?php echo htmlentities($row->ID); ?>" class="btn btn-sm" style="background-color: yellow; color: #003366; margin-right: 5px;">
        <i class="icon-lock"></i> Reset
    </a>
    <a href="manage-staff.php?delid=<?php echo htmlentities($row->ID); ?>" onclick="return confirm('Do you really want to delete?');" class="btn btn-sm" style="background-color: #003366; color: white;">
        <i class="icon-trash"></i> Delete
    </a>
</div>

<?php endif; ?>                        </td>
                                        </tr>
                                        <?php $cnt = $cnt + 1;
                                    } ?>
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
         <?php include_once('includes/footer.php');?>
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
    <script src="./vendors/moment/moment.min.js"></script>
    <script src="./vendors/daterangepicker/daterangepicker.js"></script>
    <script src="./vendors/chartist/chartist.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="./js/dashboard.js"></script>
    <!-- End custom js for this page -->
  </body>
</html>
