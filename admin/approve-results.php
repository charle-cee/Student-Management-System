<?php
session_start();
error_reporting(E_ALL); // Add this line to display all errors
ini_set('display_errors', 1); // Add this line to display all errors
include('includes/dbconnection.php');

if (empty($_SESSION['sturecmsaid'])) {
    header('location:logout.php');
    exit();
} else {
    if (isset($_GET['id']) && isset($_GET['name']) && isset($_GET['class']) && isset($_GET['gen'])) {
        $stuID = $_GET['id'];
        $name = $_GET['name'];
        $class = $_GET['class'];
        $gen = $_GET['gen'];

        // Extract only the number from the class string
        preg_match('/\d+/', $class, $matches);
        $shortclass = $matches[0];

        // Check database connection
        if (!$dbh) {
            echo "Failed to connect to the database.";
            exit();
        }

        try {
            // Fetch active academic year
            $sql = "SELECT AcademicYear, ActiveTerm FROM tblcalendar WHERE Status = 'Active'";
            $query = $dbh->prepare($sql);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $academicYear = $result['AcademicYear'];
                $term = $result['ActiveTerm'];
            }

            // Select student's performance details
            $sql = "SELECT * FROM tblstudentreg WHERE stuID = :stuID AND academic_year = :academicYear AND term = :term";
            $query = $dbh->prepare($sql);
            $query->bindParam(':stuID', $stuID, PDO::PARAM_STR);
            $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
            $query->bindParam(':term', $term, PDO::PARAM_STR);
            $query->execute();

            // Fetch records
            $student = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <title>Student Management System || Manage class</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="./css/style.css">
    <!-- Import custom signature font -->
    <link href="https://fonts.googleapis.com/css2?family=Handlee&display=swap" rel="stylesheet">
    <style>
        .signature {
            font-family: 'Handlee', cursive; /* Custom signature font */
            font-size: 24px;
            color: #333; /* Signature color */
        }
    </style>
    <!-- End layout styles -->
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
                    <div class="page-header">
                    </div>
    
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">                <button type="button" class="btn btn-center" onclick="generatePDF()" style="position: relative;">
    <img src="images/download-icon.png" alt="Download" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 100%; max-height: 100%;">
</button>
                                <div class="card-body">
                                <a href="previous-results.php?id=<?php echo $stuID; ?>" class="btn btn-center btn-primary" style="display: block; margin: 0 auto;">
   View Previous Results
</a>
     
                                  </div>
                            </div>
                        </div>
                    </div>
                    <div id="pdf-content">
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body ">                                   
                                <div class="table-responsive border rounded p-2">
                                <div class="table-responsive border rounded p-1">
                                <div class="text-center">
    <img src="images/logo.jpg" alt="Logo" style="max-width: 100%; max-height: 100%;">
</div>
                                <h2 class="font-weight-bold text-center"></h2>
                                
                                <h4 class="font-weight-bold text-center">
                                     PROGRESS REPORT</h4> <br>
                                    <div class="d-sm-flex align-items-center">
                                        <p class="font-weight-bold">NAME OF STUDENT:</p>&nbsp;
                                        <p><?php echo htmlentities($name); ?></p>&nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp;
                                        <p class="font-weight-bold">GENDER:</p>&nbsp; &nbsp; 
                                        <p> <?php echo htmlentities($gen); ?></p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                                        <p class="font-weight-bold">CLASS:</p>&nbsp; &nbsp; 
                                        <p> FORM <?php echo htmlentities($class); ?></p></div>&nbsp; &nbsp; 
                                        <div class="d-sm-flex align-items-center mb-2">
                                        <p class="font-weight-bold">TERM:</p>&nbsp; &nbsp; 
                                        <p><?php echo htmlentities($term); ?></p>&nbsp; &nbsp; &nbsp; &nbsp; 
                                        <p class="font-weight-bold">ACADEMIC YEAR:</p>&nbsp;
                                        <p><?php echo htmlentities($academicYear); ?></p>&nbsp; &nbsp; &nbsp; &nbsp; 
                                        <p><b>TOTAL STUDENTS ENROLLED:</b></p>&nbsp; &nbsp; 
                                        <?php 
 // Extract the numeric part from the class
$classNumeric = (int)filter_var($class, FILTER_SANITIZE_NUMBER_INT);

// Select all records from tblresults where academic year and term match and class is like '3%'
// If the class is 3 or 4, order by points ascending to get the lowest points first
// If the class is 1 or 2, order by points descending to get the highest points first
if ($classNumeric >= 3) {
    $order = 'points ASC'; // Order by descending for class 3 or 4
} else {
    $order = 'points DESC'; // Order by descending for class 1 or 2
}

$sqlResults = "SELECT * FROM tblresults 
               WHERE academic_year = :academicYear 
               AND term = :term 
               AND class LIKE :class
               ORDER BY $order";
$queryResults = $dbh->prepare($sqlResults);
$queryResults->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$queryResults->bindParam(':term', $term, PDO::PARAM_STR);
$queryResults->bindParam(':class', $classPrefix, PDO::PARAM_STR);
$classPrefix = $classNumeric . '%';
$queryResults->execute();
$results = $queryResults->fetchAll(PDO::FETCH_ASSOC);

// Find the position
$position = 0;
foreach ($results as $key => $result) {
    $position++;
    if ($result['stuID'] == $stuID) {
        break;
    }
}

// Get the total number of positions
$totalPositions = count($results);

// Display the position


?>
                                        <p><?php echo htmlentities($totalPositions); ?></p>&nbsp; &nbsp; &nbsp; &nbsp; 
                                        <p class="font-weight-bold">POSITION:</p>&nbsp; &nbsp; 
                                        <p><?php echo htmlentities($position); ?></p>
                                         
                                    </div>
                                    <div class="table-responsive border rounded p-1">
                                    <table class="table">
    <thead>
        <tr>
            <th class="font-weight-bold">#</th>
            <th class="font-weight-bold">SUBJECT</th>
            <th class="font-weight-bold">MARKS</th>
            <th class="font-weight-bold">GRAGE</th>
            <th class="font-weight-bold">REMARK</th>
            <th class="font-weight-bold">TEACHER'S SIGN</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Display student details
        $cnt = 1;
        foreach ($student as $row) {
            // Calculate the final score
            $finalScore = $row['final_grade'];
            ?>
            <tr>
                <td><?php echo $cnt; ?></td>
                <td><?php echo htmlentities($row['subname']); ?></td>
                <td><?php echo htmlentities($finalScore); ?> <?php if ($finalScore !== 'AB' || $finalScore !== 'NA'){
                    echo '%';}?></td>
                <td><?php echo htmlentities($row['grade']); ?></td>
                <td><?php echo htmlentities($row['remark']); ?></td>
                <td class="text-center signature"><?php echo htmlentities($row['sign']); ?></td>
                
            </tr>
            <?php
            $cnt++;
        }
        ?>
    </tbody>
    <?php
    if ($shortclass == 3 || $shortclass == 4) {
        // Display total points only if class is 3 or 4

        // Prepare and execute the query to calculate total points using the 6 lowest grades
        $sql3 = "SELECT SUM(grade) AS total_points 
            FROM (
                SELECT grade 
                FROM tblstudentreg 
                WHERE stuID = :stuID 
                    AND academic_year = :academicYear 
                    AND term = :term 
                ORDER BY 
                    CASE 
                        WHEN subname = 'English' THEN 0 -- Put English first
                        ELSE 1 -- Put other subjects after English
                    END,
                    grade ASC
                LIMIT 6
            ) AS lowest_grades";
        $query3 = $dbh->prepare($sql3);
        $query3->bindParam(':stuID', $stuID, PDO::PARAM_STR); // Corrected binding
        $query3->bindParam(':academicYear', $academicYear, PDO::PARAM_STR); // Corrected binding
        $query3->bindParam(':term', $term, PDO::PARAM_STR); // Corrected binding
        $query3->execute();

        // Fetch the result
        $totalPointsResult = $query3->fetch(PDO::FETCH_ASSOC);
        $totalPoints = $totalPointsResult['total_points'];

// Prepare and execute the query to check the grade of English
$sqlEnglishGrade = "SELECT grade FROM tblstudentreg WHERE academic_year = :academicYear 
    AND term = :term AND stuID = :stuID AND subname = 'English'";
$queryEnglishGrade = $dbh->prepare($sqlEnglishGrade);
$queryEnglishGrade->bindParam(':stuID', $stuID, PDO::PARAM_STR); // Corrected binding
$queryEnglishGrade->bindParam(':academicYear', $academicYear, PDO::PARAM_STR); // Corrected binding
$queryEnglishGrade->bindParam(':term', $term, PDO::PARAM_STR); // Corrected binding
$queryEnglishGrade->execute();

// Fetch the English grade
$englishGrade = $queryEnglishGrade->fetch(PDO::FETCH_ASSOC)['grade'];
        // Initialize $result variable
        $result = '';

        // Check if the English grade is 9
        if ($englishGrade == 9) {
            $result = 'Failed!';
        } else {
            // Check if the total points fall within the passing range
            $result = ($totalPoints >= 6 && $totalPoints < 46) ? 'Passed!' : 'Failed!';
        }
        ?>   
            
                <td>Total Points: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php  // Insert data into tblresults
    $sqlInsert = "INSERT INTO tblresults (stuID, name, class, gender, academic_year, term, points, result) 
    VALUES (:stuID, :name, :class, :gender, :academicYear, :term, :points, :result)
    ON DUPLICATE KEY UPDATE 
    name = VALUES(name), 
    class = VALUES(class), 
    gender = VALUES(gender), 
    academic_year = VALUES(academic_year), 
    term = VALUES(term), 
    points = VALUES(points), 
    result = VALUES(result)";

// Check if the record already exists for the given stuID, academic year, and term
$sqlCheck = "SELECT COUNT(*) AS count FROM tblresults WHERE stuID = :stuID AND academic_year = :academicYear AND term = :term";
$queryCheck = $dbh->prepare($sqlCheck);
$queryCheck->bindParam(':stuID', $stuID, PDO::PARAM_INT);
$queryCheck->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$queryCheck->bindParam(':term', $term, PDO::PARAM_STR);
$queryCheck->execute();
$resultCheck = $queryCheck->fetch(PDO::FETCH_ASSOC);

if ($resultCheck['count'] > 0) {
    // Record already exists, update it
    $sqlInsert = "UPDATE tblresults SET name = :name, class = :class, gender = :gender, points = :points, result = :result WHERE stuID = :stuID AND academic_year = :academicYear AND term = :term";
} else {
    // Record doesn't exist, insert a new one
    $sqlInsert = "INSERT INTO tblresults (stuID, name, class, gender, academic_year, term, points, result) 
                  VALUES (:stuID, :name, :class, :gender, :academicYear, :term, :points, :result)";
}

$queryInsert = $dbh->prepare($sqlInsert);
$queryInsert->bindParam(':stuID', $stuID, PDO::PARAM_INT);
$queryInsert->bindParam(':name', $name, PDO::PARAM_STR);
$queryInsert->bindParam(':class', $class, PDO::PARAM_STR);
$queryInsert->bindParam(':gender', $gen, PDO::PARAM_STR);
$queryInsert->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$queryInsert->bindParam(':term', $term, PDO::PARAM_STR);
$queryInsert->bindParam(':points', $totalPoints, PDO::PARAM_INT);
$queryInsert->bindParam(':result', $result, PDO::PARAM_STR);

$queryInsert->execute();

    echo htmlentities($totalPoints); ?></b> &nbsp;&nbsp;&nbsp;&nbsp;Result: <b><?php echo htmlentities($result); ?></b></td>
        
    <?php } else{
        // Prepare and execute the query to calculate total points using the 6 highest final grades, including English
$sql3 = "SELECT SUM(final_grade) AS total_points 
FROM (
    SELECT final_grade 
    FROM tblstudentreg 
    WHERE stuID = :stuID 
        AND academic_year = :academicYear 
        AND term = :term 
    ORDER BY 
        CASE 
            WHEN subname = 'English' THEN 0 -- Put English first
            ELSE 1 -- Put other subjects after English
        END,
        final_grade DESC -- Sort final grades in descending order to get the best grades
) AS highest_grades";
$query3 = $dbh->prepare($sql3);
$query3->bindParam(':stuID', $stuID, PDO::PARAM_STR); // Corrected binding
$query3->bindParam(':academicYear', $academicYear, PDO::PARAM_STR); // Corrected binding
$query3->bindParam(':term', $term, PDO::PARAM_STR); // Corrected binding
$query3->execute();

// Fetch the result
$totalPointsResult = $query3->fetch(PDO::FETCH_ASSOC);
$totalPoints = $totalPointsResult['total_points'];

// Prepare and execute the query to check the grade of English
$sqlEnglishGrade = "SELECT remark FROM tblstudentreg WHERE academic_year = :academicYear 
AND term = :term AND stuID = :stuID AND subname = 'English'";
$queryEnglishGrade = $dbh->prepare($sqlEnglishGrade);
$queryEnglishGrade->bindParam(':stuID', $stuID, PDO::PARAM_STR); // Corrected binding
$queryEnglishGrade->bindParam(':academicYear', $academicYear, PDO::PARAM_STR); // Corrected binding
$queryEnglishGrade->bindParam(':term', $term, PDO::PARAM_STR); // Corrected binding
$queryEnglishGrade->execute();

// Fetch the English grade
$englishGrade = $queryEnglishGrade->fetch(PDO::FETCH_ASSOC)['remark'];

// Initialize $result variable
$result = '';

// Check if the English grade is 9
if ($englishGrade == "Fail") {
$result = 'Failed!';
} else {
$result = 'Passed!';
}
?>

    <td colspan="7">Total marks: &nbsp;
   <b> <?php 
   // Check if the record already exists for the given stuID, academic year, and term
$sqlCheck = "SELECT COUNT(*) AS count FROM tblresults WHERE stuID = :stuID AND academic_year = :academicYear AND term = :term";
$queryCheck = $dbh->prepare($sqlCheck);
$queryCheck->bindParam(':stuID', $stuID, PDO::PARAM_INT);
$queryCheck->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$queryCheck->bindParam(':term', $term, PDO::PARAM_STR);
$queryCheck->execute();
$resultCheck = $queryCheck->fetch(PDO::FETCH_ASSOC);

if ($resultCheck['count'] > 0) {
    // Record already exists, update it
    $sqlInsert = "UPDATE tblresults SET name = :name, class = :class, gender = :gender, points = :points, result = :result WHERE stuID = :stuID AND academic_year = :academicYear AND term = :term";
} else {
    // Record doesn't exist, insert a new one
    $sqlInsert = "INSERT INTO tblresults (stuID, name, class, gender, academic_year, term, points, result) 
                  VALUES (:stuID, :name, :class, :gender, :academicYear, :term, :points, :result)";
}

$queryInsert = $dbh->prepare($sqlInsert);
$queryInsert->bindParam(':stuID', $stuID, PDO::PARAM_INT);
$queryInsert->bindParam(':name', $name, PDO::PARAM_STR);
$queryInsert->bindParam(':class', $class, PDO::PARAM_STR);
$queryInsert->bindParam(':gender', $gen, PDO::PARAM_STR);
$queryInsert->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$queryInsert->bindParam(':term', $term, PDO::PARAM_STR);
$queryInsert->bindParam(':points', $totalPoints, PDO::PARAM_INT);
$queryInsert->bindParam(':result', $result, PDO::PARAM_STR);

$queryInsert->execute();

   echo htmlentities($totalPoints); ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result: <b><?php echo htmlentities($result); ?></b></td>



  <?php  }?>
</table>
</div>           <div class="text-justify" style="padding: 10px; font-weight: bold;">
    Grading System <br>
    <span> Form 1 and 2: 0-39: F (Fail), 40-49: D (Pass), 50-60: C (Average), 61-69: B (Good), 70-79: B (Very Good), 80 and above: A (Excellent)</span>
    <br><br>
    Form 3 and 4: 0-39: Fail (9), 40-49: Pass (8), 50-54: High Pass (7), 55-60: Credit (6), 61-65: Credit (5), 66-69: High Credit (4), 70-74: Distinction (3), 75-79: Distinction (2), 80 and above: High Distinction (1)
    <br><br>
    <b>Note:</b> <i> A student who fails the English subject automatically fails the exams.</i>
</div>
                                </div>
                                



</div>
                            
                        </div>
                    </div>

                </div>
                </div>
            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompdf/0.8.4/dompdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
    function generatePDF() {
        const content = document.getElementById('pdf-content'); // Selecting the unique div
        const options = {
            margin: 0,
            filename: '<?php echo $name . "_" . $academicYear . "_" . $term; ?>.pdf', // Filename based on student name, academic year, and term
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 3,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'letter',
                orientation: 'portrait'
            },
            // Exclude the button tag and its descendants from the PDF
            ignoreElements: function (element) {
                return element.className === 'btn' || element.closest('.btn') !== null;
            }
        };
        html2pdf().set(options).from(content).save(); // Generating PDF from the selected content
    }
</script>

<script>
        // Auto-hide alerts after 6 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 150); // Remove after fade out
            });
        }, 6000);
    </script>
    <script src="./js/dashboard.js"></script>
    <!-- End custom js for this page -->
</body>

</html>