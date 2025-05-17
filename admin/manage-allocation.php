<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['sturecmsaid'] == 0)) {
    header('location:logout.php');
} else {
    // Code for deletion
    if (isset($_GET['delid'])) {
        $rid = intval($_GET['delid']);
        $sql = "delete from tbladmin where ID=:rid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_STR);
        $query->execute();
        header('location:manage-staff.php?success=Deleted sucessfully.');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <title>Student Management System|||Manage Subject Allocation</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="./css/style.css">
    <!-- End layout styles -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                        <h3 class="page-title"> Manage Subject Allocation </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Manage Subject Allocation</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-sm-flex align-items-center mb-4">
                                        <h4 class="card-title mb-sm-0">Manage Staff</h4>
                                        <div class="col-md-4">
                                        <input type="text" id="myInput" class="form-control primary-search" placeholder="Start typing..">
                                    </div>
                                    <a href="manage-subject.php" class="ml-auto mb-3 mb-sm-0" 
   style="background-color: #003366 !important; 
          color: white !important; 
          padding: 8px 15px !important; 
          border-radius: 5px !important; 
          border: none !important; 
          text-decoration: none !important; 
          display: inline-block !important; 
          cursor: pointer !important;
          font-weight: bold !important;
          text-align: center !important;
          width: fit-content !important;
          ">Add New Subject</a>
                                        <a href="subject-allocation.php" class="ml-auto mb-3 mb-sm-0" 
   style="background-color: #003366 !important; 
          color: white !important; 
          padding: 8px 15px !important; 
          border-radius: 5px !important; 
          border: none !important; 
          text-decoration: none !important; 
          display: inline-block !important; 
          cursor: pointer !important;
          font-weight: bold !important;
          text-align: center !important;
          width: fit-content !important;
          ">Allocate Subject</a>
                                    </div>
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
                                    <div class="table-responsive border rounded p-1">
                                       <table class="table table-striped">
    <thead>
        <tr style="background-color: #003366; color: white; padding: 10px; text-transform : uppercase;">
            <th class="font-weight-bold">#</th>
            <th class="font-weight-bold">Staff Name</th>
            <th class="font-weight-bold">Subject 1</th>
            <th class="font-weight-bold">Subject 2</th>
            <th class="font-weight-bold">Subject 3</th>
            <th class="font-weight-bold">Action</th>
        </tr>
    </thead>
    <tbody id="myTable">
    <?php
    // Fetch all allocated records
    $sql = "SELECT * FROM tbladmin WHERE tbladmin.AllocationStatus='Allocated'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    // Function to get the shortened subject name by ID
    function getShortSubjectName($subjectId) {
        // Fetch the subject name from the database based on the ID
        global $dbh;
        $sql = "SELECT Name FROM tblsubject WHERE ID = :subjectId";
        $query = $dbh->prepare($sql);
        $query->bindParam(':subjectId', $subjectId, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        // If the subject is found, shorten the name to the first 3 letters and convert to uppercase
        if ($result) {
            return strtoupper(substr($result->Name, 0, 3));
        } else {
            return null; // Return null if the subject is not found
        }
    }

    $cnt = 1;
    foreach ($results as $row) {
    ?>
    <tr>
        <td><?php echo htmlentities($cnt); ?></td>
        <td><?php echo htmlentities($row->Name); ?></td>
        <td>
            <?php
            $majorParts = explode('-', $row->Major);
            $majorSubjectId = $majorParts[0]; // Get the subject ID
            $majorFormNumbers = array_slice($majorParts, 1); // Get the form numbers
            $majorSubjectName = getShortSubjectName($majorSubjectId); // Get the shortened subject name

            // Display "Not Allocated" if subject name is not found, otherwise display the subject name and form numbers
            if ($majorSubjectName === null) {
                echo htmlentities('Not Allocated');
            } else {
                echo htmlentities($majorSubjectName . ' - Form ' . implode(',', $majorFormNumbers));
            }
            ?>
        </td>
        <td>
            <?php
            $minorParts = explode('-', $row->Minor);
            $minorSubjectId = $minorParts[0]; // Get the subject ID
            $minorFormNumbers = array_slice($minorParts, 1); // Get the form numbers
            $minorSubjectName = getShortSubjectName($minorSubjectId); // Get the shortened subject name

            // Display "Not Allocated" if subject name is not found, otherwise display the subject name and form numbers
            if ($minorSubjectName === null) {
                echo htmlentities('Not Allocated');
            } else {
                echo htmlentities($minorSubjectName . ' - Form ' . implode(',', $minorFormNumbers));
            }
            ?>
        </td>
        <td>
            <?php
            $otherParts = explode('-', $row->Other);
            $otherSubjectId = $otherParts[0]; // Get the subject ID
            $otherFormNumbers = array_slice($otherParts, 1); // Get the form numbers
            $otherSubjectName = getShortSubjectName($otherSubjectId); // Get the shortened subject name

            // Display "Not Allocated" if subject name is not found, otherwise display the subject name and form numbers
            if ($otherSubjectName === null) {
                echo htmlentities('Not Allocated');
            } else {
                echo htmlentities($otherSubjectName . ' - Form ' . implode(',', $otherFormNumbers));
            }
            ?>
        </td>
        <td> 
            <div>
                <a href="edit-allocation.php?id=<?php echo htmlentities($row->ID); ?>&sub1=<?php echo htmlentities($majorSubjectId); ?>&sub2=<?php echo htmlentities($minorSubjectId); ?>&sub3=<?php echo htmlentities($otherSubjectId); ?>">
                    <i class="icon-pencil" style="background-color: yellow; color :#003366; padding: 10px"> Edit</i>
                </a>
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
    <script src="./vendors/moment/moment.min.js"></script>
    <script src="./vendors/daterangepicker/daterangepicker.js"></script>
    <script src="./vendors/chartist/chartist.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="./js/dashboard.js"></script>
    <!-- End custom js for this page -->
</body>

</html>
