<?php
session_start();
error_reporting(E_ALL); // Add this line to display all errors
ini_set('display_errors', 0); // Add this line to display all errors
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
}
    if (isset($_GET['id'])) {
        $stuID = $_GET['id'];
$user = (isset($_GET['user']) && $_GET['user'] === 'known') ? 'known' : 'unknown';
        // Fetch student details based on provided ID
    $sql = "SELECT * FROM tblstudent WHERE ID = :stuID";
    $query = $dbh->prepare($sql);
    $query->bindParam(':stuID', $stuID, PDO::PARAM_INT);
    $query->execute();
    $studentData = $query->fetch(PDO::FETCH_ASSOC);

    // If student data found, proceed with registration
    if ($studentData) {
       
       
         $name =  strtoupper($studentData['StudentName']);
        $stuclass = $studentData['StudentClass'];
        $section = $studentData['Section'];
      $class =  strtoupper($stuclass . ' ' . $section);  
       // Extract only the number from the class string
        preg_match('/\d+/', $class, $matches);
        $shortclass = $matches[0];      
       $gen =  strtoupper($studentData['Gender']);
        $academicYear ='';
        $term ='';

    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
$academicYear = $_POST['academicYear'];
$term = $_POST['term'];
$stuclass = $_POST['class'];
try {
    // Fetch active calendar data
    $sql = "SELECT * FROM tblcalendar WHERE Status = 'Active'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $activeTerm = $result['ActiveTerm'];
        $examview = $result['ExaViewStatus'];
        $activeYear = $result['AcademicYear'];
        
        // Validate view permissions
        if ($term == $activeTerm && $academicYear == $activeYear && $examview == 'not viewable') {
            $errorMessage = urlencode("Current year: $academicYear and term: $term results are not viewable. Consider viewing previous terms.");
            $redirectUrl = $_SERVER['PHP_SELF'] . "?user=known&id=" . urlencode($stuID) . "&error=$errorMessage";
            header("Location: $redirectUrl");
            exit();
        }
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $errorMessage = urlencode("System error occurred. Please try again later.");
    header("Location: " . $_SERVER['PHP_SELF'] . "?user=known&id=" . urlencode($stuID) . "&error=$errorMessage");
    exit();
}

        try {

            // Select student's performance details
            $sql = "SELECT * FROM tblstudentreg WHERE stuID = :stuID AND academic_year = :academicYear AND term = :term AND class = :stuclass";
            $query = $dbh->prepare($sql);
            $query->bindParam(':stuID', $stuID, PDO::PARAM_STR);
            $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
            $query->bindParam(':term', $term, PDO::PARAM_STR);
            $query->bindParam(':stuclass', $stuclass, PDO::PARAM_STR);
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    </style> </head>
<body>
    <div class="container-scroller">
           <!-- partial:partials/_navbar.html -->
            <?php if (!($user === 'unknown')) {
    include_once('includes/header.php');
}?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <?php if (!($user === 'unknown')) {
    include_once('includes/sidebar.php');
} ?>
         <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                    </div>
    
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">               <!-- Updated button -->
<button type="button" class="btn" onclick="generatePDF()" 
        style="display: block; margin: 0 auto; background-color: #003366; color: white; border-radius: 40px; padding: 10px 20px;">
    <i class="fas fa-download" style="font-size: 20px;"></i>
</button>

                             <div class="card-body">
    <form method="post">
        <!-- Academic Year Dropdown -->
        <div class="form-group" style="margin-bottom: 15px;">
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
            <label for="academicYearSelect" style="color: #003366; font-weight: bold;">Academic Year:</label>
            <select class="form-control" id="academicYearSelect" name="academicYear" style="border: 2px solid #003366; font-size: 14px;">
                <?php
                // Fetch distinct academic years from tblstudentreg
                $sql = "SELECT DISTINCT academic_year FROM tblstudentreg ORDER BY academic_year DESC";
                $query = $dbh->prepare($sql);
                $query->execute();
                $academicYears = $query->fetchAll(PDO::FETCH_COLUMN);
                foreach ($academicYears as $year) {
                    echo "<option value=\"$year\" " . ($year == $academicYear ? 'selected' : '') . ">$year</option>";
                }
                ?>
            </select>
        </div>

        <!-- Term Dropdown -->
        <div class="form-group" style="margin-bottom: 15px;">
            <label for="termSelect" style="color: #003366; font-weight: bold;">Term:</label>
            <select class="form-control" id="termSelect" name="term" style="border: 2px solid #003366; font-size: 14px;">
                <?php
                // Fetch distinct terms for the current student
                $sql = "SELECT DISTINCT term FROM tblstudentreg WHERE stuID = :stuID ORDER BY term ASC";
                $query = $dbh->prepare($sql);
                $query->bindParam(':stuID', $stuID, PDO::PARAM_STR);
                $query->execute();
                $terms = $query->fetchAll(PDO::FETCH_COLUMN);
                foreach ($terms as $termVal) {
                    echo "<option value=\"$termVal\" " . ($termVal == $term ? 'selected' : '') . ">$termVal</option>";
                }
                ?>
            </select>
        </div>

        <!-- Class Dropdown -->
        <div class="form-group" style="margin-bottom: 15px;">
            <label for="classSelect" style="color: #003366; font-weight: bold;">Class:</label>
            <select class="form-control" id="classSelect" name="class" style="border: 2px solid #003366; font-size: 14px;">
                <?php
                // Fetch distinct class for the current student
                $sql2 = "SELECT DISTINCT class FROM tblstudentreg WHERE stuID = :stuID ORDER BY class DESC";
                $query2 = $dbh->prepare($sql2);
                $query2->bindParam(':stuID', $stuID, PDO::PARAM_STR);
                $query2->execute();
                $classes = $query2->fetchAll(PDO::FETCH_COLUMN);
                foreach ($classes as $classVal) {
                    echo "<option value=\"$classVal\" " . ($classVal == $class ? 'selected' : '') . ">$classVal</option>";
                }
                ?>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary" style="background-color: #003366; border-color: #003366;">View Results</button>
    </form>
</div>

                            </div>
                        </div>
                    </div>
                    <?php if (!empty($academicYear && $term)) {?>
<div id="pdf-content" style="font-size: 8px !important;">
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body ">                                
                                <div class="table-responsive" style="border: 10px solid #003366; padding: 5px;">
    <div class="table-responsive" style="border: 3px solid #f2f2f2; padding: 5px;">

<div class="d-flex align-items-center" style="margin-bottom: 20px; border-bottom: 2px solid #003366; padding-bottom: 20px;">
    <!-- Logo Section -->
    <div style="flex-shrink: 0;">
        <img src="images/logo.jpg" alt="Logo" style="width: 130px; height: auto; margin-right: 30px;">
    </div>

    <!-- School Information Section -->
    <div style="flex-grow: 1;">
        <!-- School Name & Title -->
        <h4 class="font-weight-bold text-center" style="color: #003366; margin-bottom: 10px;">
            NYUNGWE GIRLS CATHOLIC SECONDARY SCHOOL
        </h4>

        <!-- Address and Contact Details Container -->
        <div class="d-flex justify-content-between align-items-start" style="padding: 10px; background-color: #f2f2f2; border-radius: 8px;">
          <!-- Address Section -->
            <!-- Address Section -->
            <div style="flex: 1; padding-right: 20px;">
                <h5 style="color: #003366; margin-bottom: 5px;">Postal Address:</h5>
                <p style="font-size: 12px; margin: 0;">Nyungwe Girls Catholic Sec. School</p>
                <p style="font-size: 12px; margin: 0;">Private Bag 5</p>
                <p style="font-size: 12px; margin: 0;">Namadzi, Chiradzulu</p>
            </div>
            
            <!-- Contact Section -->
            <div style="flex: 1; padding-left: 20px;">
                <h5 style="color: #003366; margin-bottom: 5px;">Contact Details</h5>
                <p style="font-size: 12px; margin: 0;">Principal: +265 (0) 988 301 840</p>
                <p style="font-size: 12px; margin: 0;">Deputy Principal: +265 (0) 888 382 503</p>
                <p style="font-size: 12px; margin: 0;">Bursar: +265 (0) 988 476 486</p>
            </div>
        </div>
    </div>
</div>

                                <h4 class="font-weight-bold text-center">STUDENT PROGRESS REPORT</h4
                                <div></div>

                                 
    <div class="d-flex align-items-center justify-content-between" style="font-size: 8px !important;">
    <p class="font-weight-bold">STUDENT NAME:</p>
    <p><?php echo htmlentities($name); ?></p>
    <p class="font-weight-bold">CLASS:</p>
    <p> FORM <?php echo htmlentities($class); ?></p>
    <p class="font-weight-bold">TERM:</p>
    <p><?php echo htmlentities($term); ?></p>
    <p class="font-weight-bold">YEAR:</p>
    <p><?php echo htmlentities($academicYear); ?></p>


    
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
?>                                      <p class="font-weight-bold">POSITION:</p>
                                        <p><?php echo htmlentities($position); ?></p>
                                        <p class="font-weight-bold">/</p>
                                        <p><?php echo htmlentities($totalPositions); ?></p> 
                                         
                                    </div>
                                    <div class="d-sm-flex align-items-center mb-2" style="text-align: justify; font-size: 1px;">
                                    <div class="table-responsive border rounded p-1">
                                    <table class="table table-striped">
    <thead>
        <tr>
    <th class="font-weight-bold" style="background-color: #003366; color: white; padding: 10px;">
        SUBJECT
    </th>
    <th class="font-weight-bold" style="background-color: #003366; color: white; padding: 10px;">
        FINAL(100%)
    </th>
    <th class="font-weight-bold" style="background-color: #003366; color: white; padding: 10px;">
        POSITION
    </th>
    <th class="font-weight-bold" style="background-color: #003366; color: white; padding: 10px; ">
        GRADE
    </th>
    <th class="font-weight-bold" style="background-color: #003366; color: white; padding: 10px;">
        REMARK
    </th>
    <th class="font-weight-bold" style="background-color: #003366; color: white; padding: 10px;">
        TEACHER'S SIGN
    </th>
</tr>

    </thead>
    <tbody>
        <?php
        // Display student details
$cnt = 1;   
$subcount = 0; // Initialize the counter
        foreach ($student as $row) {
             // Calculate the final score
            $finalScore = $row['final_grade'];
             if (!empty($finalScore) && is_numeric($finalScore) && $finalScore >= 40) {
        $subcount++; // Increment the counter if finalScore is >= 40
    }
     $result = ($subcount >= 6) ? 'PASSED' : 'FAILED';
            $subId = $row['subID'];
            $class = $row['class'];
             // Select student's performance details for same subject and class
$sql = "SELECT * FROM tblstudentreg 
        WHERE academic_year = :academicYear 
        AND term = :term 
        AND subID = :subID 
        AND class = :class 
        ORDER BY final_grade DESC";

$query = $dbh->prepare($sql);
$query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$query->bindParam(':term', $term, PDO::PARAM_STR);
$query->bindParam(':subID', $subId, PDO::PARAM_STR);
$query->bindParam(':class', $class, PDO::PARAM_STR);
$query->execute();
$allStudents = $query->fetchAll();
         // Get the rank/position
$rank = 0;
foreach ($allStudents as $stu) {
    if ($stu['stuID'] == $row['stuID']) {
        break;
    }
    if ($stu['class'] == $row['class'] && $stu['subID'] == $row['subID']) {
        $rank++;
    }
}
$rank++; // Increment rank to include current student

        ?>
            <tr>
    
    <td style="text-transform: uppercase;"><b><?php echo htmlentities($row['subname']); ?></b></td>
  <td> <b>
    <?php 
    if (!empty($finalScore)) {
        if (is_numeric($finalScore)) {
            echo htmlentities($finalScore);
        } else {
            echo strtoupper($finalScore); // Convert to uppercase (AB)
        }
    } else {
        echo 'NA';
    }
    ?>
</b></td>
     <td>
                <?php 
                echo $rank . '/' . count($allStudents); // Display rank/position
                ?>
            </td>
<td><?php echo (!empty($row['grade']) ? htmlentities($row['grade']) : 'NA'); ?></td>
<td><?php echo (!empty($row['remark']) ? htmlentities($row['remark']) : 'NA'); ?></td>
    <td class="text-center signature"><?php echo htmlentities($row['sign'] ?? '#@*'); ?></td>
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
        $result = ($totalPoints >= 6 && $totalPoints < 42) ? 'PASSED' : 'FAILED';
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
    $sqlInsert = "UPDATE tblresults SET name = :name, class = :class, gender = :gender, points = :points, position = :position, result = :result WHERE stuID = :stuID AND academic_year = :academicYear AND term = :term";
} else {
    // Record doesn't exist, insert a new one
    $sqlInsert = "INSERT INTO tblresults (stuID, name, class, gender, academic_year, term, points, position, result) 
                  VALUES (:stuID, :name, :class, :gender, :academicYear, :term, :points,:position, :result)";
}

$queryInsert = $dbh->prepare($sqlInsert);
$queryInsert->bindParam(':stuID', $stuID, PDO::PARAM_INT);
$queryInsert->bindParam(':name', $name, PDO::PARAM_STR);
$queryInsert->bindParam(':class', $class, PDO::PARAM_STR);
$queryInsert->bindParam(':gender', $gen, PDO::PARAM_STR);
$queryInsert->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$queryInsert->bindParam(':term', $term, PDO::PARAM_STR);
$queryInsert->bindParam(':points', $totalPoints, PDO::PARAM_INT);
$queryInsert->bindParam(':position', $position, PDO::PARAM_INT);
$queryInsert->bindParam(':result', $result, PDO::PARAM_STR);

$queryInsert->execute();   
    } elseif ($shortclass == 1 || $shortclass == 2) {
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
    if ($englishGrade == "F") {
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
    $sqlInsert = "UPDATE tblresults SET name = :name, class = :class, gender = :gender, points = :points,  position = :position, result = :result WHERE stuID = :stuID AND academic_year = :academicYear AND term = :term";
} else {
    // Record doesn't exist, insert a new one
    $sqlInsert = "INSERT INTO tblresults (stuID, name, class, gender, academic_year, term, points, position, result) 
                  VALUES (:stuID, :name, :class, :gender, :academicYear, :term, :points, :position, :result)";
}

$queryInsert = $dbh->prepare($sqlInsert);
$queryInsert->bindParam(':stuID', $stuID, PDO::PARAM_INT);
$queryInsert->bindParam(':name', $name, PDO::PARAM_STR);
$queryInsert->bindParam(':class', $class, PDO::PARAM_STR);
$queryInsert->bindParam(':gender', $gen, PDO::PARAM_STR);
$queryInsert->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$queryInsert->bindParam(':term', $term, PDO::PARAM_STR);
$queryInsert->bindParam(':points', $totalPoints, PDO::PARAM_INT);
$queryInsert->bindParam(':position', $position, PDO::PARAM_INT);
$queryInsert->bindParam(':result', $result, PDO::PARAM_STR);
$queryInsert->execute();
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
$englishGrade = $queryEnglishGrade->fetch(PDO::FETCH_ASSOC)['remark'];

// Initialize $result variable
$result = '';

// Check if the English grade is 9
if ($englishGrade == "F") {
$result = 'FAILED';
} else { 
    $result = ($totalPoints >= 240 && $totalPoints < 600) ? 'PASSED' : 'FAILED';
}
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
    $sqlInsert = "UPDATE tblresults SET name = :name, class = :class, gender = :gender, points = :points,  position = :position, result = :result WHERE stuID = :stuID AND academic_year = :academicYear AND term = :term";
} else {
    // Record doesn't exist, insert a new one
    $sqlInsert = "INSERT INTO tblresults (stuID, name, class, gender, academic_year, term, points, position, result) 
                  VALUES (:stuID, :name, :class, :gender, :academicYear, :term, :points, :position, :result)";
}

$queryInsert = $dbh->prepare($sqlInsert);
$queryInsert->bindParam(':stuID', $stuID, PDO::PARAM_INT);
$queryInsert->bindParam(':name', $name, PDO::PARAM_STR);
$queryInsert->bindParam(':class', $class, PDO::PARAM_STR);
$queryInsert->bindParam(':gender', $gen, PDO::PARAM_STR);
$queryInsert->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$queryInsert->bindParam(':term', $term, PDO::PARAM_STR);
$queryInsert->bindParam(':points', $totalPoints, PDO::PARAM_INT);
$queryInsert->bindParam(':position', $position, PDO::PARAM_INT);
$queryInsert->bindParam(':result', $result, PDO::PARAM_STR);
$queryInsert->execute();
  }?>
</table>  

    <table class="table text-badge" style="text-align: justify; font-size: 1px;">
        <thead>
            <p>NOTE: <i><b>A student who fails English automatically fails exams</b></i></p>
            <?php 
if (strpos($class, '3') === 0 || strpos($class, '4') === 0) {
    // Show MSCE grading system
    ?>
            <p class="text-center font-weight-bold" style="color: #003366; font-size: 18px; margin: 20px 0; border-bottom: 2px solid #003366; padding-bottom: 5px;">
    MSCE GRADING SYSTEM
</p>
            <tr class="font-weight-bold">
                <th>9 Fail</th>
                <th>8 Bare Pass</th>
                <th>7 Pass</th>
                <th>6 Weak Credit</th>
                <th>5 Weak Credit</th>
                <th>4 Strong Credit</th>
                <th>3 Strong Credit</th>
                <th>2 Distinction</th>
                <th>1 Distinction</th>
            </tr>
        </thead>
        <tbody>

            <tr> 
                <td>0-39</td> 
                <td>40-45</td> 
                <td>46-49</td> 
                <td>50-59</td> 
                <td>60-64</td> 
                <td>65-69</td> 
                <td>70-74</td>
                <td>75-79</td> 
                <td>80-100</td>  
            </tr>
        </tbody>
    </table>
<?php 
} else { 
    // Show JCE grading system
    ?>
    <table class="table text-badge">
        <p class="text-center font-weight-bold" style="color: #003366; font-size: 18px; margin: 2px 0; border-bottom: 2px solid #003366; padding-bottom: 5px;">
    JCE GRADING SYSTEM
</p>

        <tr class="font-weight-bold">
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
            <td>40-59</td> 
            <td>60-74</td> 
            <td>75-84</td> 
            <td>85-100</td>  
        </tr>
    </tbody>
    </table>
<?php 
} 
?>

    <div class="text-badge"style="display: inline; text-align: justify; font-size: 1px; line-height: 1.0;">
        <p><b>FORM TEACHER'S REMARK:</b> <i>She has <b><?php echo htmlentities($result); ?></b> with <b><?php echo htmlentities($totalPoints); ?></b> <?php
           if ($shortclass == 3 || $shortclass == 4) {
    // For class 3 and 4, set the comment to 'points'
    $comment = 'points of 6 best subjects';
    echo htmlentities($comment);
} elseif ($shortclass == 2) {
    // For class 2, set the comment to 'marks of 6 best subjects'
    $comment = 'marks of 6 best subjects';
    echo htmlentities($comment);
} else {
    // For any other class, set the comment to 'marks'
    $comment = 'marks of 6 best subjects';
    echo htmlentities($comment);
}
?></i></p>
    </div>
</div>
                                </div>
                                </div>
                                </div>
                                </div>
                            
                        </div>
                    </div>
<?php } ?>
                </div>
                </div>
            </div>
               
            </div>
            <!-- main-panel ends -->
             <!-- partial:partials/_footer.html -->
                <?php include_once('includes/footer.php'); ?>
                <!-- partial -->
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
            scale: 3, // Increase scale for better resolution
            letterRendering: true,
            useCORS: true // Enable cross-origin requests for external images
        },
        jsPDF: {
            unit: 'mm',
            format: 'a4', // Change format to A4 if you need A4 instead of letter size
            orientation: 'portrait', // Keep orientation as portrait
            putOnlyUsedFonts: true,
            floatPrecision: 16, // Increase precision for better positioning
            autoTable: {
                styles: {
                    overflow: 'linebreak', // Ensure long text in tables breaks correctly
                },
                margin: { top: 0, left: 0, right: 0, bottom: 0 }, // Margin for auto tables
            }
        },
        // Exclude the button tag and its descendants from the PDF
        ignoreElements: function (element) {
            return element.className === 'btn' || element.closest('.btn') !== null;
        },
        // Add page numbers or headers
        onAfterRender: function (pdf) {
            pdf.internal.scaleFactor = 1.0; // Adjust scale factor to fit content
            pdf.setFontSize(1); // Set small font size

            // Add page numbers
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

    // Generate PDF
    html2pdf().set(options).from(content).save(); // Generating PDF from the selected content
}

</script>



    <script src="./js/dashboard.js"></script>
    <!-- End custom js for this page -->
</body>

</html>
