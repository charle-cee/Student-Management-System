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
                                        All subjects
                                    </div>

<?php
// Fetch the list of subjects (replace 'tblsubjects' with your actual table name)
$sqlSubjects = "SELECT ID, Name FROM tblsubject"; // Adjust the query as needed
$querySubjects = $dbh->prepare($sqlSubjects);
$querySubjects->execute();
$subjects = $querySubjects->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row report-inner-cards-wrapper">
    <?php foreach ($subjects as $subject): ?>
        <?php
        // Generate the modified subid (e.g., concatenate with additional values)
        $subid = $subject['ID']; // Base subid
        $modifiedSubid = $subid . '-1-2-3-4'; // Modify the subid as needed
        ?>
        <!-- Subject Card -->
        <div class="col-md-8 col-xl-3 report-inner-card" style="margin-bottom: 20px;">
            <div class="inner-card-text" style="background-color: #003366; color: white; padding: 20px; border-radius: 10px;">
                <!-- Subject Name -->
                <h4>
                    <a href="major.php?year=<?php echo htmlentities($academicYear); ?>&term=<?php echo htmlentities($activeTerm); ?>&subid=<?php echo htmlentities($modifiedSubid); ?>&exam=<?php echo htmlentities($exam); ?>" style="color: yellow;">
                        <?php echo htmlentities($subject['Name']); ?>
                    </a>
                </h4>
                <!-- View Link -->
                <h4>
                    <a href="major.php?year=<?php echo htmlentities($academicYear); ?>&term=<?php echo htmlentities($activeTerm); ?>&subid=<?php echo htmlentities($modifiedSubid); ?>&exam=<?php echo htmlentities($exam); ?>" class="text-white">
                        <span class="icon-eye"></span>
                    </a>
                </h4>
            </div>
        </div>
    <?php endforeach; ?>
</div>
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

