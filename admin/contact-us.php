<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid']==0)) {
  header('location:logout.php');
  } else{
   if(isset($_POST['submit']))
  {


 $pagetitle=$_POST['pagetitle'];
$pagedes=$_POST['pagedes'];
$mobnum=$_POST['mobnum'];
$email=$_POST['email'];
$sql="update tblpage set PageTitle=:pagetitle,PageDescription=:pagedes,Email=:email,MobileNumber=:mobnum where  PageType='contactus'";
$query=$dbh->prepare($sql);
$query->bindParam(':pagetitle',$pagetitle,PDO::PARAM_STR);
$query->bindParam(':pagedes',$pagedes,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':mobnum',$mobnum,PDO::PARAM_STR);
$query->execute();
header('location:contact-us.php?success=Contact us has been updated.');
exit();
  }

  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <title>Student  Management System|| Update Contact Us</title>
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <!-- plugins:css -->
    
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link rel="shortcut icon" href="assets/img/ronk1.jpg" />
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- <link href="assets/vendor/icofont/icofont.min.css" rel="stylesheet"> -->
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/owl.carousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style22.css" rel="stylesheet">
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
    <script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
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
              <h3 class="page-title"> Update Contact Us </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Update Contact Us</li>
                </ol>
              </nav>
            </div><div class="row">
    <div class="text-right">
        <!-- Update About Us Button -->
        <a href="about-us.php" class="btn btn-center" style="align : center; background-color: #003366; color: white; border-radius: 30px; padding: 10px 20px;">
            Update About Us
        </a>
    </div>
    
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title" style="text-align: center; background :#003366; color: white; padding :10px;">Update Contact Us</h4>

                <form class="forms-sample" method="post">
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
                    
                    <?php
                    $sql = "SELECT * from  tblpage where PageType='contactus'";
                    $query = $dbh -> prepare($sql);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                    $cnt = 1;
                    if ($query->rowCount() > 0) {
                        foreach ($results as $row) {
                    ?>    
                        <div class="form-group">
                            <label for="exampleInputName1" style="color: #003366;">Page Title:</label>
                            <input type="text" name="pagetitle" value="<?php echo $row->PageTitle;?>" class="form-control" required='true' style="border-radius: 5px; border: 1px solid #003366;">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName1" style="color: #003366;">Page Description:</label>
                            <textarea type="text" name="pagedes" class="form-control" required='true' style="border-radius: 5px; border: 1px solid #003366;"><?php echo $row->PageDescription;?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName1" style="color: #003366;">Email:</label>
                            <input type="text" name="email" id="email" required="true" value="<?php echo $row->Email;?>" class="form-control" style="border-radius: 5px; border: 1px solid #003366;">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName1" style="color: #003366;">Mobile Number:</label>
                            <input type="text" name="mobnum" id="mobnum" required="true" value="<?php echo $row->MobileNumber;?>" class="form-control" maxlength="10" pattern="[0-9]+" style="border-radius: 5px; border: 1px solid #003366;">
                        </div>
                    <?php
                        $cnt = $cnt + 1;
                        }
                    }
                    ?>
                    <button type="submit" class="btn" style="background-color: #003366; color: white; border-radius: 30px; padding: 10px 20px;" name="submit">Update</button>
        <a href="javascript:history.back()" class="btn" style="background-color: yellow; color: #003366; border-radius: 30px; padding: 10px 20px; margin-left: 10px;">Cancel</a>
 
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
    
  <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/jquery/jquery.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/jquery.easing/jquery.easing.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/waypoints/jquery.waypoints.min.js"></script>
  <script src="assets/vendor/counterup/counterup.min.js"></script>
  <script src="assets/vendor/owl.carousel/owl.carousel.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

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
</html><?php }  ?>