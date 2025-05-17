<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid']==0)) {
  header('location:logout.php');
  } else{
      $ID = $_SESSION['sturecmsaid'];
$sql = "SELECT UserType FROM tbladmin WHERE ID = :ID";
$query = $dbh->prepare($sql);
$query->bindParam(':ID', $ID, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);
$usertype = $result['UserType'];
           
   // Code for deletion
if(isset($_GET['delid']))
{
$rid=intval($_GET['delid']);
$sql="delete from tblpublicnotice where ID=:rid";
$query=$dbh->prepare($sql);
$query->bindParam(':rid',$rid,PDO::PARAM_STR);
$query->execute();
 echo "<script>alert('Data deleted');</script>"; 
  echo "<script>window.location.href = 'manage-public-notice.php'</script>";     


}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <title>Student  Management System|||Manage Public Notice</title>
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
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
              <h3 class="page-title"> Manage Public Notice </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Manage Public Notice</li>
                </ol>
              </nav>
            </div>
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
<div class="d-sm-flex align-items-center mb-4 flex-wrap">
    <h4 class="card-title mb-sm-0 mr-auto">Manage Public Notice</h4>
    
    <a href="manage-notice.php" style="background-color: #003366; color: white; padding: 8px 16px; border: none; text-decoration: none; border-radius: 4px; font-size: 14px; margin: 5px;">Private Notices</a>
    
    <?php if (in_array($usertype, ['Admin', 'Head Teacher', 'Deputy Head Teacher', 'Director'])) { ?>
        <a href="add-public-notice.php" style="background-color: #003366; color: white; padding: 8px 16px; border: none; text-decoration: none; border-radius: 4px; font-size: 14px; margin: 5px;">Add New Public Notice</a>
    <?php } ?>
</div>

                    <div class="table-responsive border rounded p-1">
  <table class="table table-striped">
                        <thead style="background-color: #003366; color: white;">
  <tr>
    <th class="font-weight-bold text-uppercase">#</th>
    <th class="font-weight-bold text-uppercase">Notice Title</th>
    <th class="font-weight-bold text-uppercase">Notice Date</th>
    <th class="font-weight-bold text-uppercase">Action</th>
  </tr>
</thead>

                        <tbody>
                           <?php
                            if (isset($_GET['pageno'])) {
            $pageno = $_GET['pageno'];
        } else {
            $pageno = 1;
        }
        // Formula for pagination
        $no_of_records_per_page =15;
        $offset = ($pageno-1) * $no_of_records_per_page;
       $ret = "SELECT ID FROM tblpublicnotice";
$query1 = $dbh -> prepare($ret);
$query1->execute();
$results1=$query1->fetchAll(PDO::FETCH_OBJ);
$total_rows=$query1->rowCount();
$total_pages = ceil($total_rows / $no_of_records_per_page);
$sql="SELECT * from tblpublicnotice";
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
                            <td><?php  echo htmlentities($row->NoticeTitle);?></td>
                            <td><?php  echo htmlentities($row->CreationDate);?></td>
                            <td>
<div>
    <!-- View Button with #003366 background -->
    <a href="edit-public-notice-detail.php?editid=<?php echo htmlentities($row->ID); ?>" 
       style="background-color: #003366 !important; color: white !important; padding: 5px 15px !important; border-radius: 5px !important; text-decoration: none !important; display: inline-block !important;">
        <i class="icon-eye"></i> View
    </a>
    <?php if (in_array($usertype, ['Admin', 'Head Teacher', 'Deputy Head Teacher', 'Director'])) { ?>
        <!-- Delete Button with Yellow background -->
         <a href="manage-public-notice.php?delid=<?php echo ($row->ID); ?>" 
              onclick="return confirm('Do you really want to Delete ?');" 
              style="background-color: yellow !important; color: #003366 !important; padding: 5px 15px !important; border-radius: 5px !important; text-decoration: none !important; display: inline-block !important;">
            <i class="icon-trash"></i> Delete
        </a>
    <?php } ?>
</div>

</td>
                             
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