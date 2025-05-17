<?php
session_start();
error_reporting(E_ALL); // Add this line to display all errors
ini_set('display_errors', 0); // Add this line to display all errors
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
} else {
    if (isset($_GET['year']) && isset($_GET['term'])&& isset($_GET['exam']) && isset($_GET['sub']) && isset($_GET['form'])) {
        $academicYear = $_GET['year'];
        $activeTerm = $_GET['term'];
        $sub = $_GET['sub'];
        $form = $_GET['form'];
        $exam = $_GET['exam'];

        // Check database connection
        if (!$dbh) {
            echo "Failed to connect to database.";
            exit();
        }

        try {
$sql = "
    SELECT 
        s.ID, 
        s.StudentName, 
        s.StudentClass, 
        s.Section, 
        s.Gender, 
        sr.test1, 
        sr.test2, 
        sr.final_grade 
    FROM 
        tblstudentreg sr 
    JOIN 
        tblstudent s ON sr.stuID = s.ID
    WHERE 
        sr.academic_year = :academicYear 
        AND sr.term = :activeTerm 
        AND sr.subID = :sub 
        AND s.StudentClass = :form 
    ORDER BY 
        s.StudentName ASC;
";


$query = $dbh->prepare($sql);
$query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$query->bindParam(':activeTerm', $activeTerm, PDO::PARAM_INT);
$query->bindParam(':sub', $sub, PDO::PARAM_STR);
$query->bindParam(':form', $form, PDO::PARAM_STR);
$query->execute();

// Fetch records
$students = $query->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="icon" type="image/ico" href="sms.ICO">
    <title>Student Management System || Manage class</title>
    <!-- CSS -->
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Font Awesome CDN for Edit Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                        <?php 
                        $sqlother = "SELECT Name FROM tblsubject WHERE ID = :sub";
                        $queryMinor = $dbh->prepare($sqlother);
                        $queryMinor->bindParam(':sub', $sub, PDO::PARAM_INT);
                        $queryMinor->execute();
                        $minorResult = $queryMinor->fetch(PDO::FETCH_ASSOC);
                        $name = $minorResult['Name'];
                        ?>
                        <h4 class="card-title mb-sm-0"><?php echo htmlentities($name); ?>: <?php echo htmlentities($academicYear); ?> Term <?php echo htmlentities($activeTerm); ?> Form  <?php echo htmlentities($form); ?></h4>
                                        
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                </ol>
                        </nav>
                        <!-- Statistics -->
                        <?php 
                        // Count students, males, and females
                        $totalStudents = count($students);
                        $maleCount = 0;
                        $femaleCount = 0;
                        foreach ($students as $student) {
                            if ($student['Gender'] == 'Male') {
                                $maleCount++;
                            } else {
                                $femaleCount++;
                            }
                        }
                        ?>
                        <h5>Total Students: <?php echo $totalStudents; ?></h5>
                       
                    </div>
                    <div class="row">
                    <div class="row">
                    <div style="
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    gap: 20px !important;
    margin-bottom: 20px !important;
    width: 100% !important;
">
    <!-- Search Input (Left) -->
    <input 
        type="text" 
        id="myInput" 
        class="form-control" 
        placeholder="Start typing to search..."
        style="
            border: 2px solid #FFD700 !important;
            background-color: #003366 !important;
            color: white !important;
            font-weight: bold !important;
            padding: 12px 20px !important;
            border-radius: 30px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2) !important;
            transition: 0.3s ease-in-out !important;
            flex: 1 !important;
            max-width: 400px !important;
        "
        onfocus="this.style.boxShadow='0 0 10px #FFD700';" 
        onblur="this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.2)';"
    >

    <!-- Progress Tracker (Right) -->
    <div style="
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        background: #003366 !important;
        padding: 10px 20px !important;
        border-radius: 30px !important;
        color: white !important;
        font-weight: bold !important;
        min-width: 250px !important;
        border: 2px solid #FFD700 !important;
    ">
        <?php
        $completed = 0;
        foreach($students as $student) {
            if(isset($student['test2']) && $student['test2'] !== 'NA') {
                $completed++;
            }
        }
        $percentage = $totalStudents > 0 ? round(($completed / $totalStudents) * 100) : 0;
        ?>
        
        <div style="flex-grow: 1 !important;">
            <div style="
                display: flex !important;
                justify-content: space-between !important;
                margin-bottom: 5px !important;
            ">
                <span>Progress:</span>
                <span><?= $completed ?> / <?= $totalStudents ?></span>
            </div>
            <div style="
                height: 10px !important;
                background: #1a3a6e !important;
                border-radius: 5px !important;
                overflow: hidden !important;
            ">
                <div style="
                    height: 100% !important;
                    width: <?= $percentage ?>% !important;
                    background: #FFD700 !important;
                    transition: width 0.5s ease !important;
                "></div>
            </div>
        </div>
    </div>
</div>



                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-sm-flex align-items-center mb-4">
                                        <div class="table-responsive border rounded p-1">
                                            <table class="table">
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
                                                <thead>
<tr style="background-color: #003366; color: white; text-transform: uppercase;">
    <th class="font-weight-bold" style="padding: 10px; ">Student Name</th>

    <?php if ($exam == 'Mid') { ?>
        <th class="font-weight-bold" style="padding: 10px; ">MID (40)</th>
        <th class="font-weight-bold" style="padding: 10px; ">MID (100%)</th>
        <th class="font-weight-bold" style="padding: 10px; ">Action</th>
    <?php } elseif ($exam == 'End') { ?>
    <!--
        <th class="font-weight-bold" style="padding: 10px;">MID (100%)</th> -->
        <th class="font-weight-bold" style="padding: 10px;">SCORE(100%)</th>
        <th class="font-weight-bold" style="padding: 10px;">END OF TERM</th>
        <th class="font-weight-bold" style="padding: 10px; ">Action</th>
    <?php } ?>
</tr>

</thead>
                                                <tbody id="myTable">
                                                    <?php
                                                    // Display student details
                                                    $cnt = 1;
                                                    foreach ($students as $student) {
                                                    ?>
                                                    <tr data-student-id='<?php echo htmlentities($student['ID']); ?>'>
 
                                                        <!-- Editable Test Scores -->
                                                        <form action="update-scores2.php" method="post">
                                                            <input type="hidden" name="academic_year" value="<?php echo htmlentities($academicYear); ?>">
                                                            <input type="hidden" name="subname" value="<?php echo htmlentities($name); ?>">
                                                            <input type="hidden" name="class" value="<?php echo htmlentities($student['StudentClass']); ?>">
                                                            <input type="hidden" name="term" value="<?php echo htmlentities($activeTerm); ?>">
                                                            <input type="hidden" name="sub_id" value='<?php echo htmlentities($sub); ?>'>
                                                            <input type="hidden" name="form" value='<?php echo htmlentities($form); ?>'>
                                                            <input type="hidden" name="exam" value='<?php echo htmlentities($exam); ?>'>
                                                            <input type="hidden" name="name" value='<?php echo htmlentities($student['StudentName']); ?>'>
                                                            <input type="hidden" name="student_id" value="<?php echo htmlentities($student['ID']); ?>">                                                   <td><?php echo htmlentities($student['StudentName']); ?></td>
<?php if ($exam == 'Mid') { ?>
<td>
    <input 
        type="text" 
        name="test1" 
        value="<?php echo htmlentities($student['test1']); ?>" 
        class="editable-cell final-grade form-control" 
        oninput="validateInput(this, 0, 40)"
        placeholder="0-40 or AB"
        minlength="2"
        style="width: 100%; padding: 10px; font-size: 16px; box-sizing: border-box;" 
    >
</td>

    <td>
        <input 
            type="text" 
            name="mid_percent" 
            value="<?php 
                $midPercent = is_numeric($student['test1']) ? ($student['test1'] / 40) * 100 : 0;
                echo number_format($midPercent, 0); 
            ?>" 
            class="editable-cell final-grade form-control" 
            readonly
            style="background-color: <?= $midPercent >= 50 ? '#003366' : 'red'; ?>; 
                                    color: #FFFFFF; 
                   border-radius: 50%; 
                   width: 40px; 
                   text-align: center;
                   font-weight: bold;
                   padding: 5px"
        >
    </td>
<?php } elseif ($exam == 'End') { ?>
<!-- <td>
<input  type="text" 
            name="mid_percent" 
            value="<?php 
                $midPercent = is_numeric($student['test1']) ? ($student['test1'] / 40) * 100 : 0;
                echo number_format($midPercent, 0); 
            ?>" 
            class="editable-cell final-grade form-control" 
            readonly
            style="background-color: <?= $midPercent >= 50 ? '#003366' : 'red'; ?>; 
                                    color: #FFFFFF; 
                   border-radius: 50%; 
                   width: 40px; 
                   text-align: center;
                   font-weight: bold;
                   padding: 5px"
        >
    </td> -->
    
 
   <td>
 <input 
    type="hidden" 
    name="test1" 
    value="<?= 
        is_numeric($student['test1']) ? number_format($student['test1'], 0) : 0; 
    ?>"
>
    <?php 
      //$test1 = is_numeric($student['test1']) ? $student['test1'] : 0;
      $test1 = 0;
$test2 = is_numeric($student['test2']) ? $student['test2'] : 0;
$final = $test1 + $test2;
    ?>
    <input 
        type="button" 
        name="final" 
        value="<?= htmlentities($final); ?>" 
        class="editable-cell final-grade form-control" 
        readonly 
        style="background-color: <?= ($final >= 40) ? '#003366' : 'red'; ?>; 
               color: #FFFFFF; 
               border-radius: 50%; 
               width: 40px; 
               text-align: center;
               font-weight: bold;
               padding: 5px"
    >
</td>
<?php
// Start session and get exam entry status
$examentry_status = $_SESSION['examentry_status'] ?? 'No'; // Default to 'No' if not set
$is_disabled = ($examentry_status === 'No');
?>

<!-- Input Field with Hover Tooltip -->
<td>
    <div class="tooltip-container" style="position:relative; display:inline-block; width:100%;">
        <input 
            type="text" 
            name="test2" 
            value="<?php echo htmlentities($student['test2'] ?? ''); ?>" 
            class="editable-cell final-grade form-control" 
            oninput="validateInput(this, 0, 100)"
            placeholder="0-100 or AB"
            minlength="2"
            style="width: 100%; padding: 10px; font-size: 16px; box-sizing: border-box;
                   <?php echo $is_disabled ? 'background-color: #f5f5f5; cursor: not-allowed;' : ''; ?>"
            <?php echo $is_disabled ? 'disabled' : ''; ?>
            <?php echo $is_disabled ? 'title="Editing not allowed currently"' : ''; ?>
        >
        <?php if ($is_disabled): ?>
        <div class="tooltip-text" style="visibility: hidden; width: 180px; background-color: #003366; color: #fff; text-align: center; border-radius: 6px; padding: 5px; position: absolute; z-index: 1; bottom: 125%; left: 50%; margin-left: -90px; opacity: 0; transition: opacity 0.3s;">
            Editing/Entry not allowed currently
        </div>
        <?php endif; ?>
    </div>
</td>
<?php } ?>
<!-- Submit Button with Hover Tooltip -->
<td>
    <div class="tooltip-container" style="position:relative; display:inline-block;">
        <button 
            type="submit" 
            class="btn btn-warning submit-icon" 
            style="background-color: #FFFF00; border-color: #FFFF00;
                   <?php echo $is_disabled ? 'opacity: 0.6; cursor: not-allowed;' : ''; ?>"
            <?php echo $is_disabled ? 'disabled' : ''; ?>
            <?php echo $is_disabled ? 'title="Submission not allowed currently"' : ''; ?>
        >
            <i class="fas fa-check" style="color: #0000FF;"></i>
        </button>
        <?php if ($is_disabled): ?>
        <div class="tooltip-text" style="visibility: hidden; width: 180px; background-color: #003366; color: #fff; text-align: center; border-radius: 6px; padding: 5px; position: absolute; z-index: 1; bottom: 125%; left: 50%; margin-left: -90px; opacity: 0; transition: opacity 0.3s;">
            Submission not allowed currently
        </div>
        <?php endif; ?>
    </div>
</td>
                                                    </form>
                                                    </tr>
                                                    <?php
                                                        $cnt++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
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
   <script>
function validateInput(input, min, max) {
    var value = input.value.trim().toUpperCase();
    if (value === 'AB') {
        input.setCustomValidity(''); // Allow 'AB'
        return;
    }
    var numericValue = parseInt(value);
    if (isNaN(numericValue) || numericValue < min || numericValue > max) {
        input.setCustomValidity('Invalid input. Enter a whole number between ' + min + ' and ' + max + ' or AB');
        input.reportValidity();
    } else {
        input.setCustomValidity('');
    }
}
</script>

<!-- JavaScript for Tooltip Behavior -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.tooltip-container');
    
    containers.forEach(container => {
        const input = container.querySelector('input, button');
        const tooltip = container.querySelector('.tooltip-text');
        
        if (input && tooltip) {
            container.addEventListener('mouseenter', () => {
                if (input.disabled) {
                    tooltip.style.visibility = 'visible';
                    tooltip.style.opacity = '1';
                }
            });
            
            container.addEventListener('mouseleave', () => {
                tooltip.style.visibility = 'hidden';
                tooltip.style.opacity = '0';
            });
        }
    });
});
</script> 
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
    <!-- Custom js for this page --><!-- JavaScript for capturing changes and sending AJAX request -->


    <!-- End custom js for this page -->
</body>

</html>