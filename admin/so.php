<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
} else {
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
            // Redirect to dashboard with an error message
            header('location: dashboard.php?error=Invalid active term');
            exit();
        }
    } else {
        // Redirect to dashboard with an error message
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
                <title>Student Management System || Returning Book</title>
                <!-- plugins:css -->
                <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
                <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
                <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
                <!-- endinject -->
                <!-- Plugin css for this page -->
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
                <link rel="stylesheet" href="vendors/select2/select2.min.css">
                <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
                <!-- End plugin css for this page -->
                <!-- inject:css -->
                <!-- endinject -->
                <!-- Layout styles -->
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
    <style>
        /* Optional: Custom styles for the table */
        th {
            background-color: #007bff;
            color: white;
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
                <?php include_once('includes/header.php');?>
                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <!-- partial:partials/_sidebar.html -->
                    <?php include_once('includes/sidebar.php');?>
                    <!-- partial -->
                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="page-header">
                                <h3 class="page-title"> Mid Term <?php echo $activeTerm; ?> results </h3>
                <button type="button" class="btn btn-center" onclick="generatePDF()" style="position: relative;">
                    <img src="images/download-icon.png" alt="Download" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 100%; max-height: 100%;">
                </button>
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Performance List</li>
                        </ol>
                    </nav>
                </div>



                <div class="row">
                    <!-- Search input -->
                    <div class="col-md-4 offset-md-4 text-center">
                        <input type="text" id="myInput" class="form-control primary-search" placeholder="Start typing..">
                    </div>

<!-- Table -->
                            <div id="pdf-content" class="col-md-12 grid-margin stretch-card">
    
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
<div class="container-responsive mt-4">
    <div class="table-responsive border rounded p-1">
        <h5 class="text-primary text-center">BISHOP AUNEA GIRLS CATHOLIC SECONDARY SCHOOL</h5>
        <h6 class="text-primary text-center">FORM 3 MID TERM RESULTS</h6>
        <h6 class="text-primary text-center"><?php echo strtoupper($academicYearMessage); ?></h6>
<div class="table-responsive">
        <table class="table table-striped table-bordered">
        
            <thead>
                <tr>
                    <th class="font-weight-bold">#</th>
                    <th class="font-weight-bold">NAME</th>
                    <th class="font-weight-bold">AGR</th> <!-- Agriculture -->
                    <th class="font-weight-bold">BIO</th> <!-- Biology -->
                    <th class="font-weight-bold">B/K</th> <!-- Bible Knowledge -->
                    <th class="font-weight-bold">CHE</th> <!-- Chemistry -->
                    <th class="font-weight-bold">CHI</th> <!-- Chichewa -->
                    <th class="font-weight-bold">COM</th> <!-- Computer Studies -->
                    <th class="font-weight-bold">ENG</th> <!-- English -->
                    <th class="font-weight-bold">GEO</th> <!-- Geography -->
                    <th class="font-weight-bold">MAT</th> <!-- Mathematics -->
                    <th class="font-weight-bold">PHY</th> <!-- Physics -->
                    <th class="font-weight-bold">SOC</th> <!-- Social/Life Skills -->
                    <th class="font-weight-bold">POINTS</th> <!-- Social/Life Skills -->                    
                    <th class="font-weight-bold">RESULT</th> <!-- Social/Life Skills -->
                </tr>
            </thead>
            <tbody id="myTable" >
  <?php
// SQL query to retrieve distinct student names and their IDs
$sqlResults = "SELECT DISTINCT s.StudentName, sr.stuID
               FROM tblmid AS sr
               JOIN tblstudent AS s ON sr.stuID = s.ID
               WHERE sr.class = 3"; // Only filtering by class

$queryResults = $dbh->prepare($sqlResults);
$queryResults->execute();

$results = $queryResults->fetchAll(PDO::FETCH_ASSOC);

// Initialize an array to hold total scores for each student
$studentTotals = [];

// Output each student's name and calculate total scores
foreach ($results as $record) {
    $fullName = $record['StudentName'];

    // Split the name into parts
    $nameParts = explode(' ', trim($fullName));
    $lastName = array_pop($nameParts);
    $firstNames = implode(' ', $nameParts);
    
    // Rearrange and convert to uppercase
    $studentName = strtoupper($lastName) . ' ' . strtoupper($firstNames);
    $stuID = $record['stuID'];

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

    // Initialize an array to hold grades and test scores
    $grades = [];
    foreach ($subjects as $subject => $pattern) {
        // Prepare the query to fetch grade and test1 score for the current subject
        $sqlSubjectData = "SELECT grade, test1 FROM tblmid 
                           WHERE stuID = :stuID 
                           AND academic_year = :academicYear 
                           AND term = :term 
                           AND class = 3
                           AND subname LIKE :subname";

        $querySubject = $dbh->prepare($sqlSubjectData);
        $querySubject->bindParam(':stuID', $stuID, PDO::PARAM_INT);
        $querySubject->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
        $querySubject->bindParam(':term', $activeTerm, PDO::PARAM_STR);
        $querySubject->bindValue(':subname', $pattern, PDO::PARAM_STR);

        $querySubject->execute();
        $subjectData = $querySubject->fetch(PDO::FETCH_ASSOC);

        $grades[$subject] = [
            'grade' => $subjectData['grade'] ?? '-',
            'test1' => $subjectData['test1'] ?? '-'
        ];
    }

    // Collect grades for calculation
    $gradeScores = [];
    foreach ($grades as $subject => $data) {
        if ($subject !== 'English' && $data['grade'] !== '-') {
            $gradeScores[$subject] = $data['grade'];
        }
    }

    // Sort grades in ascending order to get the lowest 5
    asort($gradeScores);
    $bestGrades = array_slice($gradeScores, 0, 5, true);

    // Add English as the 6th grade if available
    if (isset($grades['English'])) {
        $bestGrades['English'] = $grades['English']['grade'];
    }

    // Calculate the total of the best 5 grades plus English
    $total = array_sum($bestGrades);

    // Determine the result based on total score and English grade
    $englishGrade = $grades['English']['grade'];
    $result = ($total < 6 || $englishGrade === '9') ? 'FAIL' : 'PASS';

    // Store the total and result along with grades
    $studentTotals[$studentName] = [
        'total' => $total,
        'result' => $result,
        'grades' => $grades
    ];
}

// Sort students by total scores in ascending order (lowest scores rank first)
uasort($studentTotals, function($a, $b) {
    return $a['total'] <=> $b['total'];
});

// Output the sorted results
$position = 1;
foreach ($studentTotals as $studentName => $data) {
    echo "<tr>";
    echo "<td>" . htmlentities($position) . "</td>";
    echo "<td>" . htmlentities($studentName) . "</td>";

    foreach ($data['grades'] as $subject => $subjectData) {
        echo "<td>" . htmlentities($subjectData['grade']) . "</td>";
    }

    echo "<td>" . htmlentities($data['total']) . "</td>";
     echo "<td>";
if ($data['result'] === 'PASS') {
    echo '<span style="color: blue;">PASS ✔️</span>'; // Blue tick for PASS
} else {
    echo '<span style="color: red;">FAIL ❌</span>'; // Red cross for FAIL
}
echo "</td>";
    echo "</tr>";

    $position++;
}
?>

        </table>
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
            <!-- inject:js -->
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
            filename: '<?php echo "Mid term result_" . $academicYear . "_term " . $activeTerm; ?>.pdf', // Filename based on student name, academic year, and term
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 2,
                letterRendering: true
            },https://bagcss.great-site.net/admin/view-performance.php?id=100011&name=Lopez+Jennifer&class=4+A&gen=Female
           // For A4
jsPDF: {
    unit: 'mm',
    format: 'a3', // or 'A4'
    orientation: 'landscape' // or 'landscape'
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
         