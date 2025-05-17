<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Fetch data from the database based on the provided ID
        $sql = "SELECT * FROM tbladmin WHERE ID = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Retrieve data from the database and assign to variables
            $staffname = $result['Name'];
            $gender = $result['Gender'];
            $userType = $result['UserType'];
            $role1 = $result['Role1'];
           
            $password = $result['Password'];
        } else {
            // No record found with the provided ID
            header('location:edit-staff.php?error=No record found for the provided ID.');
            exit();
        }
        if (isset($_POST['submit'])) {
          $password = $_POST['password'];
          // Generate a hashed password
          $hashed_password = md5($password);
  
          // Update data in tbladmin table
          $sql = "UPDATE tbladmin SET Password=:password WHERE ID=:id";
          $query = $dbh->prepare($sql);
          $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
          $query->bindParam(':id', $id, PDO::PARAM_INT);
          $query->execute();
          
          $affected_rows = $query->rowCount();
          if ($affected_rows > 0) {
             header('Location: manage-staff.php?id=' . urlencode($id) . '&success=Password+for+' . urlencode($staffname) . '+has+been+changed+successfully.');
exit;
          } else {
              header('location:manage-staff.php?id=' . urlencode($id) . '&error=Failed to change password for  ' . urlencode($staffname) . '  Please try again.');
              exit();
          }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <link rel="icon" type="image/jpg" href="logo.jpg">
     <title>Student Management System || Edit Staff Details</title>
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
    <style>
/* CSS to style the placeholder text */
::placeholder {
    color: #000000; /* Black placeholder text */
}
.btn-center {
    display: block; /* Make the button a block element */
    margin: 0 auto; /* Center the button horizontally */
}

/* CSS to style select dropdown */
select {
    appearance: none; /* Remove default dropdown arrow */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='4' viewBox='0 0 8 4'%3E%3Cpath fill='%23181824' d='M4 4L0 0h8z'/%3E%3C/svg%3E"); /* Custom dropdown arrow */
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 8px 4px;
}

/* CSS to style textarea */
textarea {
    resize: vertical; /* Allow vertical resizing */
}

    </style>
  </head>
  <body>
    <div class="container-scroller">
      <?php include_once('includes/header.php');?>
      <div class="container-fluid page-body-wrapper">
        <?php include_once('includes/sidebar.php');?>
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title"> Reset Password </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Reset Password</li>
                </ol>
              </nav>
            </div>
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
<h4 class="card-title" style="text-align: center; background : #003366; padding : 10px; font-size: 24px; font-weight: bold; color: white;">Reset Staff Password</h4> 

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
                    <form class="forms-sample" method="post" enctype="multipart/form-data">
 <div class="form-group row">
    <div class="col-md-6">
        <input type="text" name="staffname" class="form-control" placeholder="Staff Name" value="<?php echo $staffname; ?>" required readonly>
    </div>
    <div class="col-md-6">
        <input type="text" name="userType" class="form-control" placeholder="User Type" value="<?php echo $userType; ?>" required readonly>
    </div>
</div>

                      <div class="form-group">
                          <input type="password" name="password" class="form-control" placeholder="Password" value="" required>
                      </div>
                     <button type="submit" style="background-color: #032B44; color: #fff; border-radius: 40px; border: none; padding: 10px 20px; font-size: 16px;" name="submit">Change Password</button>
                    </form>
                  </div>
                </div>
              </div>
              <!-- content-wrapper ends -->
          <?php include_once('includes/footer.php');?>
            </div>
          </div>
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
