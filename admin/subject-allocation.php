<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
} else {
    if (isset($_POST['submit'])) {
        $teacherID = $_POST['teacherID'];
        $majorSubjectID = $_POST['majorSubjectID'];
        $minorSubjectID = $_POST['minorSubjectID'];
        $otherSubjectID = $_POST['otherSubjectID'];
        $class = $_POST['class'];

        // Check if the major or minor subjects for the selected teacher are already allocated
        $sqlCheckAllocation = "SELECT Major, Minor, Other FROM tbladmin WHERE ID = :teacherID";
        $queryCheckAllocation = $dbh->prepare($sqlCheckAllocation);
        $queryCheckAllocation->bindParam(':teacherID', $teacherID, PDO::PARAM_INT);
        $queryCheckAllocation->execute();
        $row = $queryCheckAllocation->fetch();

        if (!empty($row['Major']) || !empty($row['Minor'])) {
            // Reallocation logic
            $allostatus = 'Allocated';
            $sql = "UPDATE tbladmin SET Major = :majorSubjectID, Minor = :minorSubjectID, Other = :otherSubjectID, AllocationStatus = :allostatus WHERE ID = :teacherID";
            $query = $dbh->prepare($sql);
            $query->bindParam(':teacherID', $teacherID, PDO::PARAM_INT);
            $query->bindParam(':majorSubjectID', $majorSubjectID, PDO::PARAM_INT);
            $query->bindParam(':minorSubjectID', $minorSubjectID, PDO::PARAM_INT);
            $query->bindParam(':otherSubjectID', $otherSubjectID, PDO::PARAM_INT);
            $query->bindParam(':allostatus', $allostatus, PDO::PARAM_STR);
            $query->execute();

            if ($query) {
                // Direct redirection with success message
                header("location: edit-allocation.php?id=$teacherID&sub1=$majorSubjectID&sub2=$minorSubjectID&sub3=$otherSubjectID&success=Subject+Reallocation+is+successful. <br>Notice that you have automatically redirected to this page in order to assign class for the allocated subject(s)");
                exit();
            } else {
                header('location: subject-allocation.php?error=Failed+to+reallocate+subjects.');
                exit();
            }
        }

        if (empty($majorSubjectID) && empty($minorSubjectID) && empty($otherSubjectID)) {
            header('location: subject-allocation.php?error=Select+at+least+one+subject+to+allocate');
            exit();
        } else {
            // Allocation logic
            $allostatus = 'Allocated';
            $sql = "UPDATE tbladmin SET Major = :majorSubjectID, Minor = :minorSubjectID, Other = :otherSubjectID, AllocationStatus = :allostatus WHERE ID = :teacherID";
            $query = $dbh->prepare($sql);
            $query->bindParam(':teacherID', $teacherID, PDO::PARAM_INT);
            $query->bindParam(':majorSubjectID', $majorSubjectID, PDO::PARAM_INT);
            $query->bindParam(':minorSubjectID', $minorSubjectID, PDO::PARAM_INT);
            $query->bindParam(':otherSubjectID', $otherSubjectID, PDO::PARAM_INT);
            $query->bindParam(':allostatus', $allostatus, PDO::PARAM_STR);
            $query->execute();

            if ($query) {
                // Direct redirection with success message
                header("location: edit-allocation.php?id=$teacherID&sub1=$majorSubjectID&sub2=$minorSubjectID&sub3=$otherSubjectID&success=Subject+Allocation+is+successful.");
                exit();
            } else {
                header('location: subject-allocation.php?error=Failed+to+allocate+subjects.');
                exit();
            }
        }
    }

    // Fetch teachers and subjects for the form
    $sqlTeachers = "SELECT ID, Name FROM tbladmin";
    $queryTeachers = $dbh->prepare($sqlTeachers);
    $queryTeachers->execute();
    $teachers = $queryTeachers->fetchAll(PDO::FETCH_ASSOC);

    $sqlSubjects = "SELECT ID, Name FROM tblsubject";
    $querySubjects = $dbh->prepare($sqlSubjects);
    $querySubjects->execute();
    $subjects = $querySubjects->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/jpg" href="logo.jpg">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Student Management System|| Subject Allocation</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="css/style.css" />
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
                    <h3 class="page-title"> Subject Allocation </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Subject Allocation</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align: center; background-color: #003366; color: white; padding: 10px 20px;">Subject Allocation</h4>
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
<form class="forms-sample" method="post">
    <!-- Error and success messages -->
    
    <div class="form-group">
        <select name="teacherID" class="form-control" required>
            <option value="">Select Teacher</option>
            <?php foreach ($teachers as $teacher) { ?>
                <option value="<?php echo $teacher['ID']; ?>"><?php echo $teacher['Name']; ?></option>
            <?php } ?>
        </select>
    </div>    
    <div class="form-group">
        <select name="majorSubjectID" class="form-control" required id="major-subject-select">
            <option value="" disabled selected>Select First Subject</option>
            <?php foreach ($subjects as $subject) { ?>
                <option value="<?php echo $subject['ID']; ?>"><?php echo $subject['Name']; ?></option>
            <?php } ?>
        </select>   </div>

    <div class="form-group">
        <select name="minorSubjectID" class="form-control" id="minor-subject-select">
            <option value="" disabled selected>Select Second Subject</option>
            <?php foreach ($subjects as $subject) { ?>
                <option value="<?php echo $subject['ID']; ?>"><?php echo $subject['Name']; ?></option>
            <?php } ?>
        </select>
           </div>

    <div class="form-group">
        <select name="otherSubjectID" class="form-control"  id="other-subject-select">
            <option value="" disabled selected>Select Third Subject</option>
            <?php foreach ($subjects as $subject) { ?>
                <option value="<?php echo $subject['ID']; ?>"><?php echo $subject['Name']; ?></option>
            <?php } ?>
        </select>
    </div>
    <input type="hidden" name="majorFinalSubject" id="major-final-subject">
    <input type="hidden" name="minorFinalSubject" id="minor-final-subject">
    <input type="hidden" name="otherFinalSubject" id="other-final-subject">
    <input type="hidden" name="majorFinalSubject" id="major-final-subject">
    <input type="hidden" name="minorFinalSubject" id="minor-final-subject">
    <input type="hidden" name="otherFinalSubject" id="other-final-subject">
    
    <!-- Buttons Section -->
        <div class="d-flex justify-content-between mt-4">
            <!-- Update Book Button -->
            <button type="submit" class="btn" name="submit" style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 40px; border: none;">
                Allocate Subject(s)
            </button>

            <!-- Cancel Button -->
            <button type="button" class="btn" style="background-color: yellow; color: #003366; padding: 10px 20px; border-radius: 40px; border: none;" onclick="window.history.back();">
                Cancel
</form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
            <!-- partial:partials/_footer.html -->
            <?php include_once('includes/footer.php');?>
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
<script>
    const majorSelect = document.getElementById('major-subject-select');
    const minorSelect = document.getElementById('minor-subject-select');
    const otherSelect = document.getElementById('other-subject-select');

    // Function to update the select options based on current selections
    function updateSelectOptions() {
        const selectedMajor = majorSelect.value;
        const selectedMinor = minorSelect.value;
        const selectedOther = otherSelect.value;

        const allSelects = [majorSelect, minorSelect, otherSelect];
        const selectedValues = [selectedMajor, selectedMinor, selectedOther];

        // Iterate over all selects to disable the selected options in others
        allSelects.forEach(select => {
            const currentSelectValue = select.value;

            // Remove all disabled options
            Array.from(select.options).forEach(option => {
                option.disabled = false;
            });

            // Disable options that are already selected in other selects
            selectedValues.forEach(value => {
                if (value && value !== currentSelectValue) {
                    Array.from(select.options).forEach(option => {
                        if (option.value === value) {
                            option.disabled = true;
                        }
                    });
                }
            });
        });
    }

    // Add event listeners to update options on change
    majorSelect.addEventListener('change', updateSelectOptions);
    minorSelect.addEventListener('change', updateSelectOptions);
    otherSelect.addEventListener('change', updateSelectOptions);
</script>
<!-- Plugin js for this page -->
<script src="vendors/select2/select2.min.js"></script>
<script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="js/off-canvas.js"></script>
<script src="js/misc.js"></script>
<!-- endinject -->
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
<!-- Custom js for this page -->
<script src="js/typeahead.js"></script>
<script src="js/select2.js"></script>
<!-- End custom js for this page -->
</body>
</html>
