<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
error_reporting(0);
if (strlen($_SESSION['sturecmsaid']==0)) {
  header('location:logout.php');
  } else{
if(isset($_POST['submit']))
{
$adminid=$_SESSION['sturecmsaid'];
$cpassword=md5($_POST['currentpassword']);
$newpassword=md5($_POST['newpassword']);
$sql ="SELECT ID FROM tbladmin WHERE ID=:adminid and Password=:cpassword";
$query= $dbh -> prepare($sql);
$query-> bindParam(':adminid', $adminid, PDO::PARAM_STR);
$query-> bindParam(':cpassword', $cpassword, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);

if($query -> rowCount() > 0)
{
$con="update tbladmin set Password=:newpassword where ID=:adminid";
$chngpwd1 = $dbh->prepare($con);
$chngpwd1-> bindParam(':adminid', $adminid, PDO::PARAM_STR);
$chngpwd1-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
$chngpwd1->execute();

header("location: change-password.php?success=Your password has successully changed");
} else {
header("location : change-password.php?error=Your current password is wrong");

}
}
  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Student  Management System|| Change Password</title>
    <!-- plugins:css -->
    <link rel="icon" type="image/jpg" href="logo.jpg">
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
    <script type="text/javascript">
function checkpass()
{
if(document.changepassword.newpassword.value!=document.changepassword.confirmpassword.value)
{
alert('New Password and Confirm Password field does not match');
document.changepassword.confirmpassword.focus();
return false;
}
return true;
}   

</script>
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
              <h3 class="page-title"> Change Password </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                </ol>
              </nav>
            </div><div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <!-- Header with Background Color -->
                <div style="background-color: #003366; padding: 15px; border-radius: 5px;">
                    <h4 class="card-title text-center" style="color: #fff; text-transform: uppercase; font-weight: bold; margin: 0;">
                        Change Password
                    </h4>
                </div>
                <br>

                <!-- Display Success or Error Messages -->
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger alert-dismissible text-center">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <b><?php echo $_GET['error']; ?></b>
                    </div>
                <?php } ?>
                
                <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success alert-dismissible text-center">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <b><?php echo $_GET['success']; ?></b>
                    </div>
                <?php } ?>

                <!-- Password Change Form -->
                <form class="forms-sample" name="changepassword" method="post" onsubmit="return checkpass();" style="margin-top: 20px;">
                    <div class="form-group">
                        <label for="currentpassword" style="font-weight: bold;">Current Password</label>
                        <input type="password" name="currentpassword" id="currentpassword" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="newpassword" style="font-weight: bold;">New Password</label>
                        <input type="password" name="newpassword" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmpassword" style="font-weight: bold;">Confirm Password</label>
                        <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" required>
                    </div>
                    
                   <div class="text-center">
    <!-- Change Button -->
    <button type="submit" class="btn" style="background-color: #003366; color: white; border-radius: 40px; margin-right: 10px;" name="submit">
        Change
    </button>
    
    <!-- Cancel Button -->
    <button type="button" class="btn" onclick="history.back();" style="background-color: yellow; color: #003366; border-radius: 40px;">
        Cancel
    </button>
</div>

                </form>
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
</html><?php }  ?>