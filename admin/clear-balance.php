<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_GET['id'])) {
        $stuID = $_GET['id'];
        // Fetch active academic year and term
        $sql = "SELECT * FROM tblcalendar WHERE Status = 'Active'";
        $query = $dbh->prepare($sql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $academicYear = $result['AcademicYear'];
            $activeTerm = $result['ActiveTerm'];
        }
        // Check if the student exists
        $sql2 = "SELECT * FROM tblaccounts WHERE stuID = :stuID AND term = :activeTerm AND academic_year = :academicYear";
        $query = $dbh->prepare($sql2);
        $query->bindParam(':stuID', $stuID, PDO::PARAM_INT);
        $query->bindParam(':activeTerm', $activeTerm, PDO::PARAM_INT);
        $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
        $query->execute();
        $result2 = $query->fetchAll(PDO::FETCH_ASSOC);

        if (isset($_POST['submit'])) {
            // Get selected student financial details from the form post data
            $stuID = $_POST['stuID'];
            $paid = $_POST['paid'];
            $stuname = $_POST['stuname'];
            $balance = $_POST['balance'];
            $date = date('Y-m-d H:i:s');

            // Check if the paid amount exceeds the balance
            if ($paid > $balance) {
                header('location: accounts.php?error=The amount to be cleared exceeds the current student balance.');
                exit();
            }

            // Check if the balance is already zero
            if ($balance == 0) {
                header('location: accounts.php?error=The student has no outstanding fees balance.');
                exit();
            }

            // Determine the status based on the paid amount and balance
            if ($paid == $balance) {
                $status = 'Fully Paid';
            } else {
                $status = 'Partially Paid';
            }
            // Calculate the new balance after payment
            $newbalance = $balance - $paid;
            // Update the student's account with the new balance and payment details
            $sql3 = "UPDATE tblaccounts SET balance = :newbalance, paid = :paid, date_paid= :date, status =:status WHERE stuID = :stuID AND term = :activeTerm AND academic_year = :academicYear";
            $query = $dbh->prepare($sql3);
            $query->bindParam(':stuID', $stuID, PDO::PARAM_INT);
            $query->bindParam(':activeTerm', $activeTerm, PDO::PARAM_INT);
            $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
            $query->bindParam(':newbalance', $newbalance, PDO::PARAM_INT);
            $query->bindParam(':paid', $paid, PDO::PARAM_INT);
            $query->bindParam(':date', $date, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->execute();
            if ($query->execute()) {
                // Redirect to the accounts page with success message
                header('location: accounts.php?success=You have ' . urlencode($status) . ' fees for ' . urlencode($stuname) . ' for ' . urlencode($academicYear) . ', Term ' . urlencode($activeTerm) . '.');
                exit();
            } else {
                // Redirect to the accounts page with error message
                header('location: accounts.php?error=Failed to clear fees record.');
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
<title>Student Management System || Returning Book</title>
<link rel="icon" type="image/jpg" href="logo.jpg">
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
                    <h3 class="page-title"> Clear balances </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Clear Balances</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align: center; background-color: #003366; color: white; border-radius: 40px; padding: 10px"">Clearing Balance</h4>
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
                                <?php }?>
                                <form class="forms-sample" method="post" enctype="multipart/form-data">
                                    <?php foreach ($result2 as $results) { ?>
                                    <input type="hidden" name="stuID" value="<?php echo htmlentities($results['stuID']); ?>" class="form-control" readonly>
                                    <!-- Student Details -->
                                    <div class="form-group">
                                        <label for="exampleInputName1">Student Details</label>
                                        <input type="text" name="stuname" value="<?php echo htmlentities($results['name']) . ' &nbsp; Form ' . htmlentities($results['class']); ?>" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputName1">Balance</label>
                                        <input type="text" name="balance" value="<?php echo htmlentities($results['balance']); ?>" class="form-control" readonly>
                                    </div> 
                                    <div class="form-group">
                                        <label for="exampleInputName1">Paid</label>
                                        <input type="text" name="paid" value="<?php echo htmlentities($results['paid']); ?>" class="form-control">
                                    </div> 
                                    <div class="form-group">
                                        <label for="exampleInputName1">Status</label>
                                        <input type="text" name="status" value="<?php echo htmlentities($results['status']); ?>" class="form-control" readonly>
                                    </div> 
                                    <?php } ?>
<button type="submit" onclick="return confirm('Are you sure you want to clear this amount for <?php echo htmlentities($results['name']); ?>?')" class="btn mr-2" style="background-color: #003366; color: white; border-radius: 40px; padding: 10px" name="submit">Clear</button>

                            <a href="accounts.php" class="btn"  style="background-color: yellow; color: #003366; border-radius: 40px; padding: 10px">Student List</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
    <!-- main-panel ends -->
    <?php include_once('includes/footer.php');?>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->

        <!-- partial -->
    </div>
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery library -->
</body>
</html>
