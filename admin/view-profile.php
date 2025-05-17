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
  <link rel="icon" type="image/jpg" href="logo.jpg">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <title>Student  Management System|| Profile</title>
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
              <h3 class="page-title"> My Profile </h3>
              <nav aria-label="breadcrumb">
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
        <h4 class="card-title text-center" style="color: #003366; text-transform: uppercase; font-weight: bold;">
            My Profile
        </h4>

        <?php
        $id = $_SESSION['sturecmsaid'];
        $sql = "SELECT * FROM tbladmin WHERE ID=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        // Check if any results were found
        if ($query->rowCount() > 0) {
            // Fetch the first record
            $row = $results[0];
        ?>

        <!-- Profile Information Display -->
        <div class="table-responsive border rounded p-3" style="background-color: #f9f9f9;">
            <table class="table table-borderless" style="width: 100%;">
                <tbody>
                    <tr>
                        <th style="width: 30%; color: #003366; text-transform: uppercase;">Staff Name:</th>
                        <td><?php echo htmlentities($row->Name); ?></td>
                    </tr>
                    <tr>
                        <th style="color: #003366; text-transform: uppercase;">User Type:</th>
                        <td><?php echo htmlentities($row->UserType); ?></td>
                    </tr>
                    <tr>
                        <th style="color: #003366; text-transform: uppercase;">Role:</th>
                        <td><?php echo htmlentities($row->Role1); ?></td>
                    </tr>
                    <tr>
                        <th style="color: #003366; text-transform: uppercase;">Gender:</th>
                        <td><?php echo htmlentities($row->Gender); ?></td>
                    </tr>
                    <tr>
                        <th style="color: #003366; text-transform: uppercase;">Phone:</th>
                        <td><?php echo htmlentities($row->MobileNumber); ?></td>
                    </tr>
                    <tr>
                        <th style="color: #003366; text-transform: uppercase;">Email:</th>
                        <td><?php echo htmlentities($row->Email); ?></td>
                    </tr>
                    <tr>
                        <th style="color: #003366; text-transform: uppercase;">Admission Date:</th>
                        <td><?php echo htmlentities($row->AdminRegdate); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
            <p class="text-center text-danger">No profile information found.</p>
        <?php } ?>

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
</html><?php } ?>