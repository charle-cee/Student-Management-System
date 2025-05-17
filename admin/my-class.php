<?php
session_start();
//error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid'] == 0)) {
    header('location:logout.php');
} else {
    $academicYearMessage = "";
    $termStartDate = "";
    $termEndDate = "";
    $ID = $_SESSION['sturecmsaid'];
    // Fetch active academic year
    $sql = "SELECT AcademicYear FROM tblcalendar WHERE Status = 'Active'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $academicYear = $result['AcademicYear'];
         

        // Fetch active term based on the academic year
        $sql = "SELECT * FROM tblcalendar WHERE AcademicYear = :academicYear AND Status = 'Active'";
        $query = $dbh->prepare($sql);
        $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $activeTerm = $result['ActiveTerm'];
             $exam = $result['ExaType'];
             $examentry = $result['ExamEntry'];
         $_SESSION['examentry_status'] = $examentry;  
            // Select the appropriate term start and end dates based on the active term
            switch ($activeTerm) {
                case 1:
                    $termStartDate = $result['Term1Start'];
                    $termEndDate = $result['Term1End'];
                    break;
                case 2:
                    $termStartDate = $result['Term2Start'];
                    $termEndDate = $result['Term2End'];
                    break;
                case 3:
                    $termStartDate = $result['Term3Start'];
                    $termEndDate = $result['Term3End'];
                    break;
                default:
                    // Handle if necessary
                    break;
            }
            $sql = "SELECT * FROM tbladmin WHERE ID= :ID";
            $query = $dbh->prepare($sql);
            $query->bindParam(':ID', $ID, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $class = $result['FormClass'];
 // Fetch and process major subject name
$majorIDString = $result['Major'];
$majorID = explode('-', $majorIDString)[0]; // Extract the first part as major ID
$sqlMajor = "SELECT Name FROM tblsubject WHERE ID = :majorID";
$queryMajor = $dbh->prepare($sqlMajor);
$queryMajor->bindParam(':majorID', $majorID, PDO::PARAM_INT);
$queryMajor->execute();
$majorResult = $queryMajor->fetch(PDO::FETCH_ASSOC);
$major = $majorResult['Name'] ?? ''; // Set a default empty string if no result found

// Fetch and process minor subject name
$minorIDString = $result['Minor'];
$minorID = explode('-', $minorIDString)[0]; // Extract the first part as minor ID
$sqlMinor = "SELECT Name FROM tblsubject WHERE ID = :minorID";
$queryMinor = $dbh->prepare($sqlMinor);
$queryMinor->bindParam(':minorID', $minorID, PDO::PARAM_INT);
$queryMinor->execute();
$minorResult = $queryMinor->fetch(PDO::FETCH_ASSOC);
$minor = $minorResult['Name'] ?? ''; // Set a default empty string if no result found

// Fetch and process other subject name
$otherIDString = $result['Other'];
$otherID = explode('-', $otherIDString)[0]; // Extract the first part as other ID
$sqlOther = "SELECT Name FROM tblsubject WHERE ID = :otherID";
$queryOther = $dbh->prepare($sqlOther);
$queryOther->bindParam(':otherID', $otherID, PDO::PARAM_INT);
$queryOther->execute();
$otherResult = $queryOther->fetch(PDO::FETCH_ASSOC);
$other = $otherResult['Name'] ?? ''; // Set a default empty string if no result found
            // Construct academic year message
            $academicYearMessage = $academicYear . " Academic year, Term " . $activeTerm . " from " . $termStartDate . " to " . $termEndDate;
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
     <title>Student Management System ||| Dashboard</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="vendors/chartist/chartist.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="css/style.css">
    <!-- End layout styles -->
   <!-- nano scroller -->
   <script src="../assets/js/lib/menubar/sidebar.js"></script>
    <script src="../assets/js/lib/preloader/pace.min.js"></script>
</head>
<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?php include_once('includes/header.php'); ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <?php include_once('includes/sidebar.php'); ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="card" style="border-radius: 10px; border: 1px solid #003366;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="d-sm-flex align-items-baseline report-summary-header" style="margin-bottom: 20px;">
                                                <!-- Academic Year Header -->
                                                <h5 class="text-uppercase" style="background-color: yellow; color: #003366; padding: 10px; font-weight: bold;">
                                                    <?php echo strtoupper($academicYearMessage); ?>
                                                </h5>
                                                
                                                <!-- "My Sign" Button -->
                                                <span class="ml-auto">
                                                    <a href="update-sign.php" class="btn p-2" style="background-color: #003366; color: white; border-radius: 5px; padding: 10px 20px;">
                                                        My Sign <i class="icon-refresh" style="color: white;"></i>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="" style="background-color: #003366; color: white; padding: 10px; text-align: center;">
                                        My Assigned Subjects and Form Class
                                    </div>

                                    <?php
                                    // Function to check if an ID is valid (not empty, null, 0, or out of range 1-12)
                                    function isValidId($id) {
                                        return !empty($id) && $id !== null && $id != 0 && $id >= 1 && $id <= 12;
                                    }

                                    // Check if all IDs are invalid
                                    if (!isValidId($majorID) && !isValidId($minorID) && !isValidId($otherID) && empty($class)) {
                                        echo '<div class="row"><div class="col-md-12"><p class="text-danger">Sorry, you\'re not allocated to any class.</p></div></div>';
                                    } else {
                                    ?>

                                    <div class="row report-inner-cards-wrapper">
                                        <!-- Major Card -->
                                        <?php if (isValidId($majorID)) { ?>
                                        <div class="col-md-8 col-xl-3 report-inner-card" style="margin-bottom: 20px;">
                                            <div class="inner-card-text" style="background-color: #003366; color: white; padding: 20px; border-radius: 10px;">
                                                <h4><a href="major.php?year=<?php echo htmlentities($academicYear); ?>&term=<?php echo htmlentities($activeTerm); ?>&subid=<?php echo htmlentities($majorIDString); ?>&exam=<?php echo htmlentities($exam); ?>" style="color: yellow;"><?php echo htmlentities($major); ?></a></h4>
                                                <h4><a href="major.php?year=<?php echo htmlentities($academicYear); ?>&term=<?php echo htmlentities($activeTerm); ?>&subid=<?php echo htmlentities($majorIDString); ?>&exam=<?php echo htmlentities($exam); ?>" class="text-white"><span class="icon-eye"></span></a></h4>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <!-- Minor Card -->
                                        <?php if (isValidId($minorID)) { ?>
                                        <div class="col-md-8 col-xl-3 report-inner-card" style="margin-bottom: 20px;">
                                            <div class="inner-card-text" style="background-color: #003366; color: white; padding: 20px; border-radius: 10px;">
                                                <h4><a href="major.php?year=<?php echo htmlentities($academicYear); ?>&term=<?php echo htmlentities($activeTerm); ?>&subid=<?php echo htmlentities($minorIDString); ?>&exam=<?php echo htmlentities($exam); ?>" style="color: yellow;"><?php echo htmlentities($minor); ?></a></h4>
                                                <h4><a href="major.php?year=<?php echo htmlentities($academicYear); ?>&term=<?php echo htmlentities($activeTerm); ?>&subid=<?php echo htmlentities($minorIDString); ?>&exam=<?php echo htmlentities($exam); ?>" class="text-white"><span class="icon-eye"></span></a></h4>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <!-- Other Card -->
                                        <?php if (isValidId($otherID)) { ?>
                                        <div class="col-md-8 col-xl-3 report-inner-card" style="margin-bottom: 20px;">
                                            <div class="inner-card-text" style="background-color: #003366; color: white; padding: 20px; border-radius: 10px;">
                                                <h4><a href="major.php?year=<?php echo htmlentities($academicYear); ?>&term=<?php echo htmlentities($activeTerm); ?>&subid=<?php echo htmlentities($otherIDString); ?>&exam=<?php echo htmlentities($exam); ?>" style="color: yellow;"><?php echo htmlentities($other); ?></a></h4>
                                                <h4><a href="major.php?year=<?php echo htmlentities($academicYear); ?>&term=<?php echo htmlentities($activeTerm); ?>&subid=<?php echo htmlentities($otherIDString); ?>&exam=<?php echo htmlentities($exam); ?>" class="text-white"><span class="icon-eye"></span></a></h4>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <!-- Class Card -->
                                        <?php if (!empty($class)) { ?>
                                        <div class="col-md-8 col-xl-3 report-inner-card" style="margin-bottom: 20px;">
                                            <div class="inner-card-text" style="background-color: #003366; color: white; padding: 20px; border-radius: 10px;">
                                                <h4><a href="my-form-class.php?class=<?php echo htmlentities($class); ?>" style="color: yellow;"><?php echo htmlentities($class); ?></a></h4>
                                                <h4><a href="my-form-class.php?class=<?php echo htmlentities($class); ?>" class="text-white"><span class="icon-eye"></span></a></h4>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <?php include_once('includes/footer.php'); ?>
                <!-- partial -->
            </div>
        </div>
    </div>
</body>
    <!-- Scripts -->
        <script src="vendors/js/vendor.bundle.base.js"></script>
        <script src="vendors/chart.js/Chart.min.js"></script>
        <script src="vendors/moment/moment.min.js"></script>
        <script src="vendors/daterangepicker/daterangepicker.js"></script>
        <script src="vendors/chartist/chartist.min.js"></script>
        <script src="js/off-canvas.js"></script>
        <script src="js/misc.js"></script>
        <script src="js/dashboard.js"></script>
</html>

