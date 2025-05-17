<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Check if the book exists
        $sqlCheckBook = "SELECT * FROM tblbook WHERE id = :id";
        $queryCheckBook = $dbh->prepare($sqlCheckBook);
        $queryCheckBook->bindParam(':id', $id, PDO::PARAM_INT);
        $queryCheckBook->execute();

        if ($queryCheckBook->rowCount() > 0) {
            $book = $queryCheckBook->fetch(PDO::FETCH_ASSOC);

            // Populate the form fields with existing book details
            $subject = $book['subject'];
            $description = $book['description'];
            $author = $book['author'];
            $year = $book['year'];
            $bookNumber = $book['book_number'];
            $class = $book['class'];

            if (isset($_POST['submit'])) {
                $subject = $_POST['subject'];
                $description = $_POST['description'];
                $author = $_POST['author'];
                $year = $_POST['year'];
                $bookNumber = strtoupper($_POST['book_number']); // Convert to uppercase
                $class = $_POST['class'];

                // Update book details in the database
                $sqlUpdateBook = "UPDATE tblbook 
                                  SET subject = :subject, 
                                      description = :description, 
                                      author = :author, 
                                      year = :year, 
                                      book_number = :bookNumber, 
                                      class = :class
                                  WHERE id = :id";
                $queryUpdateBook = $dbh->prepare($sqlUpdateBook);
                $queryUpdateBook->bindParam(':subject', $subject, PDO::PARAM_STR);
                $queryUpdateBook->bindParam(':description', $description, PDO::PARAM_STR);
                $queryUpdateBook->bindParam(':author', $author, PDO::PARAM_STR);
                $queryUpdateBook->bindParam(':year', $year, PDO::PARAM_INT);
                $queryUpdateBook->bindParam(':bookNumber', $bookNumber, PDO::PARAM_STR);
                $queryUpdateBook->bindParam(':class', $class, PDO::PARAM_INT);
                $queryUpdateBook->bindParam(':id', $id, PDO::PARAM_INT);

                if ($queryUpdateBook->execute()) {
                    header('location: edit-book.php?id='.urlencode($id).'&success='.urlencode($subject).' Book '.urlencode($class).' updated successfully.');
                    exit();
                } else {
                    header('location: edit-book.php?='.urlencode($id).'&error='.urlencode($subject).' Book '.urlencode($class).' failed to update');
                    exit();
                }
            }
        } else {
            // Book not found
            header('location: edit-book.php?='.urlencode($id).'&error=Book not found');
            exit();
        }
    } 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
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
                        <h3 class="page-title">Book Details</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Book Details</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
    <h4 class="card-title text-center" style="background-color: #003366; color: white; padding: 10px" > Book Details</h4>

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

    <form class="forms-sample" method="post">
        <!-- Row 1: Subject & Description -->
        <div class="d-flex justify-content-between align-items-center mb-4" style="gap: 20px;">
            <div style="flex: 1;">
                <label for="subject">Subject Name:</label>
                <select name="subject" class="form-control" required>
                    <option selected disabled>Select Subject</option>
                    <option value="English Literature" <?php if ($subject == 'English Literature') echo 'selected'; ?>>English Literature</option>
                    <option value="English Language" <?php if ($subject == 'English Language') echo 'selected'; ?>>English Language</option>
                    <option value="Chichewa Literature" <?php if ($subject == 'Chichewa Literature') echo 'selected'; ?>>Chichewa Literature</option>
                    <option value="Chichewa Language" <?php if ($subject == 'Chichewa Language') echo 'selected'; ?>>Chichewa Language</option>
                    <option value="Biology" <?php if ($subject == 'Biology') echo 'selected'; ?>>Biology</option>
                    <option value="Agriculture" <?php if ($subject == 'Agriculture') echo 'selected'; ?>>Agriculture</option>
                    <option value="Bible Knowledge" <?php if ($subject == 'Bible Knowledge') echo 'selected'; ?>>Bible Knowledge</option>
                    <option value="Social Studies" <?php if ($subject == 'Social Studies') echo 'selected'; ?>>Social Studies</option>
                    <option value="Life Skills" <?php if ($subject == 'Life Skills') echo 'selected'; ?>>Life Skills</option>
                    <option value="Computer Studies" <?php if ($subject == 'Computer Studies') echo 'selected'; ?>>Computer Studies</option>
                    <option value="Mathematics" <?php if ($subject == 'Mathematics') echo 'selected'; ?>>Mathematics</option>
                    <option value="Business Studies" <?php if ($subject == 'Business Studies') echo 'selected'; ?>>Business Studies</option>
                    <option value="Chemistry" <?php if ($subject == 'Chemistry') echo 'selected'; ?>>Chemistry</option>
                    <option value="Physics" <?php if ($subject == 'Physics') echo 'selected'; ?>>Physics</option>
                    <option value="Physics" <?php if ($subject == 'History') echo 'selected'; ?>>History</option>
                    <option value="Geography" <?php if ($subject == 'Geography') echo 'selected'; ?>>Geography</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" class="form-control" required value="<?php echo $description; ?>">
            </div>
        </div>

        <!-- Row 2: Author & Year -->
        <div class="d-flex justify-content-between align-items-center mb-4" style="gap: 20px;">
            <div style="flex: 1;">
                <label for="author">Author:</label>
                <input type="text" id="author" name="author" class="form-control" required value="<?php echo $author; ?>">
            </div>
            <div style="flex: 1;">
                <label for="year">Year:</label>
                <input type="text" id="year" name="year" class="form-control" required value="<?php echo $year; ?>">
            </div>
        </div>

        <!-- Row 3: Book Number & Class -->
        <div class="d-flex justify-content-between align-items-center mb-4" style="gap: 20px;">
            <div style="flex: 1;">
                <label for="book_number">Book Number:</label>
                <input type="text" id="book_number" name="book_number" class="form-control" required value="<?php echo $bookNumber; ?>">
            </div>
            <div style="flex: 1;">
                <label for="class">Class:</label>
                <select id="class" name="class" class="form-control" required>
                    <option selected disabled>Select Class</option>
                    <option value="1" <?php if ($class == 1) echo 'selected'; ?>>Book 1</option>
                    <option value="2" <?php if ($class == 2) echo 'selected'; ?>>Book 2</option>
                    <option value="3" <?php if ($class == 3) echo 'selected'; ?>>Book 3</option>
                    <option value="4" <?php if ($class == 4) echo 'selected'; ?>>Book 4</option>
                </select>
            </div>
        </div>

        <!-- Buttons Section -->
        <div class="d-flex justify-content-between mt-4">
            <!-- Update Book Button -->
            <button type="submit" class="btn" name="submit" style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 40px; border: none;">
                Update Book
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
