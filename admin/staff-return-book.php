<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_GET['id']) && isset($_GET['name']) && isset($_GET['role'])) {
        $stuID = $_GET['id'];
        $name = $_GET['name'];
        $role = $_GET['role'];

        // Check if the book exists
        $sql = "SELECT * FROM tblbook WHERE UserID = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $stuID, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <title>Student Management System || Staff Returning Book</title>
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
                <style>
                    .return-btn {
                        background-color: #007bff;
                        color: #fff;
                        border: none;
                        padding: 5px 10px;
                        border-radius: 5px;
                        cursor: pointer;
                    }
                </style>
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
                                <h3 class="page-title"> Staff Returning Book </h3>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page"> Staff Returning Book</li>
                                    </ol>
                                </nav>
                            </div>
                            <div class="row">
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title" style="text-align: center; background-color: yellow; color: #003366; padding: 10px;">staff Returning Book</h4>
                                            <table class="table">
                                            <?php if (isset($_SESSION['error'])) { ?>
                                    <div class="alert alert-danger alert-dismissible text-center">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <b><?php echo $_GET['error']; ?></b>
                                    </div> <br>
                                <?php } ?>
                                <?php if (isset($_SESSION['success'])) { ?>
                                    <div class="alert alert-success alert-dismissible text-center">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <b><?php echo $_GET['success']; ?></b>
                                    </div>
                                <?php } ?>
                                                <thead>
                                                    <tr style="background-color: #003366; color : white; padding: 10px; text-transform : uppercase;">
                                                        <th>#</th>
                                                        <th>Book Number</th>
                                                        <th>Subject</th>
                                                        <th>Description</th>
                                                        <th>Author</th>
                                                        <th>Year</th>
                                                        <th>Class</th>
                                                        <th>Date Taken</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $cnt = 1;
                                                    foreach ($result as $row) {
                                                        ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($cnt);?></td>
                                                            <td><?php echo htmlentities($row['book_number']);?></td>
                                                            <td><?php echo htmlentities($row['subject']);?></td>
                                                            <td><?php echo htmlentities($row['description']);?></td>
                                                            <td><?php echo htmlentities($row['author']);?></td>
                                                            <td><?php echo htmlentities($row['year']);?></td>
                                                            <td><?php echo htmlentities($row['class']);?></td>
                                                            <td><?php echo htmlentities($row['dateTaken']);?></td>
                                                            <td>
                                                                <button class="return-btn"style="background-color: yellow; color: #003366; padding: 10px;" onclick="confirmReturn('<?php echo htmlentities($row['book_number']); ?>')">Return</button>
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

                    <!-- partial -->
                </div>
                <!-- main-panel ends -->
                <?php include_once('includes/footer.php');?>
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

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery library -->

<script>
    function confirmReturn(bookNumber, stuID, name, className) {
        if (confirm('Are you sure you want to return the book with number ' + bookNumber + '?')) {
            $.ajax({
                type: 'POST',
                url: 'staff-check-book.php',
                data: { 
                    book_number: bookNumber,
                    stu_id: stuID,
                    name: name,
                    class: className
                },
                success: function(response) {
                    alert(response); // Display success message or handle as needed
                    window.location.reload(); // Reload the page after successful return
                },
                error: function() {
                    alert('Error: Failed to return book.'); // Display error message
                }
            });
        }
    }
</script>


            </body>
            </html>
            <?php
            exit();
        } else {
            header('location: staff-borrow.php?error= ' . urlencode($name) . ', The ' . urlencode($role) . ', has not yet borrowed any book ');
            exit();
        }
    } else {
        header('location: staff-return-book.php?error=Invalid parameters.');
        exit();
    }
}
?>
