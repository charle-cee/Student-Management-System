<?php
session_start();
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
include('includes/dbconnection.php');

// Check if the user session is set; if not, redirect to logout
if (empty($_SESSION['sturecmsaid'])) {
    header('location:logout.php');
    exit();
}

// Check if required GET parameters are provided
if (isset($_GET['id'], $_GET['name'], $_GET['class'], $_GET['gen'])) {
    $stuID = $_GET['id'];
    $name = strtoupper($_GET['name']);
    $class = strtoupper($_GET['class']);
    $gen = strtoupper($_GET['gen']);
 

    // SQL query to fetch the active academic year and term information
    $sql = "SELECT AcademicYear, ActiveTerm, Term1Start, Term1End, Term2Start, Term2End, Term3Start, Term3End
            FROM tblcalendar
            WHERE Status = 'Active'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $academicYear = $result['AcademicYear'];
        $activeTerm = $result['ActiveTerm'];

        // Verify that the active term is within the valid range
        if ($activeTerm >= 1 && $activeTerm <= 3) {
            // Determine the start and end dates for the active term
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

            // Generate the academic year message
            $academicYearMessage = "$academicYear Academic year, Term $activeTerm";
        } else {
            // Redirect to dashboard with an invalid term error message
            header('location: dashboard.php?error=Invalid active term');
            exit();
        }
    } else {
        // Redirect to dashboard with an error if the academic year data is not found
        header('location: dashboard.php?error=Active academic year not found');
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
    <title>Student Management System</title>
                <!-- plugins:css -->
                <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
                <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
                <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
                <!-- endinject -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
                <!-- Plugin css for this page -->
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
                <link rel="stylesheet" href="vendors/select2/select2.min.css">
                <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
                <!-- End plugin css for this page -->
                <!-- inject:css -->
                <!-- endinject -->
                <!-- Layout styles -->
   
<style>
    /* Mobile responsive styling */
    @media (max-width: 768px) {
        .info-field {
            display: block;
            margin-bottom: 15px;
        }

        .label,
        .value,
        .additional-info {
            font-size: 14px; /* Reduce font size for mobile */
            margin-bottom: 8px;
        }

        .student-info {
            padding: 10px; /* Slightly smaller padding on mobile */
            margin-bottom: 10px; /* Reduce bottom margin on mobile */
        }
    }

    /* Tablet and above */
    @media (min-width: 769px) {
        .info-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .label {
            font-size: 16px;
            font-weight: bold;
            color: #003366;
        }

        .value {
            font-size: 14px;
            color: #333;
        }

        .additional-info {
            font-size: 14px;
            color: #003366;
        }

        .student-info {
            padding: 15px;
            margin-bottom: 15px;
        }
    }
</style>

                <link rel="stylesheet" href="css/style.css" />
                <style>
                    .return-btn {
                        background-color: #007bff;
                        color: #fff;
                        border: none;
                        padding: 5px 10px;
                        border-radius: 5px;
                        cursor: pointer;
                    }
                </style>
            </head>
            <body>
            <div class="container-scroller">
                <!-- partial:partials/_navbar.html -->
            <?php 
    include_once('includes/header.php');
?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <?php  
    include_once('includes/sidebar.php');
 ?>
            <!-- partial -->
                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="page-header">
                                <h3 class="page-title"> Mid Term <?php echo $activeTerm; ?> results </h3>
<!-- Updated button -->
<button type="button" class="btn" onclick="generatePDF()" 
        style="display: block; margin: 0 auto; background-color: #003366; color: white; border-radius: 40px; padding: 10px 20px;">
    <i class="fas fa-download" style="font-size: 20px;"></i>
</button>

                <nav aria-label="breadcrumb">
                       
                    </nav>
                </div>



                <div class="row">

<!-- Table -->
                            <div id="pdf-content" class="col-md-12 grid-margin stretch-card">
    
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
<div class="container-responsive mt-4">
    <div class="table-responsive border rounded p-1" >
    <div class="table-responsive" style="border: 10px solid #003366; border-radius: 10px; padding: 10px;">
                              <div class="text-center">
    <img src="images/logo.jpg" alt="Logo" style="width: 15% !important; height: 15% !important;">
</div>
   <h5 class="text-blue text-center">BISHOP AUNEAU GIRLS CATHOLIC SECONDARY SCHOOL</h5>
<h6 class="text-blue text-center">FORM <?php echo strtoupper($class); ?> MID TERM RESULTS <?php echo strtoupper($academicYearMessage); ?></h6> 
<div class="table-responsive">
     
<?php


// Retrieve the student ID from the URL
$stuID = isset($_GET['id']) ? $_GET['id'] : null;

// If student ID is provided, proceed with fetching results
if ($stuID) {
    // Fetch the student's name and details based on the provided ID
    $sqlStudentDetails = "SELECT s.StudentName, s.StudentClass, s.Gender
                          FROM tblstudent AS s
                          WHERE s.ID = :stuID";
    
    $stmtStudent = $dbh->prepare($sqlStudentDetails);
    $stmtStudent->bindParam(':stuID', $stuID, PDO::PARAM_INT);
    $stmtStudent->execute();
    $studentDetails = $stmtStudent->fetch(PDO::FETCH_ASSOC);

    if ($studentDetails) {
        $fullName = strtoupper($studentDetails['StudentName']);
        $class = strtoupper($studentDetails['StudentClass']);
        $gen = strtoupper($studentDetails['Gender']);
        
        // Define an array of subjects with their corresponding LIKE patterns
        $subjects = [
            'Agriculture' => '%Agricu%',
            'Biology' => '%Biolo%',
            'Bible Knowledge' => '%Bible%',
            'Chemistry' => '%Chemis%',
            'Chichewa' => '%Chichewa%',
            'Computer Studies' => '%Computer%',
            'English' => '%English%',
            'Geography' => '%Geograph%',
            'Mathematics' => '%Mathematics%',
            'Physics' => '%Physics%',
            'Social/Life Skills' => '%Social%'
        ];

        $grades = [];
        $totalScores = []; // Initialize the totalScores array

        foreach ($subjects as $subject => $pattern) {
            $sqlSubjectData = "SELECT grade, test1 FROM tblmid 
                               WHERE stuID = :stuID 
                               AND academic_year = :academicYear 
                               AND term = :term 
                               AND class = :class 
                               AND subname LIKE :subname";
            
            $querySubject = $dbh->prepare($sqlSubjectData);
            $querySubject->bindParam(':stuID', $stuID, PDO::PARAM_INT);
            $querySubject->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
            $querySubject->bindParam(':term', $activeTerm, PDO::PARAM_STR);
            $querySubject->bindParam(':class', $class, PDO::PARAM_STR);
            $querySubject->bindValue(':subname', $pattern, PDO::PARAM_STR);
            $querySubject->execute();
            
            $subjectData = $querySubject->fetch(PDO::FETCH_ASSOC);
            // Check if $subjectData is false (indicating a query failure)
            if ($subjectData) {
                $grades[$subject] = [
                    'grade' => $subjectData['grade'] ?? '-',
                    'test1' => $subjectData['test1'] ?? '-'
                ];

                // Check if class is 3 or 4 for total score calculation
                if ($class == 3 || $class == 4) {
                    if ($subject !== 'English') {
                        $totalScores[$subject] = $subjectData['grade']; // Use grade for classes 3 and 4
                    }
                } else {
                    if ($subject !== 'English') {
                        $totalScores[$subject] = $subjectData['test1']; // Use test1 for classes 1 or 2
                    }
                }
            } else {
                // If the query failed, set the grade and test1 to a default value
                $grades[$subject] = [
                    'grade' => '-',
                    'test1' => '-'
                ];
            }
        }

        // Sort scores based on class
        if ($class == 3 || $class == 4) {
            asort($totalScores); // Ascending for class 3 or 4 (to get the lowest scores)
        } else {
            arsort($totalScores); // Descending for class 1 or 2
        }

        // Select the best 5 scores
        $bestScores = array_slice($totalScores, 0, 5); // Get the lowest 6 scores

        // Include English in the calculation
        if (isset($grades['English'])) {
            if ($class == 3 || $class == 4) {
                // Use grade for English
                $bestScores['English'] = $grades['English']['grade'];
            } else {
                // Use test1 for English
                $bestScores['English'] = $grades['English']['test1'];
            }
        }
        // Calculate total based on selected best scores
        $total = array_sum($bestScores);
        $englishGrade = $grades['English']['grade'] ?? '-';
        // Determine the result based on the total score and specific conditions for class 3 or 4
        if ($class == 3 || $class == 4) {
            $result = ($total < 6 || $total > 42) ? 'FAIL' : 'PASS'; // Different conditions for class 3 or 4
        } else {
            $result = ($total < 240 || $englishGrade === 'F') ? 'FAIL' : 'PASS'; // Original conditions for class 1 or 2
        }
?>
 <div class="student-info" style="background-color: #f9f9f9; padding: 15px; border-radius: 8px; border: 2px solid #003366; margin-bottom: 15px;">
    <div class="info-field" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <!-- Student Name Field -->
        <span class="label" style="font-weight: bold; color: #003366; font-size: 16px; flex: 1 0 100%; margin-bottom: 5px;">STUDENT NAME:</span>
        <span class="value" style="color: #333; font-size: 14px; font-weight: normal; flex: 1 0 100%; margin-bottom: 10px;"><?= htmlentities($fullName) ?></span>
        
        <!-- Additional Info (Form) -->
        <span class="additional-info" style="color: #003366; font-size: 14px; flex: 1 0 100%; margin-bottom: 5px;">
            <b>FORM: </b><span class="value" style="color: #333;"><?= htmlentities($class) ?></span>
        </span>
    </div>
</div>

<?php
        // Display the student's results in a vertical format
        echo "<table id='myTable' class='table table-striped table-bordered'>";
       echo "<thead><tr>
    <th class='font-weight-bold text-uppercase' style='background-color: #003366; color: white; padding: 10px;'>Subject</th>
    <th class='font-weight-bold text-uppercase' style='background-color: #003366; color: white; padding: 10px;'>Score</th>
    <th class='font-weight-bold text-uppercase' style='background-color: #003366; color: white; padding: 10px;'>Grade</th>
  </tr></thead>";
        echo "<tbody>";

        foreach ($grades as $subject => $data) {
            echo "<tr><td>" . htmlentities($subject) . "</td><td>" . htmlentities($data['test1']) . "</td><td>" . htmlentities($data['grade']) . "</td></tr>";
        }

        // Conditional check for total points or total marks
        if ($class == 3 || $class == 4) {
            echo "<tr><td><b>Total Points (6 Best Subjects, English Inclusive)</b></td><td><b>" . htmlentities($total) . "</b></td></tr>";
        } else {
            echo "<tr><td><b>Total Marks (6 Best Subjects, English Inclusive)</b></td><td><b>" . htmlentities($total) . "</b></td></tr>";
        }

        // Result display
        echo "<tr><td>Result</td><td>";
        if ($result === 'PASS') {
            echo '<button style="background-color: #003366; color: white; border: none; padding: 5px 10px; border-radius: 5px;">PASS ✔️</button>';
        } else {
            echo '<button style="background-color: red; color: white; border: none; padding: 5px 10px; border-radius: 5px;">FAIL ❌</button>';
        }
        // Add a note about failing English
        echo "<tr><td colspan='2' style='color: red; font-weight: bold;'> Note that: A student who fails English automatically fails the exam.</td></tr>";

        echo "</tbody></table>";
    } else {
        echo "No student found with the provided ID.";
    }
} else {
    echo "No student ID provided.";
}
?>

    </div></div>
</div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- content-wrapper ends -->
                    <!-- partial:partials/_footer.html -->

                    <!-- partial -->
                    
                <!-- main-panel ends -->
                <?php include_once('includes/footer.php');?>
                    </div>
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
            <!-- inject:js -->
            <script src="js/off-canvas.js"></script>
            <script src="js/misc.js"></script>
            <!-- endinject -->
            
<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script>

            <!-- Custom js for this page -->
            <script src="js/typeahead.js"></script>
            <script src="js/select2.js"></script>
            <!-- End custom js for this page -->

                <!-- inject:js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompdf/0.8.4/dompdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
    function generatePDF() {
        const content = document.getElementById('pdf-content'); // Selecting the unique div
        const options = {
            margin: 0,
            filename: '<?php echo "Mid term results for " . $name . "_" . $academicYear . "_term " . $activeTerm; ?>.pdf', // Filename based on student name, academic year, and term
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 3,
                letterRendering: true
            },
            jsPDF: {
    unit: 'mm',
    format: 'a4', // or 'A4'
    orientation: 'portrait' // or 'landscape'
},
   // Exclude the button tag and its descendants from the PDF
            ignoreElements: function (element) {
                return element.className === 'btn' || element.closest('.btn') !== null;
            }
        };
        html2pdf().set(options).from(content).save(); // Generating PDF from the selected content
    }
</script>

            </body>
            </html>
         