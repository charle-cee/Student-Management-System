<?php
session_start();
// error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid'] == 0)) {
    header('location:logout.php');
} else {
    $academicYearMessage = "";
    $termStartDate = "";
    $termEndDate = "";

    // Fetch active academic year
    $sql = "SELECT AcademicYear FROM tblcalendar WHERE Status = 'Active'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $academicYear = $result['AcademicYear'];

        // Fetch active term based on the academic year
        $sql = "SELECT ActiveTerm, Term1Start, Term1End, Term2Start, Term2End, Term3Start, Term3End FROM tblcalendar WHERE AcademicYear = :academicYear AND Status = 'Active'";
        $query = $dbh->prepare($sql);
        $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $activeTerm = $result['ActiveTerm'];

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="images/ico" href="images/sms.ICO">
    <title>Student Management System ||| Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .report-summary-header {
            background-color: #003366;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .report-summary-header h5 {
            color: #ffffff;
            font-weight: bold;
            margin: 0;
        }

        .report-summary-header a {
            color: yellow;
            text-decoration: none;
            font-weight: bold;
        }

        .report-summary-header a:hover {
            text-decoration: underline;
        }

        .report-inner-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }

        .report-inner-card:hover {
            background-color: #f1f1f1;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
            display: block;
            margin-bottom: 10px;
        }

        .inner-card-icon {
            background-color: #003366;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }

        .inner-card-icon i {
            color: #ffffff;
            font-size: 24px;
        }

        .btn-custom {
            background-color: yellow;
            color: #003366;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: bold;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #f7dc6f;
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <!-- Header -->
        <?php include_once('includes/header.php'); ?>
        
        <!-- Sidebar -->
        <?php include_once('includes/sidebar.php'); ?>
        
        <!-- Main Content -->
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-md-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <!-- Header Section -->
                                <div class="report-summary-header">
                                    <h5><?php echo $academicYearMessage; ?></h5>
                                    <a href="between-dates-reports.php">
                                        Reports <i class="fas fa-sync-alt"></i>
                                    </a>
                                </div>
                                
                                <!-- Report Inner Cards -->
                                <div class="row">
                                    <!-- Fees Card -->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="report-inner-card">
                                            <div class="inner-card-icon">
                                                <i class="fas fa-wallet"></i>
                                            </div>
                                            <span class="report-title">Fees</span>
                                            <button class="btn-custom" onclick="window.location.href='update-fees.php?academic_year=<?php echo urlencode($academicYear); ?>';">
                                                Update
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Contact Details Card -->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="report-inner-card">
                                            <div class="inner-card-icon">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <span class="report-title">Contact Details</span>
                                            <button class="btn-custom" onclick="window.location.href='contact-us.php';">
                                                Update
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Active Term Card -->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="report-inner-card">
                                            <div class="inner-card-icon">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                            <span class="report-title">Active Term</span>
                                            <button class="btn-custom" onclick="window.location.href='update-active-term.php';">
                                                Update
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Manage Classes Card -->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="report-inner-card">
                                            <div class="inner-card-icon">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                            <span class="report-title">Manage Classes</span>
                                            <button class="btn-custom" onclick="window.location.href='manage-class.php';">
                                                Update
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Student Details Card -->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="report-inner-card">
                                            <div class="inner-card-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <span class="report-title">Student Details</span>
                                            <button class="btn-custom" onclick="window.location.href='student-updates.php';">
                                                Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <?php include_once('includes/footer.php'); ?>
        </div>
    </div>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
<?php } ?>