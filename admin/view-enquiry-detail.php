<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid']==0)) {
  header('location:logout.php');
  } else{
      $aid = $_SESSION['sturecmsaid'];
$sql = "SELECT UserType FROM tbladmin WHERE ID = :ID";
$query = $dbh->prepare($sql);
$query->bindParam(':ID', $aid, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);
$usertype = $result['UserType'];

  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <link rel="icon" type="image/jpg" href="logo.jpg">
     <title>Student  Management System|| Update Annoucement</title>
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
              <h3 class="page-title"> View Enquiry </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Viewing  Enquiry</li>
                </ol>
              </nav>
            </div>
            <div class="row">
          
              <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">

                   
                  <?php
$eid = $_GET['viewid'];
// Step 1: Select the enquiry details
$sql_select = "SELECT * FROM tblenquiry WHERE EnquiryID = :eid AND Admid = :aid";
$query_select = $dbh->prepare($sql_select);
$query_select->bindParam(':eid', $eid, PDO::PARAM_STR);
$query_select->bindParam(':aid', $aid, PDO::PARAM_STR);
$query_select->execute();
$results = $query_select->fetchAll(PDO::FETCH_OBJ);

// Step 2: Update the status to 'Read'
if ($query_select->rowCount() > 0) {
    $sql_update = "UPDATE tblenquiry SET Status = 'Read' WHERE EnquiryID = :eid AND Admid = :aid";
    $query_update = $dbh->prepare($sql_update);
    $query_update->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query_update->bindParam(':aid', $aid, PDO::PARAM_STR);
    $query_update->execute();
}

// Display the enquiry details
$cnt = 1;
if ($query_select->rowCount() > 0) {
    foreach ($results as $row) {
?>
        <!-- Enquiry Details Card -->
        <div class="card" style="border: none; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: white;">
            <div class="card-header" style="text-align:center;background-color: yellow; color: #003366; border-radius: 10px 10px 0 0; padding: 15px;">
                <h4 class="card-title mb-0">Enquiry Details</h4>
            </div>
            <div class="card-body" style="padding: 20px;">
                <!-- Full Name -->
                <div class="form-group">
                    <label for="fullname" style="color: #003366; font-weight: bold;">Full Name</label>
                    <input type="text" id="fullname" value="<?php echo htmlentities($row->FullName); ?>" class="form-control" readonly style="background: transparent; border: none; color: #003366; font-size: 16px;">
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" style="color: #003366; font-weight: bold;">Email</label>
                    <input type="text" id="email" value="<?php echo htmlentities($row->Email); ?>" class="form-control" readonly style="background: transparent; border: none; color: #003366; font-size: 16px;">
                </div>

                <!-- Mobile Number -->
                <div class="form-group">
                    <label for="mobile" style="color: #003366; font-weight: bold;">Mobile Number</label>
                    <input type="text" id="mobile" value="<?php echo htmlentities($row->MobileNumber); ?>" class="form-control" readonly style="background: transparent; border: none; color: #003366; font-size: 16px;">
                </div>

                <!-- Message -->
                <div class="form-group">
                    <label for="message" style="color: #003366; font-weight: bold;">Message</label>
                    <textarea id="message" rows="4" class="form-control" readonly style="background: transparent; border: 1px solid #003366; border-radius: 5px; color: #003366; font-size: 16px; resize: none;"><?php echo htmlentities($row->Message); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Suggested Responses Card -->
        <div class="card mt-4" style="border: none; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: white;">
            <div class="card-header" style="text-align:center;background-color: yellow; color: white; border-radius: 10px 10px 0 0; padding: 15px;">
                <h4 class="card-title mb-0">Suggested Responses</h4>
            </div>
            <div class="card-body" style="padding: 20px;">
                <?php if (!empty($row->MobileNumber)) { ?>
                    <div class="mb-3">
                        <p style="color: #003366; font-weight: bold;">Mobile Number Available:</p>
                        <div class="d-flex gap-2">
                            <a href="tel:<?php echo htmlentities($row->MobileNumber); ?>" class="btn" style="background-color: #FFD700; color: #003366; border: none; border-radius: 5px; padding: 10px 20px; font-weight: bold;">Call</a>
                            &nbsp;&nbsp;  <a href="sms:<?php echo htmlentities($row->MobileNumber); ?>" class="btn" style="background-color: #FFD700; color: #003366; border: none; border-radius: 5px; padding: 10px 20px; font-weight: bold;">Send SMS</a>
                            &nbsp;&nbsp; <a href="https://api.whatsapp.com/send?phone=<?php echo htmlentities($row->MobileNumber); ?>" class="btn" style="background-color: #FFD700; color: #003366; border: none; border-radius: 5px; padding: 10px 20px; font-weight: bold;" target="_blank">WhatsApp</a>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!empty($row->Email)) { ?>
                    <div>
                        <p style="color: #003366; font-weight: bold;">Or Via Email</p>
                        <a href="mailto:<?php echo htmlentities($row->Email); ?>" class="btn" style="background-color: #FFD700; color: #003366; border: none; border-radius: 5px; padding: 10px 20px; font-weight: bold;">Open Email</a>
                    </div>
                <?php } ?>
            </div>
        </div>
<?php
        $cnt = $cnt + 1;
    }
} else {
    echo "<div class='alert alert-danger'>No enquiry found.</div>";
}
?>                  </div>
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
</html><?php }  ?>