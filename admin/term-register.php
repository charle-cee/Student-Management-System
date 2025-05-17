<?php
session_start();
error_reporting(0);
include 'includes/dbconnection.php';

// Redirect user to logout if not logged in
if (empty($_SESSION['sturecmsaid'])) {
    header('location:logout.php');
    exit();
}

// Check if student ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Establish database connection
    include 'includes/dbconnection.php';

    // Fetch student details based on provided ID
    $sql = "SELECT * FROM tblstudent WHERE ID = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $studentData = $query->fetch(PDO::FETCH_ASSOC);

    // If student data found, proceed with registration
    if ($studentData) {
        $stuname = $studentData['StudentName'];
        $stuclass = $studentData['StudentClass'];
        $section = $studentData['Section'];
        $gender = $studentData['Gender'];

        // Fetch active academic year data from database
        $sql6 = "SELECT * FROM tblcalendar WHERE Status = 'Active'";
        $query = $dbh->prepare($sql6);
        $query->execute();
        $academicYearData = $query->fetch(PDO::FETCH_ASSOC);

        // If active academic year found, process registration
        if ($academicYearData) {
            $term = $academicYearData['ActiveTerm'];
            $year = $academicYearData['AcademicYear'];
            if (isset($_POST['submit'])) {
                // Get selected subject from the form post data
                $subjectID = $_POST['subject'];
                $class = $_POST['class'];
                $reg_date = date('Y-m-d H:i:s');
                if (empty($subjectID)) {
                    $errorMessage = urlencode("No subject selected to be registered for $stuname, for {$academicYearData['AcademicYear']} term {$academicYearData['ActiveTerm']} for $name.");
                    header("location: term-register.php?id=" . urlencode($id) . "&error=" . $errorMessage);
                    exit();
                }
                
                // Fetch subject name from tblsubject
                $sql = "SELECT Name AS subname FROM tblsubject WHERE ID = :subjectID"; // Alias the Name column as subname
                $query = $dbh->prepare($sql);
                $query->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);
                $query->execute();
                $subjectData = $query->fetch(PDO::FETCH_ASSOC);
                $subname = $subjectData['subname']; // Use the alias subname for the column name

                // Check if the selected subject is already registered for the student
                $sql = "SELECT * FROM tblstudentreg WHERE stuID = :id AND academic_year = :academicYear AND term = :activeTerm AND subID = :subjectID";
                $query = $dbh->prepare($sql);
                $query->bindParam(':id', $id, PDO::PARAM_INT);
                $query->bindParam(':academicYear', $academicYearData['AcademicYear'], PDO::PARAM_STR);
                $query->bindParam(':activeTerm', $academicYearData['ActiveTerm'], PDO::PARAM_INT);
                $query->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);
                $query->execute();
                $registrationData = $query->fetch(PDO::FETCH_ASSOC);

                // If subject is already registered, redirect with error
                if ($registrationData) {
                    $errorMessage = urlencode("$stuname is already registered for {$academicYearData['AcademicYear']} term {$academicYearData['ActiveTerm']} for $subname.");
                    header("location: term-register.php?id=" . urlencode($id) . "&error=" . $errorMessage);
                    exit();
                } else {// Check if mandatory subjects are already inserted for the student
                    $sqlCheckMandatory = "SELECT subID FROM tblstudentreg WHERE stuID = :id AND academic_year = :academicYear AND term = :activeTerm";
                    $queryCheckMandatory = $dbh->prepare($sqlCheckMandatory);
                    $queryCheckMandatory->bindParam(':id', $id, PDO::PARAM_INT);
                    $queryCheckMandatory->bindParam(':academicYear', $academicYearData['AcademicYear'], PDO::PARAM_STR);
                    $queryCheckMandatory->bindParam(':activeTerm', $academicYearData['ActiveTerm'], PDO::PARAM_INT);
                    $queryCheckMandatory->execute();
                    $mandatorySubjects = $queryCheckMandatory->fetchAll(PDO::FETCH_COLUMN);
                    
                    // Fetch mandatory subjects from tblsubject
                    $sqlMandatory = "SELECT ID FROM tblsubject WHERE subtype = 'Mandatory'";
                    $queryMandatory = $dbh->prepare($sqlMandatory);
                    $queryMandatory->execute();
                    $mandatorySubjectIDs = $queryMandatory->fetchAll(PDO::FETCH_COLUMN);
                    
                    // Insert mandatory subjects if not already inserted
                    $insertedMandatorySubjects = array_diff($mandatorySubjectIDs, $mandatorySubjects);
                    foreach ($insertedMandatorySubjects as $mandatorySubjectID) {
                        // Fetch subject name for mandatory subjects
                        $sql = "SELECT Name AS subname FROM tblsubject WHERE ID = :mandatorySubjectID"; // Alias the Name column as subname
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':mandatorySubjectID', $mandatorySubjectID, PDO::PARAM_INT);
                        $query->execute();
                        $subjectData = $query->fetch(PDO::FETCH_ASSOC);
                        $subname = $subjectData['subname']; // Use the alias subname for the column name
                        
                        // Insert registration data into database for mandatory subjects
                        $sqlInsertMandatory = "INSERT INTO tblstudentreg (academic_year, term, stuID, class, subID, final_grade, test1, test2, reg_date, status, subname) 
                                               VALUES (:academicYear, :activeTerm, :id, :class, :mandatorySubjectID, 'NA', 'NA', 'NA', :reg_date, 'In Progress', :subname)";
                        $queryInsertMandatory = $dbh->prepare($sqlInsertMandatory);
                        $queryInsertMandatory->bindParam(':academicYear', $academicYearData['AcademicYear'], PDO::PARAM_STR);
                        $queryInsertMandatory->bindParam(':activeTerm', $academicYearData['ActiveTerm'], PDO::PARAM_INT);
                        $queryInsertMandatory->bindParam(':id', $id, PDO::PARAM_INT);
                        $queryInsertMandatory->bindParam(':class', $class, PDO::PARAM_STR);
                        $queryInsertMandatory->bindParam(':mandatorySubjectID', $mandatorySubjectID, PDO::PARAM_INT);
                        $queryInsertMandatory->bindParam(':reg_date', $reg_date, PDO::PARAM_STR);
                        $queryInsertMandatory->bindParam(':subname', $subname, PDO::PARAM_STR);
                        $insertResult = $queryInsertMandatory->execute();
                    }
                    
                    // Insert registration data into database for selected subject from the post
                    // Fetch subject name for selected subject
                    $sql = "SELECT Name AS subname FROM tblsubject WHERE ID = :subjectID"; // Alias the Name column as subname
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);
                    $query->execute();
                    $subjectData = $query->fetch(PDO::FETCH_ASSOC);
                    $subname = $subjectData['subname']; // Use the alias subname for the column name

                    $sqlInsertSelected = "INSERT INTO tblstudentreg (academic_year, term, stuID, class, subID, final_grade, test1, test2, reg_date, subname, status) 
                    VALUES (:academicYear, :activeTerm, :id, :class, :subjectID, 'NA', 'NA', 'NA', :reg_date, :subname, 'In Progress')";
                    $queryInsertSelected = $dbh->prepare($sqlInsertSelected);
                    $queryInsertSelected->bindParam(':academicYear', $academicYearData['AcademicYear'], PDO::PARAM_STR);
                    $queryInsertSelected->bindParam(':activeTerm', $academicYearData['ActiveTerm'], PDO::PARAM_INT);
                    $queryInsertSelected->bindParam(':id', $id, PDO::PARAM_INT);
                    $queryInsertSelected->bindParam(':class', $class, PDO::PARAM_STR);
                    $queryInsertSelected->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);
                    $queryInsertSelected->bindParam(':reg_date', $reg_date, PDO::PARAM_STR);
                    $queryInsertSelected->bindParam(':subname', $subname, PDO::PARAM_STR);
                    $insertResult = $queryInsertSelected->execute();
                    // Redirect with success or error message based on insertion result
                    if ($insertResult) {
                        $sql8 = "UPDATE tblstudent Set Reg_status ='Yes' WHERE ID = :id"; // Alias the Name column as subname
                        $query8 = $dbh->prepare($sql8);
                        $query8->bindParam(':id', $id, PDO::PARAM_INT);
                        $query8->execute();
                        $successMessage = urlencode("$stuname has been registered for $subname, {$academicYearData['AcademicYear']} term {$academicYearData['ActiveTerm']}.");
                        header("location: term-register.php?id=" . urlencode($id) . "&success=" . $successMessage);
                        exit();
                    } else {
                        $errorMessage = urlencode("Something went wrong. Please try again.");
                        header("location: term-register.php?id=" . urlencode($id) . "&error=" . $errorMessage);
                        exit();
                    }
                    
                }
            }
        }
    } else {
        $errorMessage = urlencode("No student records found!");
        header("location: term-register.php?id=" . urlencode($id) . "&error=" . $errorMessage);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <title>Student Management System || Register Students</title>
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="container-scroller">
    <?php include_once('includes/header.php');?>
    <div class="container-fluid page-body-wrapper">
        <?php include_once('includes/sidebar.php');?>
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="page-header">
                    <h3 class="page-title">Register Students</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Register Students</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-lg-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                            <h4 class="card-title" style="text-align: center; background: #003366; color: white; padding: 10px; border-radius: 5px;">
    Register Students
</h4>

                                <?php if (isset($_GET['error'])) { ?>
                                    <div class="alert alert-danger alert-dismissible text-center">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <b><?php echo $_GET['error']; ?></b>
                                    </div>
                                <?php } ?>
                                <?php if (isset($_GET['success'])) { ?>
                                    <div class="alert alert-success alert-dismissible text-center">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <b><?php echo $_GET['success']; ?></b>
                                    </div>
                                <?php }?>
                                <form class="forms-sample" method="post" enctype="multipart/form-data">
                                    <!-- Student Details -->
                                    <div class="form-group">
                                        <label for="exampleInputName1">Student Details</label>
                                        <input type="hidden" name="class" value="<?php echo htmlentities($stuclass); ?>" class="form-control" readonly>
                                        <input type="text" name="stuname" value="<?php echo $stuname; ?> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlentities($stuclass); ?>&nbsp;&nbsp;&nbsp;<?php echo htmlentities($section); ?>" class="form-control" readonly>
                                    </div>
                                    <!-- Select subjects -->
                                    <div class="form-group">
                                        <label for="subject">Select subjects</label><br>
                                        <select class="form-control" id="subject" name="subject">
                                            <option selected disabled>Select Subject</option>
                                            <?php
                                            // Assuming $dbh is your database connection object

                                            // Fetch subjects from tblsubject
                                            $sql = "SELECT ID, Name FROM tblsubject WHERE subtype = 'Elective'";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $subjects = $query->fetchAll(PDO::FETCH_ASSOC);

                                            // Display options for each subject
                                            foreach ($subjects as $subject) {
                                                echo '<option value="' . $subject['ID'] . '">' . $subject['Name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                   <!-- Register Button -->
<button type="submit" class="btn mr-2" name="submit" style="background: #003366; border-radius:40px; color: white;">
    Register
</button>

<!-- Student List Button -->
<a href="manage-students.php" class="btn" style="background: yellow; border-radius:40px; color: #003366;">
    Student List
</a>

                                </form>
                                <br> 
                                <form method="post" action="remove-subject.php" style="display: inline;" onsubmit="return confirm('Do you want to de-register this student?');">
    <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
    <input type="hidden" name="term" value="<?php echo htmlspecialchars($term); ?>">
    <input type="hidden" name="stuID" value="<?php echo htmlspecialchars($id); ?>">
    
    <button type="submit" 
            style="background-color: #dc3545;
                   color: white;
                   border: none;
                   border-radius: 4px;
                   padding: 6px 12px;
                   cursor: pointer;
                   font-size: 14px;
                   display: inline-flex;
                   align-items: center;
                   gap: 6px;">
        <span style="font-size: 16px;">×</span>
        De-register <?php echo htmlspecialchars($stuname); ?>
    </button>
</form>
                            </div>
                           
                        </div>
                        
                    </div>
                    
                    <div class="col-lg-6 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-center" style="background: #003366; color: white; padding: 10px; border-radius: 5px;">
                Registered Subjects Statistics
            </h4>
            
            <?php
            // Fetch academic year and term
            $year = $academicYearData['AcademicYear'];
            $term = $academicYearData['ActiveTerm'];

            // Count total subjects registered
            $sql = "SELECT COUNT(*) AS total FROM tblstudentreg WHERE stuID = :id AND academic_year = :year AND term = :term";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->bindParam(':year', $year, PDO::PARAM_STR);
            $query->bindParam(':term', $term, PDO::PARAM_STR);
            $query->execute();
            $totalSubjects = $query->fetch(PDO::FETCH_ASSOC);

            if ($totalSubjects && $totalSubjects['total'] > 0) {
                echo "<p>Total subjects registered: <b>" . $totalSubjects['total'] . "</b></p>";

                // Select and display names of registered subjects
                $sql2 = "SELECT * FROM tblstudentreg WHERE stuID = :id AND academic_year = :year AND term = :term";
                $query2 = $dbh->prepare($sql2);
                $query2->bindParam(':id', $id, PDO::PARAM_INT);
                $query2->bindParam(':year', $year, PDO::PARAM_STR);
                $query2->bindParam(':term', $term, PDO::PARAM_STR);
                $query2->execute();
                $registeredSubjects = $query2->fetchAll(PDO::FETCH_ASSOC);

                echo "<ul class=\"list-group\">";
                foreach ($registeredSubjects as $subject) {
                    echo "<li class=\"list-group-item d-flex justify-content-between align-items-center\" style=\"background: #003366; color: white;\">";
                    echo "<span>" . $subject['subname'] . "</span>";
                    echo "<form method=\"post\" action=\"remove-subject.php\" style=\"display: inline;\" onsubmit=\"return confirm('Are you sure you want to remove " . $subject['subname'] . "?');\">";
                    echo "<input type=\"hidden\" name=\"year\" value=\"$year\">";
                    echo "<input type=\"hidden\" name=\"term\" value=\"$term\">";
                    echo "<input type=\"hidden\" name=\"stuID\" value=\"$id\">";
                    echo "<input type=\"hidden\" name=\"subID\" value=\"" . $subject['subID'] . "\">";
                    echo "<button type=\"submit\" class=\"btn btn-sm text-white\" style=\"background: transparent; padding: 0; border: none; font-size: 20px; line-height: 1;\" aria-label=\"Close\">";
                    echo "<span aria-hidden=\"true\">&times;</span>";
                    echo "</button>";
                    echo "</form>";
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p class=\"text-center\" style=\"color: #003366;\">No subjects registered yet.</p>";
            }
            ?>
        </div>
    </div>
</div>
                </div>
            </div>
            <?php include_once('includes/footer.php');?>
        </div>
    </div>
</div>
<script src="vendors/js/vendor.bundle.base.js"></script>
<script src="vendors/select2/select2.min.js"></script>
<script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
<script src="js/off-canvas.js"></script>
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
<script src="js/misc.js"></script>
<script src="js/typeahead.js"></script>
<script src="js/select2.js"></script>
</body>
</html>
