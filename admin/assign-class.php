<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid']==0)) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        $teacherID = $_POST['teacherID'];
        $class = $_POST['class'];

        // Check if the major or minor subjects for the selected teacher are already allocated
        $sqlCheckAllocation = "SELECT Name FROM tbladmin WHERE ID = :teacherID";
        $queryCheckAllocation = $dbh->prepare($sqlCheckAllocation);
        $queryCheckAllocation->bindParam(':teacherID', $teacherID, PDO::PARAM_INT);
        $queryCheckAllocation->execute();
        $row = $queryCheckAllocation->fetch();
        $name = $row['Name'];
    
            $sql = "UPDATE tbladmin SET FormClass = :class WHERE ID = :teacherID";
            $query = $dbh->prepare($sql);
            $query->bindParam(':teacherID', $teacherID, PDO::PARAM_INT);
            $query->bindParam(':class', $class, PDO::PARAM_STR);
            $query->execute();

            if ($query) {
                header('location: assign-class.php?success= You have assigned '.$name.' as a form teacher for '.$class.'.');
                exit();
            } else {
                header('location: assign-class.php?error=Failed to assign class.');
                exit();
            }
        }
    
    $sqlTeachers = "SELECT ID, Name FROM tbladmin WHERE Role1='Form Teacher' OR UserType='Staff Member'";
    $queryTeachers = $dbh->prepare($sqlTeachers);
    $queryTeachers->execute();
    $teachers = $queryTeachers->fetchAll(PDO::FETCH_ASSOC);

    $sqlSubjects = "SELECT ClassName, Section FROM tblclass";
    $querySubjects = $dbh->prepare($sqlSubjects);
    $querySubjects->execute();
    $subjects = $querySubjects->fetchAll(PDO::FETCH_ASSOC);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <title>Student Management System|| Assign Class</title>
    <link rel="icon" type="image/jpg" href="logo.jpg">
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
                    <h3 class="page-title"> Assign Class </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Assign Class</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align: center; background:#003366; color :white; padding :10px; ">Assign Class</h4>
                                <form class="forms-sample" method="post">
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
                                    <div class="form-group">
                                        <label for="teacherID">Teacher Name</label>
                                        <select name="teacherID" class="form-control" required>
                                            <option selecetd disabled>Select Teacher</option>
                                            <?php foreach ($teachers as $teacher) { ?>
                                                <option value="<?php echo $teacher['ID']; ?>"><?php echo $teacher['Name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="minorSubject">Classs</label>
                                        <select name="class" class="form-control" required>
                                            <option selected disabled>Select Class</option>
                                            <?php foreach ($subjects as $subject) { ?>
                                                <option value="<?php echo $subject['ClassName']; ?> <?php echo $subject['Section']; ?>"><?php echo $subject['ClassName']; ?> <?php echo $subject['Section']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                   <!-- Assign Class Button -->
<button type="submit" class="btn mr-2" name="submit" style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 40px; border: none;">
    Assign Class
</button>

<!-- Cancel Button (with redirection to the previous page) -->
<button type="button" class="btn" style="background-color: yellow; color: #003366; padding: 10px 20px; border-radius: 40px; border: none;" onclick="window.history.back();">
    Cancel
</button>

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
