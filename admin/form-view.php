<?php
session_start();
error_reporting(0); // Add this line to display all errors
include('includes/dbconnection.php');

if (empty($_SESSION['sturecmsaid'])) {
    header('location:logout.php');
    exit();
} else {
    if (isset($_GET['id']) && isset($_GET['name']) && isset($_GET['class']) && isset($_GET['gen'])) {
        $stuID = $_GET['id'];
        $name = strtoupper($_GET['name']);
        $class = strtoupper($_GET['class']);
        $gen = strtoupper($_GET['gen']);
        // Extract only the number from the class string
        preg_match('/\d+/', $class, $matches);
        $shortclass = $matches[0];

        // Check database connection
        if (!$dbh) {
            echo "Failed to connect to the database.";
            exit();
        }

try {
    // Fetch active academic year and determine the next term
    $sql = "SELECT AcademicYear, ActiveTerm FROM tblcalendar WHERE Status = 'Active'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $academicYear = $result['AcademicYear'];
        $term = $result['ActiveTerm'];
        $nextterm = $term + 1;

        if ($nextterm < 4) { // Only if next term is 4
            // Fetch start date and fees for the next term
            $sql8 = "SELECT Term{$nextterm}Start, Term{$nextterm}Fees FROM tblcalendar WHERE Status = 'Active'";
            $query8 = $dbh->prepare($sql8);
            $query8->execute();
            $result8 = $query8->fetch(PDO::FETCH_ASSOC);

            if ($result8) {
                $termStart = $result8["Term{$nextterm}Start"];
                $termFees = $result8["Term{$nextterm}Fees"];
                  }
        } else {
            $sql8 = "SELECT Term{$term}Start, Term{$term}Fees FROM tblcalendar WHERE Status = 'Active'";
            $query8 = $dbh->prepare($sql8);
            $query8->execute();
            $result8 = $query8->fetch(PDO::FETCH_ASSOC);

            if ($result8) {
               $currentStart = $result8["Term{$term}Start"];
        $termStart = date('Y-m-d', strtotime("$currentStart +4 months"));
        $termFees =$result8["Term{$term}Fees"];
                  }
        }
        }
            // Select student's performance de
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
    <link rel="icon" type="images/ico" href="images/sms.ICO">
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
            <?php include_once('includes/header.php');?>
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
        
<div id="pdf-content" style="font-size: 8px;">
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body ">                                   
                                <div class="table-responsive border rounded p-2" style="font-size: 1px;">
                                <div class="table-responsive border rounded p-1" style="font-size: 1px;">
                                <div class="text-center">
    <img src="images/logo.jpg" alt="Logo" style="width: 10% !important; height: 10% !important;">
</div>
                                <h4 class="font-weight-bold text-center">BISHOP AUNEA GIRLS CATHOLIC SECONDARY SCHOOL</h4>
                                <h6 class="font-weight-bold text-center">
                                     PROGRESS REPORT</h6> <br>
                                   <div class="d-sm-flex align-items-center justify-content-between" style="font-size: 1px;">
    <p class="font-weight-bold">NAME OF STUDENT:</p>
    <p><?php echo htmlentities($name); ?></p>
    <p class="font-weight-bold">GENDER:</p>
    <p> <?php echo htmlentities($gen); ?></p>
    <p class="font-weight-bold">CLASS:</p>
    <p> FORM <?php echo htmlentities($class); ?></p>
</div>
<div class="d-sm-flex align-items-center justify-content-between" style="font-size: 1px;">
    <p class="font-weight-bold">TERM:</p>
    <p><?php echo htmlentities($term); ?></p>
    <p class="font-weight-bold">ACADEMIC YEAR:</p>
    <p><?php echo htmlentities($academicYear); ?></p>
    <p><b>TOTAL STUDENTS ENROLLED:</b></p>
                                        <?php 
 // Extract the numeric part from the class
$classNumeric = (int) filter_var($class, FILTER_SANITIZE_NUMBER_INT);
if ($classNumeric >= 3) {
    $orderDirection = 'ASC'; // Order by ascending for class 3 or 4
} else {
    $orderDirection = 'DESC'; // Order by descending for class 1 or 2
}

// Construct the order clause
$order = "CONVERT(points, UNSIGNED) $orderDirection";

// SQL query
$sqlResults = "SELECT * FROM tblresults 
               WHERE academic_year = :academicYear 
               AND term = :term 
               AND class LIKE :class 
               ORDER BY $order, FIELD(result, 'PASSED', 'FAILED'), name ASC";

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
                                        <p><?php echo htmlentities($totalPositions); ?></p> 
                                        <p class="font-weight-bold">POSITION:</p>
                                        <p><?php echo htmlentities($position); ?></p>
                                         
                                    </div>
                                    <div class="d-sm-flex align-items-center mb-2" style="text-align: justify; font-size: 1px;">
                                    <div class="table-responsive border rounded p-1">
                                    <table class="table">
    <thead>
        <tr >
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
    <td>
        <?php 
        if (!empty($finalScore) && is_numeric($finalScore)) {
            echo htmlentities($finalScore) . '%';
        } else {
            echo 'NA';
        }
        ?>
    </td>
<td><?php echo (!empty($row['grade']) ? htmlentities($row['grade']) : 'NA'); ?></td>
<td><?php echo (!empty($row['remark']) ? htmlentities($row['remark']) : 'NA'); ?></td>
    <td class="text-center signature"><?php echo htmlentities($row['sign'] ?? 'not-available'); ?></td>
</tr>
            <?php
            $cnt++;
        }
        ?>
    </tbody>
    <?php
    if ($shortclass == 3 || $shortclass == 4) {
        // Display total points only if class is 3 or 4
$sql3 = "SELECT SUM(grade) AS total_points 
    FROM (
        SELECT grade 
        FROM tblstudentreg 
        WHERE stuID = :stuID 
            AND academic_year = :academicYear 
            AND term = :term 
            AND grade REGEXP '^[0-9]+$' -- Include only numeric values
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
if ($queryEnglishGrade && $queryEnglishGrade->rowCount() > 0) {
    $englishGrade = $queryEnglishGrade->fetch(PDO::FETCH_ASSOC)['grade'];

    // Check if the English grade is 9
    if ($englishGrade == 9) {
        $result = 'FAILED';
    } else {
        // Check if the total points fall within the passing range
        $result = ($totalPoints >= 6 && $totalPoints < 46) ? 'PASSED' : 'FAILED';
    }
} else {
    // English grade is not available or query unsuccessful
    $result = 'FAILED';
}
        // Insert data into tblresults
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
   
    } elseif ($shortclass == 2) {
       $sql3 = "SELECT SUM(final_grade) AS total_points 
FROM (
    SELECT final_grade 
    FROM tblstudentreg 
    WHERE stuID = :stuID 
        AND academic_year = :academicYear 
        AND term = :term 
        AND final_grade REGEXP '^[0-9]+$' -- Filter out non-numeric values
    ORDER BY 
        CASE 
            WHEN subname = 'English' THEN 0 -- Put English first
            ELSE 1 -- Put other subjects after English
        END,
        final_grade DESC LIMIT 6 -- Sort final grades in descending order to get the best grades
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
if ($queryEnglishGrade && $queryEnglishGrade->rowCount() > 0) {
    $englishGrade = $queryEnglishGrade->fetch(PDO::FETCH_ASSOC)['grade'];

    // Check if the English grade is 9
    if ($englishGrade == 9) {
        $result = 'FAILED';
    } else {
        // Check if the total points fall within the passing range
        $result = ($totalPoints >= 240 && $totalPoints < 600) ? 'PASSED' : 'FAILED';
    }
} else {
    // English grade is not available or query unsuccessful
    $englishGrade = 'NA'; // Set English grade to 'NA'
    $result = 'FAILED';
}

  }
    else{
        // Prepare and execute the query to calculate total points using the 6 highest final grades, including English
$sql3 = "SELECT SUM(final_grade) AS total_points 
FROM (
    SELECT final_grade 
    FROM tblstudentreg 
    WHERE stuID = :stuID 
        AND academic_year = :academicYear 
        AND term = :term 
        AND final_grade REGEXP '^[0-9]+$' -- Filter out non-numeric values
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
$result = 'FAILED';
} else { 
    $result = ($totalPoints >= 400 && $totalPoints < 1800) ? 'PASSED' : 'FAILED';
}
   
  }?>
</table>    <table class="table text-badge" style="text-align: justify; font-size: 1px;">
        <thead>
            <p>NOTE: <i><b>A student who fails English automatically fails exams</b></i></p>
            <?php 
if (strpos($class, '3A') === 0 || strpos($class, '3B') === 0 || strpos($class, '4A') === 0 || strpos($class, '4B') === 0) { 
    // Show MSCE grading system
    ?>
            <p class="font-weight-bold text-center">MSCE GRADING SYSTEM</p>
            <tr>
                <th>9 Fail</th>
                <th>8 Pass</th>
                <th>7 Pass</th>
                <th>6 Credit</th>
                <th>5 Credit</th>
                <th>4 Strong Credit</th>
                <th>3 Strong Credit</th>
                <th>2 Distinction</th>
                <th>1 Distinction</th>
            </tr>
        </thead>
        <tbody>
            <tr> 
                <td>0-39</td> 
                <td>40-49</td> 
                <td>50-54</td> 
                <td>55-59</td> 
                <td>60-64</td> 
                <td>65-69</td> 
                <td>70-74</td>
                <td>75-84</td> 
                <td>85-100</td>  
            </tr>
        </tbody>
    </table>
<?php 
} else { 
    // Show JCE grading system
    ?>
    <table class="table text-badge">
        <p class="font-weight-bold text-center">JCE GRADING SYSTEM</p>
        <tr>
            <th>F Fail</th>
            <th>D Average</th>
            <th>C Good</th>
            <th>B Very Good</th>
            <th>A Excellent</th>
        </tr>
    </thead>
    <tbody>
        <tr> 
            <td>0-39</td> 
            <td>40-49</td> 
            <td>50-69</td> 
            <td>70-79</td> 
            <td>80-100</td>  
        </tr>
    </tbody>
    </table> <?php } ?>

    <div class="text-badge"style="display: inline; text-align: justify; font-size: 1px; line-height: 1.0;">
        <p><b>FORM TEACHER'S REMARK:</b> has <b><?php echo htmlentities($result); ?></b> with <b><?php echo htmlentities($totalPoints); ?></b> <?php
            if ($shortclass == 3 || $shortclass == 4) { $comment = 'points';echo htmlentities($comment);} elseif ($shortclass == 2) { $comment = 'marks of 6 best subjects';echo htmlentities($comment);}else {$comment = 'marks';echo htmlentities($comment);}?></p>
    </div>
    <div class="text-badge" style="display: inline; text-align: justify; font-size: 1px; line-height: 1.0;">
        <p><b>HEAD TEACHER'S REMARK: </b> The student  has <?php echo htmlentities($result); ?></p>
    </div>
    <div class="text-badge"style="display: inline; text-align: justify; font-size: 1px; line-height: 1.0;">
        <p><b>NEXT TERM OPENS ON: </b><?php echo htmlentities($termStart); ?>  &nbsp; &nbsp; &nbsp;<b>SCHOOL FEES REQUIRED:</b> MWK<?php echo htmlentities(number_format($termFees)); ?></p>
    </div>
</div>
                                </div>
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
            filename: '<?php echo $name . "_" . $academicYear . "_term" . $term; ?>.pdf', // Filename based on student name, academic year, and term
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 4,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'letter',
                orientation: 'portrait', // Keep orientation as portrait
                putOnlyUsedFonts: true,
                floatPrecision: 16 // Increase precision for better positioning
            },
            // Exclude the button tag and its descendants from the PDF
            ignoreElements: function (element) {
                return element.className === 'btn' || element.closest('.btn') !== null;
            },
            // Adjust font size to fit all content on one page
            // and add page numbers or headers
            onAfterRender: function (pdf) {
                pdf.internal.scaleFactor = 1.0; // Adjust scale factor to fit content
                pdf.setFontSize(1); // Reduce font size

                // Add page numbers or headers
                const totalPages = pdf.internal.pages.length;
                for (let i = 1; i <= totalPages; i++) {
                    pdf.setPage(i);
                    const pageSize = pdf.internal.pageSize;
                    const pageWidth = pageSize.getWidth();
                    const pageHeight = pageSize.getHeight();
                    pdf.text('Page ' + i + ' of ' + totalPages, pageWidth - 1, pageHeight - 0.5, { align: 'right' });
                }
            }
        };
        html2pdf().set(options).from(content).save(); // Generating PDF from the selected content
    }
</script>



    <script src="./js/dashboard.js"></script>
    <!-- End custom js for this page -->
</body>

</html>
