<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid']==0)) {
  header('location:logout.php');
  } else{
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <link rel="icon" type="image/jpg" href="logo.jpg">
     <title>Student  Management System|||Manage Calendar</title>
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
              <h3 class="page-title"> Manage Calendar </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Manage Calendar</li>
                </ol>
              </nav>
            </div>
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="d-sm-flex align-items-center mb-4">
                      <h4 class="card-title mb-sm-0">Academic Calendars</h4>
                      <a href="academic-year.php" class="text ml-auto mb-3 mb-sm-0" style="background-color: yellow; color : #003366; padding: 10px;"> Add New Academic Year</a>
                    </div>
                    <div class="table-responsive border rounded p-1">
                      <table class="table table-striped">
                        <thead>
                          <tr style="background-color: #003366; color: white; padding: 10px; text-transform : uppercase">
                            <th class="font-weight-bold">#</th>
                            <th class="font-weight-bold">Academic Year</th>
                            <th class="font-weight-bold">Term 1 Start</th>
                            <th class="font-weight-bold">Term 1 End</th>
                            <th class="font-weight-bold">Term 1 Fees</th>
                            <th class="font-weight-bold">Term 2 Start</th>
                            <th class="font-weight-bold">Term 2 End</th>
                            <th class="font-weight-bold">Term 2 Fees</th>                            
                            <th class="font-weight-bold">Term 3 Start</th>
                            <th class="font-weight-bold">Term 3 End</th>
                            <th class="font-weight-bold">Term 3 Fees</th>
                            <th class="font-weight-bold">Status</th>
                            
                          </tr>
                        </thead>
                        <tbody>
                           <?php
                      
// Formula for pagination
$sql = "SELECT * FROM tblcalendar ORDER BY ID DESC";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $row)
{               ?>   
                          <tr>
                           
                            <td><?php echo htmlentities($cnt);?></td>
                            <td><?php  echo htmlentities($row->AcademicYear);?></td>
                            <td><?php  echo htmlentities($row->Term1Start);?></td>
                            <td> <?php  echo htmlentities($row->Term1End);?></td>
                            <td><?php  echo number_format(htmlentities($row->Term1Fees));?></td>
                            <td><?php  echo htmlentities($row->Term2Start);?></td>
                            <td> <?php  echo htmlentities($row->Term2End);?></td>
                            <td><?php  echo number_format(htmlentities($row->Term2Fees));?></td>
                            <td><?php  echo htmlentities($row->Term3Start);?></td>
                            <td> <?php  echo htmlentities($row->Term3End);?></td>
                            <td><?php  echo number_format(htmlentities($row->Term3Fees));?></td>
                            <td><?php  echo htmlentities($row->Status);?></td>
                            <td>
                          </tr><?php $cnt=$cnt+1;}} ?>
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
</html><?php }  ?>