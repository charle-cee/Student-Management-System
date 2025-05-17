<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        $subject = $_POST['subject'];
        $name = $_POST['description'];
        $author = $_POST['author'];
        $year = $_POST['year'];
        $bookNumber = strtoupper($_POST['book_number']); // Convert to uppercase
        $class = $_POST['class'];
        $dateAddedd = date('Y-m-d');

        // Check if the book number already exists
        $sqlCheckBookNumber = "SELECT * FROM tblbook WHERE book_number = :bookNumber";
        $queryCheckBookNumber = $dbh->prepare($sqlCheckBookNumber);
        $queryCheckBookNumber->bindParam(':bookNumber', $bookNumber, PDO::PARAM_STR);
        $queryCheckBookNumber->execute();

        if ($queryCheckBookNumber->rowCount() > 0) {
            header('location: register-books.php?error='.urlencode($subject).' '.urlencode($bookNumber).' already exists.');
            exit();
        }

        $sql = "INSERT INTO tblbook (subject, description, author, year, book_number, class, dateAdded) 
                VALUES (:subject, :name, :author, :year, :bookNumber, :class, :dateAdded)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':subject', $subject, PDO::PARAM_STR);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':author', $author, PDO::PARAM_STR);
        $query->bindParam(':year', $year, PDO::PARAM_INT);
        $query->bindParam(':bookNumber', $bookNumber, PDO::PARAM_STR);
        $query->bindParam(':class', $class, PDO::PARAM_INT);
        $query->bindParam(':dateAdded', $dateAddedd, PDO::PARAM_STR);

        if ($query->execute()) {
            header('location: register-books.php?success='.urlencode($subject).' Book '.urlencode($class).' added successfully.');
            exit();
        } else {
            header('location: register-books.php?error='.urlencode($subject).' Book '.urlencode($class).'failed to add');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Student Management System || Book Registration</title>
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
                    <h3 class="page-title"> Book Registration </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Book Registration</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align: center;background-color: yellow; color: #003366; padding: 10px;">Book Registration</h4>
<form class="forms-sample" method="post">
    <!-- Error Message -->
    <?php if (isset($_GET['error'])) { ?>
        <div class="alert alert-danger alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <b><?php echo $_GET['error']; ?></b>
        </div><br>
    <?php } ?>

    <!-- Success Message -->
    <?php if (isset($_GET['success'])) { ?>
        <div class="alert alert-success alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <b><?php echo $_GET['success']; ?></b>
        </div>
    <?php } ?>

    <!-- Row 1: Subject & Description -->
    <div class="d-flex justify-content-between align-items-center mb-4" style="gap: 20px;">
        <div style="flex: 1;">
            <label for="subject">Subject Name:</label>
            <select name="subject" class="form-control" required>
                <option selected disabled>Select Subject</option>
                <option value="English Literature">English Literature</option>
                <option value="English Language">English Language</option>
                <option value="Chichewa Literature">Chichewa Literature</option>
                <option value="Chichewa Language">Chichewa Language</option>
                <option value="Biology">Biology</option>
                <option value="Agriculture">Agriculture</option>
                <option value="Bible Knowledge">Bible Knowledge</option>
                <option value="Social Studies">Social Studies</option>
                <option value="Life Skills">Life Skills</option>
                <option value="Computer Studies">Computer Studies</option>
                <option value="Mathematics">Mathematics</option>
                <option value="Business Studies">Business Studies</option>
                <option value="Chemistry">Chemistry</option>
                 <option value="History">History</option>
                <option value="Physics">Physics</option>
                <option value="Geography">Geography</option>
            </select>
        </div>
        <div style="flex: 1;">
            <label for="description">Description:</label>
            <input type="text" id="description" name="description" class="form-control" required>
        </div>
    </div>

    <!-- Row 2: Author & Year -->
    <div class="d-flex justify-content-between align-items-center mb-4" style="gap: 20px;">
        <div style="flex: 1;">
            <label for="author">Author:</label>
            <input type="text" id="author" name="author" class="form-control" required>
        </div>
        <div style="flex: 1;">
            <label for="year">Year:</label>
            <input type="text" id="year" name="year" class="form-control" required>
        </div>
    </div>

    <!-- Row 3: Book Number & Class -->
    <div class="d-flex justify-content-between align-items-center mb-4" style="gap: 20px;">
        <div style="flex: 1;">
            <label for="book_number">Book Number:</label>
            <input type="text" id="book_number" name="book_number" class="form-control" required>
        </div>
        <div style="flex: 1;">
            <label for="class">Class:</label>
            <select id="class" name="class" class="form-control" required>
                <option selected disabled>Select Class</option>
                <option value="1">Book 1</option>
                <option value="2">Book 2</option>
                <option value="3">Book 3</option>
                <option value="4">Book 4</option>
            </select>
        </div>
    </div>

    <!-- Buttons Section -->
    <div class="d-flex justify-content-between mt-4">
        <!-- Add Book Button -->
        <button type="submit" class="btn mr-2" name="submit" style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 40px; border: none;">
            Add Book
        </button>

        <!-- Cancel Button -->
        <button type="button" class="btn" style="background-color: yellow; color: #003366; padding: 10px 20px; border-radius: 40px; border: none;" onclick="window.history.back();">
            Cancel
        </button>
    </div>
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
<!-- Custom js for this page -->
<script src="js/typeahead.js"></script>
<script src="js/select2.js"></script>
<!-- End custom js for this page -->
</body>
</html>
