<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Check if the user is logged in
if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
}

// Add code to handle deleting a book
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM tblbook WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    header('location:manage-books.php?success=Book deleted successfully.');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <title>Student Management System || Book Registration</title>
    <!-- plugins:css -->
    <link rel="icon" type="image/jpg" href="logo.jpg">
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
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Custom JavaScript for search filter -->
    <style> .primary-search {
    color: #007bff; /* Primary color - blue */
    border-color: #007bff; /* Border color */
    /* Add any additional styling here */
}
</style>
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
                    <h3 class="page-title"> Manage Books </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Manage Books</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-sm-flex align-items-center mb-4">
                                    <h4 class="card-title mb-sm-0">Manage Books</h4>
                                    <div class="col-md-4">
                                        <input type="text" id="myInput" class="form-control primary-search" placeholder="Start typing..">
                                    </div>
                                      </div>
            
                                <div class="table-responsive border rounded p-1">
                                <table class="table">
    <thead>
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
        <tr style="background-color: #003366; color: white; padding: 10px; text-transform : uppercase;">
            <th>#</th>
            <th>Name</th>
            <th>Class</th>
            <th>Gender</th>
            <th>Total Books Borrowed</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="myTable">
        <?php
        $sql = "SELECT * FROM tblstudent";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        $cnt = 1;
        foreach ($results as $row) {
            ?>
            <tr>
                <td><?php echo htmlentities($cnt);?></td>
                <td><?php  echo htmlentities($row->StudentName);?></td>
                <td><?php  echo htmlentities($row->StudentClass);?></td>
                <td><?php  echo htmlentities($row->Gender);?></td>
                <td>
                    <?php
                    // Fetch books for this student
                    $studentID = $row->ID;
                    $book_sql = "SELECT COUNT(*) AS total_books FROM tblbook WHERE userID = :studentID";
                    $book_query = $dbh->prepare($book_sql);
                    $book_query->bindParam(':studentID', $studentID, PDO::PARAM_INT);
                    $book_query->execute();
                    $book_result = $book_query->fetch(PDO::FETCH_ASSOC);
                    echo $book_result['total_books'];
                    ?>
                </td>
                <td>
                    <div>
                        <a href="borrow-action.php?id=<?php echo htmlentities($row->ID); ?>&name=<?php echo htmlentities($row->StudentName); ?>&class=<?php echo htmlentities($row->StudentClass); ?>" class="btn" style="background-color: #003366; color: white; padding: 10px;">Borrow</a>
                        <a href="return-book.php?id=<?php echo htmlentities($row->ID); ?>&name=<?php echo htmlentities($row->StudentName); ?>&class=<?php echo htmlentities($row->StudentClass); ?>" class="btn"style="background-color: yellow; color: #003366; padding: 10px;"> Return</a>
                    </div>
                </td> 
            </tr>
            <?php $cnt = $cnt + 1;
        } ?>
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
<script src="./vendors/chart.js/Chart.min.js"></script>
<script src="./vendors/moment/moment.min.js"></script>
<script src="./vendors/daterangepicker/daterangepicker.js"></script>
<script src="./vendors/chartist/chartist.min.js"></script>
<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="js/off-canvas.js"></script>
<script src="js/misc.js"></script>
<!-- endinject -->
<!-- nano scroller -->
<script src="../assets/js/lib/menubar/sidebar.js"></script>
<script src="../assets/js/lib/preloader/pace.min.js"></script>
<!-- Custom js for this page -->
<script src="./js/dashboard.js"></script>
<!-- End custom js for this page -->
</body>
</html>
