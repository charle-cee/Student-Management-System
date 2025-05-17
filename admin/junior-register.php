<?php
session_start();
error_reporting(E_ALL); // Enable error reporting for debugging
ini_set('display_errors', 1); // Display errors on the screen
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
}

// Fetch active academic year and term
$sql = "SELECT AcademicYear, ActiveTerm, Term1Start, Term1End, Term2Start, Term2End, Term3Start, Term3End, Term1Fees, Term2Fees, Term3Fees FROM tblcalendar WHERE Status = 'Active'";
$query = $dbh->prepare($sql);
$query->execute();
$academicYearData = $query->fetch(PDO::FETCH_ASSOC);

if (!$academicYearData) {
    header('location: junior-register.php?error=Active academic year not found');
    exit();
}

$academicYear = $academicYearData['AcademicYear'];
$activeTerm = $academicYearData['ActiveTerm'];

// Determine term start, end dates, and fees based on active term
switch ($activeTerm) {
    case 1:
        $termStartDate = $academicYearData['Term1Start'];
        $termEndDate = $academicYearData['Term1End'];
        $fees = $academicYearData['Term1Fees'];
        break;
    case 2:
        $termStartDate = $academicYearData['Term2Start'];
        $termEndDate = $academicYearData['Term2End'];
        $fees = $academicYearData['Term2Fees'];
        break;
    case 3:
        $termStartDate = $academicYearData['Term3Start'];
        $termEndDate = $academicYearData['Term3End'];
        $fees = $academicYearData['Term3Fees'];
        break;
    default:
        header('location: junior-register.php?error=Invalid active term');
        exit();
}

