<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    // Code for deletion
    if (isset($_GET['delid'])) {
        $rid = intval($_GET['delid']);
        $sql = "DELETE FROM tblstudent WHERE ID=:rid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_STR);
        $query->execute();
        echo "<script>alert('Data deleted');</script>";
        echo "<script>window.location.href = 'manage-students.php'</script>";
    }
        // Fetch active academic year and term
    $sql = "SELECT AcademicYear, ActiveTerm, Term1Start, Term1End, Term2Start, Term2End, Term3Start, Term3End FROM tblcalendar WHERE Status = 'Active'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $academicYear = $result['AcademicYear'];
        $activeTerm = $result['ActiveTerm'];
        

        // Check if the active term is valid
        if ($activeTerm >= 1 && $activeTerm <= 3) {
            // Determine term start and end dates
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
            }
            // Construct academic year message
            $academicYearMessage = "$academicYear Academic year, Term $activeTerm from $termStartDate to $termEndDate";
        } else {
            // Redirect to accounts.php with an error
            header('location: accounts.php?error=Invalid active term');
            exit();
        }
    } else {
        // Redirect to accounts.php with an error
   header('location: accounts.php?error=Active academic year not found');
        exit();
    }
}
    
    ?>
    <!DOCTYPE html>
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
                           
                     <!-- Statistics -->
<?php 
// Define SQL query to count total students
$sql = "SELECT COUNT(DISTINCT stuID) as totalStudents 
        FROM tblstudentreg 
        WHERE class IN (3, 4) 
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

$sql2 = "SELECT COUNT(DISTINCT ID) as totalActive FROM tblstudent WHERE StudentClass IN (3, 4) AND status ='Active'";
$query2 = $dbh->prepare($sql2);
$query2->execute();
$result2 = $query2->fetch(PDO::FETCH_ASSOC);

// Extract total count of students
$totalActive = $result2['totalActive'];
?> 
<div class="row align-items-center">
    <!-- Junior Classes Registration Button (Left-aligned) -->
    <div class="row-md-6 d-flex justify-content-start">
        <a href="junior-register.php" 
           style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 40px; text-decoration: none; display: inline-block; text-align: center; width: 100%;">
            Junior Classes Registration
        </a>
    </div> &nbsp;&nbsp;&nbsp;

    <!-- Statistics Button (Right-aligned) -->
    <div class="row-md-6 d-flex justify-content-end">
        <span 
            style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 40px; display: inline-block; text-align: center; width: 100%;">
            Total Registered: <?php echo htmlentities($totalStudents); ?>/<?php echo htmlentities($totalActive); ?> 
            <?php
                // Calculate the percentage
                $percentage = ($totalStudents / $totalActive) * 100;
                // Output the percentage with two decimal places
                echo htmlentities(number_format($percentage, 0)) . '%';
            ?>
        </span>
    </div>
</div>




                          </div>
                          <div>
                          <div class="row align-items-center">
    <!-- Search Input -->
    <div class="col-md-8">
        <input type="text" id="myInput" class="form-control " placeholder="Start typing..." style=" color: #003366; width: 100%; padding: 10px 15px; border-radius: 40px;">
    </div>
    
    <!-- Add New Student Button (Right-aligned) -->
    <div class="col-md-4 d-flex justify-content-end">
        <a href="add-students.php" 
           style="background-color: yellow; color: #003366; padding: 10px 20px; border-radius: 40px; text-decoration: none; display: inline-block; text-align: center;">
            Add New Student
        </a>
    </div>
</div>

                                       

                          </div>
                                    <div class="table-responsive border rounded p-1">
                                        <table class="table table-striped">
                                        <h5 class="text-uppercase" style="background-color: yellow; color: #003366; padding: 5px;">
    <?php echo $academicYearMessage; ?>
</h5>

                                         
    <thead>
    <tr>
        <th class="font-weight-bold" style="background-color: #003366; color: white; text-transform: uppercase; ">#</th>
        <th class="font-weight-bold" style="background-color: #003366; color: white; text-transform: uppercase;">Student Name</th>
        <th class="font-weight-bold" style="background-color: #003366; color: white; text-transform: uppercase;">Student Class</th>
        <th class="font-weight-bold" style="background-color: #003366; color: white; text-transform: uppercase; ">Admission Date</th>
        <th class="font-weight-bold" style="background-color: #003366; color: white; text-transform: uppercase;">Reg. Status</th>
        <th class="font-weight-bold" style="background-color: #003366; color: white; text-transform: uppercase; ">Action</th>
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
                                           $sql = "SELECT * FROM tblstudent WHERE status ='Active' AND StudentClass IN (3, 4)";
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
        <i class="icon-plus"></i> Register
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
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <?php include_once('includes/footer.php'); ?>
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

