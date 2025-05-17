<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_GET['id']) && isset($_GET['name']) && isset($_GET['class'])) {
        $stuID = $_GET['id'];
        $name = $_GET['name'];
        $class = $_GET['class'];
    
        if (isset($_POST['submit'])) {
            $bookid = strtoupper($_POST['bookid']); // Convert book number to uppercase
            $status = 'Available';
            // Check if the book exists
            $sql = "SELECT * FROM tblbook WHERE book_number = :bookid AND status = :status";
            $query = $dbh->prepare($sql);
            $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Fetch book details
                $subject = $result['subject'];
                $description = $result['description'];
                $author = $result['author'];
                $year = $result['year'];
                $bookClass = $result['class'];
                
                // Update tblbook with borrowed book details
                $currentDate = date('Y-m-d');
                $userID = $stuID;
                $takenBy = $name;
                $status = 'Borrowed';

                $sql = "UPDATE tblbook SET dateTaken = :currentDate, userID = :userID, takenBy = :takenBy, status = :status WHERE book_number = :bookid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':currentDate', $currentDate, PDO::PARAM_STR);
                $query->bindParam(':userID', $userID, PDO::PARAM_INT);
                $query->bindParam(':takenBy', $takenBy, PDO::PARAM_STR);
                $query->bindParam(':status', $status, PDO::PARAM_STR);
                $query->bindParam(':bookid', $bookid, PDO::PARAM_STR);

                if ($query->execute()) {
                    // Construct the success message with book details
                    $successMessage = 'You have successfully borrowed ' . $description . ', ' . $subject . ', written by ' . $author . ' in  ' . $year . ', book ' . $bookClass;
                    
                    // Redirect with success message
                    header('location: borrow-action.php?id=' . urlencode($stuID) . '&name=' . urlencode($name) . '&class=' . urlencode($class) . '&success=' . urlencode($successMessage));
                    exit();
                } else {
                    // Redirect with error message
                    header('location: borrow-action.php?id=' . urlencode($stuID) . '&name=' . urlencode($name) . '&class=' . urlencode($class) . '&error=Failed to borrow book.');
                    exit();
                }
                
            } else {
                header('location: borrow-action.php?id=' . urlencode($stuID) . '&name=' . urlencode($name) . '&class=' . urlencode($class) . '&error=The selected book may have been borrowed by someone.');
                exit();
            }
        }
    } else {
        header('location: borrow-action.php?error=Invalid parameters.');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <title>Student Management System || Borrowing Book</title>
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
                    <h3 class="page-title"> Borrowing Book </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Borrowing Book</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align: center;">Borrowing Book</h4>
                                <form class="forms-sample" method="post" enctype="form-data">
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
                                    <div class="form-group">
                                        <label for="bookid">Student Name</label>
                                        <input type="text" class="form-control" id="studentName" name="studentName" readonly value="<?php echo htmlspecialchars($name); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="fees">Class</label>
                                        <input type="text" class="form-control" id="className" name="className" readonly value="<?php echo htmlspecialchars($class); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="bookid">Book Number</label>
                                        <input type="text" class="form-control" id="bookid" name="bookid" placeholder="Enter book number" required>
                                    </div>
                                    <button type="submit" class="btn mr-2"  style="background-color: #003366; color: white;  border-radius : 40px; padding: 10px;" name="submit">Borrow</button>
                                    <!-- Cancel Button (with redirection to the previous page) -->
<button type="button" class="btn" style="background-color: yellow; color: #003366; padding: 10px; border-radius: 40px; border: none;" onclick="window.history.back();">
    Cancel
</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
       
        <!-- partial -->
    </div>
    <!-- main-panel ends -->
    <?php include_once('includes/footer.php');?>
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
<!-- nano scroller -->
<script src="../assets/js/lib/menubar/sidebar.js"></script>
<script src="../assets/js/lib/preloader/pace.min.js"></script>
<!-- End custom js for this page -->
</body>
</html>