$academicYearMessage = "$academicYear Academic year, Term $activeTerm from $termStartDate to $termEndDate";
?>    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
        <link rel="icon" type="image/jpg" href="logo.jpg">
        <title>Student Management System|||Manage Students</title>
        <!-- plugins:css -->
        <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
        <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
        <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
        <!-- endinject -->
        <!-- Plugin css for this page -->
        <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
        <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
        <!-- End plugin css for this page -->
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Custom JavaScript for search filter -->
    <style> .primary-search {
    color: #007bff; /* Primary color - blue */
    border-color: #007bff; /* Border color */
    /* Add any additional styling here */
}
</style>
        <!-- Layout styles -->
        <link rel="stylesheet" href="./css/style.css">
        <!-- End layout styles -->
    </head>

    <script>
        $(document).ready(function(){
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
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
                    <div class="page-header">
                        <h3 class="page-title"> Manage Students </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Manage Students</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                <div class="d-sm-flex align-items-center mb-4">
                              <div class="col-md-12">
                     <!-- Statistics -->
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
                       <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body ">
                                    <div class="d-sm-flex mb-2">
                                        <div class="table-responsive border rounded p-1">
                                            <table class="table table-striped">
                                           
<?php 
// Define SQL query to count total students
$sql = "SELECT COUNT(DISTINCT stuID) as totalStudents 
        FROM tblstudentreg 
        WHERE class IN (1, 2) 
        AND term = :activeTerm 
        AND academic_year = :academicYear";

// Prepare and execute the query
$query = $dbh->prepare($sql);
$query->bindParam(':activeTerm', $activeTerm, PDO::PARAM_INT);
$query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$query->execute();

// Fetch the result
$result = $query->fetch(PDO::FETCH_ASSOC);

// Extract total count of students
$totalStudents = $result['totalStudents'];

$sql2 = "SELECT COUNT(DISTINCT ID) as totalActive FROM tblstudent WHERE StudentClass IN (1, 2) AND status ='Active'";
$query2 = $dbh->prepare($sql2);
$query2->execute();
$result2 = $query2->fetch(PDO::FETCH_ASSOC);

// Extract total count of students
$totalActive = $result2['totalActive'];
?>

<div>
                                        <div class="row align-items-center">
    <!-- Search Input -->
    <div class="col-md-12">
        <input type="text" id="myInput" class="form-control primary-search" placeholder="Start typing..." 
               style="padding: 10px 20px; border-radius: 40px; width: 100%; display: inline-block;">
    </div>
           
     
    <div class="container">
        <form id="registrationForm">
            <div class="row">
                <div class="col-md-6 d-flex justify-content-end">
                    <button type="button" id="registerAllBtn" style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 40px; display: inline-block; text-align: center;">
                        Register All
                    </button>
                </div>
            </div>
        </form>

        <!-- Progress Tracker -->
        <div id="progressTracker" style="margin-top: 20px;">
            <h4>Progress</h4>
            <div id="progressBar" style="width: 100%; background-color: #f3f3f3; border-radius: 5px;">
                <div id="progressBarFill" style="width: 0%; height: 30px; background-color:rgb(226, 176, 10); border-radius: 5px;"></div>
            </div>
            <p id="progressText"> 
            <?php
                // Calculate the percentage
                $percentage = ($totalStudents / $totalActive) * 100;
              
            ?>
            <?php echo htmlentities($totalStudents); ?>/<?php echo htmlentities($totalActive); ?> registered
            <?php // Output the percentage with two decimal places
            echo htmlentities(number_format($percentage, 0)) . '%';?></p>
            <p id="progressDetails"></p>
        </div>
    </div>                          <div class="col-md-12 d-flex justify-content-center">
    <a href="manage-students.php" 
       style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 40px; text-decoration: none; display: inline-block; text-align: center; width: 100%;">
        Senior Classes Registration
    </a>
</div>

                                    <div class="table-responsive border rounded p-1">
                                        <table class="table table-striped">
                           <h5 class="text-uppercase" style="background-color: yellow; color: #003366; padding: 5px;">
    <?php echo $academicYearMessage; ?>
</h5>


                                         
                                           <thead>
    <tr>
        <th class="font-weight-bold" 
            style="background-color: #003366; color: white; text-transform: uppercase;">
            #
        </th>
        <th class="font-weight-bold" 
            style="background-color: #003366; color: white; text-transform: uppercase;">
            Student Name
        </th>
        <th class="font-weight-bold" 
            style="background-color: #003366; color: white; text-transform: uppercase;">
            Student Class
        </th>
        <th class="font-weight-bold" 
            style="background-color: #003366; color: white; text-transform: uppercase;">
            Admission Date
        </th>
        <th class="font-weight-bold" 
            style="background-color: #003366; color: white; text-transform: uppercase;">
            Reg. Status
        </th> 
        <th class="font-weight-bold" 
            style="background-color: #003366; color: white; text-transform: uppercase;">
            Action
        </th>
    </tr>
</thead>

                                            <tbody id="myTable">
                                            <?php
                                            // Define SQL query to count total students


// Prepare and execute the query
$query = $dbh->prepare($sql);
$query->bindParam(':activeTerm', $activeTerm, PDO::PARAM_INT);
$query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$query->execute();
                                           $sql = "SELECT * FROM tblstudent WHERE status ='Active' AND StudentClass IN (1, 2)";
                                           $query = $dbh->prepare($sql);
                                           $query->execute();
                                           $results = $query->fetchAll(PDO::FETCH_OBJ);
                                           

                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $row) { ?>
                                                    <tr>
                                                        <td><?php echo htmlentities($cnt); ?></td>
                                                        <td><?php echo htmlentities($row->StudentName); ?></td>
                                                        <td><?php echo htmlentities($row->StudentClass); ?>
                                                            <?php echo htmlentities($row->Section); ?></td>
                                                          
                                                        <td><?php echo htmlentities($row->DateofAdmission); ?></td>
                                                        <?php $status = $row->Reg_status;
                                                           if ($status == 'Yes'){?>
                                                        <td><i class="icon-check"></i></td>
                                                        <?php } else{?>
                                                            <td><i class="icon-refresh"></i></td>
                                                       <?php }?>
                                                        <td>
                                                           <div>
    <a href="term-register.php?id=<?php echo htmlentities($row->ID); ?>" 
       style="background-color: yellow; color: #003366; padding: 10px 20px; border-radius: 40px; text-decoration: none; display: inline-block; text-align: center;">
        <i class="icon-plus" style="color: #003366;"></i> Register
    </a>
</div>

                                                        </td>
                                                    </tr>
                                                    <?php $cnt = $cnt + 1;
                                                }
                                            } else {?>
                                            <tr><td class="text-center text-primary" colspan='6'>Sorry! No records found</td></tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <!-- main-panel ends -->
            
        </div>
        <!-- page-body-wrapper ends -->
         <!-- main-panel ends -->
                <?php include_once('includes/footer.php');?>
    </div>
    <!-- container-scroller -->
     
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
  $(document).ready(function() {
    $('#registerAllBtn').click(function() {
        if (confirm('Are you sure you want to register all Form 1s and 2s for this term?')) {
            $('#progressTracker').css('display', 'block'); // Show progress tracker
            registerStudents(); // Call the registration function
        }
    });

    function registerStudents() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'handle-junior-register.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        var buffer = '';

        xhr.onprogress = function(event) {
            // Append new data to the buffer
            buffer += xhr.responseText;

            // Process complete JSON responses
            var responses = buffer.split('\n');
            for (var i = 0; i < responses.length - 1; i++) {
                try {
                    var response = JSON.parse(responses[i]);
                    if (response.success) {
                        $('#progressBarFill').css('width', response.overallProgress + '%');
                        $('#progressText').text(response.overallProgress.toFixed(2) + '%');
                        $('#progressDetails').text(response.message);
                    } else {
                        $('#progressDetails').html(`<div style="color: red;">${response.message}</div>`);
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                }
            }

            // Keep the last incomplete response in the buffer
            buffer = responses[responses.length - 1];
        };

        xhr.onload = function() {
            // Process the final response
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    $('#progressBarFill').css('width', '100%');
                    $('#progressText').text('100%');
                    $('#progressDetails').html(`<div style="color: #003366;">${response.message}</div>`);
                } else {
                    $('#progressDetails').html(`<div style="color: red;">${response.message}</div>`);
                }
            } catch (e) {
                console.error('Error parsing final JSON:', e);
            }
        };

        xhr.onerror = function() {
            $('#progressDetails').html(`<div style="color: red;">An error occurred during the request.</div>`);
        };

        xhr.send();
    }
});
</script>
    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="./vendors/chart.js/Chart.min.js"></script>
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
    </html>

