<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid']==0)) {
  header('location:logout.php');
  } else{
   // Code for deletion
if(isset($_GET['delid']))
{
$rid=intval($_GET['delid']);
$sql="delete from tbladmin where ID=:rid";
$query=$dbh->prepare($sql);
$query->bindParam(':rid',$rid,PDO::PARAM_STR);
$query->execute();    
  header('location:manage-staff.php?success=Deleted sucessfully.');
  exit();
}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <title>Student  Management System|||Manage Form Teachers</title>
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
              <h3 class="page-title"> Manage Form Teachers </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Manage Form Teachers</li>
                </ol>
              </nav>
            </div>
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                  <div class="d-sm-flex align-items-center mb-4">
                      <h4 class="card-title mb-sm-0">Manage Form Teachers</h4>
                      <a href="assign-class.php" class="text ml-auto mb-3 mb-sm-0" style="background :#003366; color : white;padding :10px;"><i class="icon-plus"></i> Assign New Class</a>
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
                        <thead>
                        <tr style="background :#003366; color : white;padding :10px; text-transform : uppercase;">
                                        <th class="font-weight-bold">#</th>
                                        <th class="font-weight-bold">Staff Name</th>
                                        <th class="font-weight-bold">User Type</th>
                                        <th class="font-weight-bold">Class Assigned</th>
                                        <th class="font-weight-bold">Gender</th>
                                        <th class="font-weight-bold">Phone</th>
                                        <th class="font-weight-bold">Email</th>
                                        <th class="font-weight-bold">Admission Date</th>
                                        <th class="font-weight-bold">Action</th>
                                    </tr>
                        </thead>
                        <tbody>
                                    <?php
                                    $sql = "SELECT * FROM tbladmin WHERE FormClass IS NOT NULL";
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
                                            <td><?php echo htmlentities($row->FormClass); ?></td>
                                            <td><?php echo htmlentities($row->Gender); ?></td>
                                            <td><?php echo htmlentities($row->MobileNumber); ?></td>
                                            <td><?php echo htmlentities($row->Email); ?></td>
                                            <td><?php echo htmlentities($row->AdminRegdate); ?></td>
                                            <td>
                                            <div>
                                                  <a href="edit-form-class.php?id=<?php echo htmlentities($row->ID); ?>"><i class="icon-pencil"  style="background-color: yellow ; color: #003366; padding: 10px;"> Edit</i></a>
                                              </div>
                                              </td>
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