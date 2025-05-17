<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $academicYear = $_POST['academicYear'];
        $activeTerm = $_POST['term'];
           

      
            // Construct academic year message
            $academicYearMessage = "$academicYear Academic year, Term $activeTerm";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <title>Student Management System|||Manage Students</title>
    <!-- CSS -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <!-- jQuery -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    function sortStudents() {
        var status = document.getElementById("status").value;
        if (status !== "") {
            document.getElementById("sortForm").submit();
        }
    }
</script>
    <!-- Custom JavaScript for search filter -->
    <style> 
    .primary-search {
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
        <?php include_once('includes/header.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include_once('includes/sidebar.php'); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">Financial Details</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Manage Finances</li>
                            </ol>
                        </nav>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-sm-flex align-items-center mb-4">
                                        <div class="col-md-12">
                                            <div class="d-sm-flex align-items-baseline report-summary-header">
                                                <span class="ml-auto text-primary"></span>
                                            </div>
                                            <div class="card-body">
                                    <form method="post">
                                        <div class="form-group">
                                            <label for="academicYearSelect">Academic Year:</label>
                                            <select class="form-control" id="academicYearSelect" name="academicYear">
                                                <?php
                                                // Fetch distinct academic years from tblstudentreg
                                                $sql = "SELECT DISTINCT academic_year FROM tblaccounts";
                                                $query = $dbh->prepare($sql);
                                                $query->execute();
                                                $academicYears = $query->fetchAll(PDO::FETCH_COLUMN);
                                                foreach ($academicYears as $year) {
                                                    echo "<option value=\"$year\" " . ($year == $academicYear ? 'selected' : '') . ">$year</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="termSelect">Term:</label>
                                            <select class="form-control" id="termSelect" name="term">
                                                <?php
                                                // Fetch distinct terms from tblstudentreg for the current student
                                                $sql = "SELECT DISTINCT term FROM tblaccounts";
                                                $query = $dbh->prepare($sql);
                                                $query->execute();
                                                $terms = $query->fetchAll(PDO::FETCH_COLUMN);
                                                foreach ($terms as $termVal) {
                                                    echo "<option value=\"$termVal\" " . ($termVal == $term ? 'selected' : '') . ">$termVal</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn" style="background-color: #003366; color :white;  padding: 10px; ">View</button>
                                    </form>
                                </div>
                                            <div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div class="col-md-4">
                <input type="text text-center" id="myInput" class="form-control primary-search" placeholder="Start typing..">
            </div>
            <form method="post">
    <div class="form-group">
        <select class="form-control" id="status" name="status" onchange="this.form.submit()">
            <option value="">Sort By:</option>
            <option value="Fully Paid">Fully Paid</option>
            <option value="Not Fully Paid">Not Fully Paid</option>
            <option value="Partially Paid">Partially Paid</option>
            <option value="Not Paid">Not Paid</option>
        </select>
    </div>
</form>


            <a href="between-dates-reports.php" class=" border-0 p-2">Reports<i class="icon-refresh"></i></a>
            <div class="d-sm-flex align-items-baseline report-summary-header">
<!-- Updated button -->
<button type="button" class="btn" onclick="generatePDF()" 
        style="display: block; margin: 0 auto; background-color: #003366; color: white; border-radius: 40px; padding: 10px 20px;">
    <i class="fas fa-download" style="font-size: 20px;"></i>
</button>
                </button>
            </div>
        </div>
    </div>
</div>

                                            
                                            <div id="pdf-content" class="table-responsive border rounded p-1">
                                            <table class="table table-striped">
                                            <h5 class="text text-center" style="background-color: yellow; color : #003366; padding: 10px; "> Students Fees Details </h5>
                                            <h5 class="text text-center" style="text-transform: uppercase; color : black;"><?php echo $academicYearMessage; ?></h5>
                                                <span class="ml-auto text-primary"></span>
                                                
                                                
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
        <tr style="background-color: #003366; color :white; padding: 10px; text-transform : uppercase; ">
            <th class="font-weight-bold">#</th>
            <th class="font-weight-bold">Name</th>
            <th class="font-weight-bold">Class</th>
            <th class="font-weight-bold">Gender</th>
            <th class="font-weight-bold">Paid</th>
            <th class="font-weight-bold">Balance</th>
            <th class="font-weight-bold">Date Paid</th>
            <th class="font-weight-bold">Status</th>
        </tr>
    </thead>
    <tbody id="myTable">
    <?php
    // Default query without filtering by status
    $sql = "SELECT * FROM tblaccounts WHERE term = :ActiveTerm AND academic_year = :AcademicYear ORDER BY class ASC";
    $query = $dbh->prepare($sql);

    // Bind parameters
    $query->bindParam(':ActiveTerm', $activeTerm, PDO::PARAM_INT);
    $query->bindParam(':AcademicYear', $academicYear, PDO::PARAM_STR);

 // Initialize the SQL query
$sql = "SELECT * FROM tblaccounts WHERE term = :ActiveTerm AND academic_year = :AcademicYear";

// Check if form is submitted and status is selected
if (isset($_POST['status']) && !empty($_POST['status'])) {
    $status = $_POST['status'];
    if ($status == 'Not Fully Paid') {
        // Include both 'Not Paid' and 'Partially Paid' statuses
        $sql .= " AND (status = 'Not Paid' OR status = 'Partially Paid')";
    } else {
        $sql .= " AND status = :status"; // Append condition to filter by status
    }
}

// Prepare and execute the SQL query
$query = $dbh->prepare($sql);

// Bind parameters
$query->bindParam(':ActiveTerm', $activeTerm, PDO::PARAM_INT);
$query->bindParam(':AcademicYear', $academicYear, PDO::PARAM_STR);

// Bind status parameter if needed
if (isset($status) && $status != 'Not Fully Paid') {
    $query->bindParam(':status', $status, PDO::PARAM_STR);
}

// Execute the query
$query->execute();

// Fetch the results
$results = $query->fetchAll(PDO::FETCH_OBJ);

    // Execute the query
    $query->execute();

    // Fetch the results
    $results = $query->fetchAll(PDO::FETCH_OBJ);
// Initialize total balance variable
$totalBalance = 0;
    $cnt = 1;
    foreach ($results as $row) {
        $totalBalance += $row->balance;
        $totalpaid += $row->paid;
        ?>
        <tr>
            <td><?php echo htmlentities($cnt); ?></td>
            <td><?php echo htmlentities($row->name); ?></td>
            <td><?php echo htmlentities($row->class); ?></td>
            <td><?php echo htmlentities($row->gender); ?></td>
            <td><?php echo htmlentities($row->paid); ?></td>
            <td><?php echo htmlentities($row->balance); ?></td>
            <td><?php echo htmlentities($row->date_paid); ?></td>
            <td><?php echo htmlentities($row->status); ?></td>
            <td>
                <div>
                    <a href="clear-balance.php?id=<?php echo htmlentities($row->stuID); ?>"><i class="icon-check"></i></a>
                </div>
            </td>
        </tr>
 
        <?php
        $cnt++;
        $totalstudents = $cnt;
    }
    ?>
           
       <tr>
    <td colspan="5" class="text-center" style="background-color: #f1f1f1; padding: 15px; font-size: 18px;">
        <strong>
            <span style="color: #003366;">Total Paid:</span> MWK <?php echo number_format($totalpaid, 2); ?>
            &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
            <span style="color: #e74c3c;">Balance:</span> MWK <?php echo number_format($totalBalance, 2); ?>
            &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
            <span style="color: #003366;">Total Students: </span><?php echo number_format($totalstudents-1); ?>
        </strong>
    </td>
</tr>
</tbody>
</table>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include_once('includes/footer.php'); ?>
            </div>
        </div>
    </div><script src="https://cdnjs.cloudflare.com/ajax/libs/dompdf/0.8.4/dompdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
    function generatePDF() {
        const content = document.getElementById('pdf-content'); // Selecting the unique div
        const options = {
            margin: 0,
            filename: '<?php echo "Accounts_" . $academicYear . "_term " . $activeTerm; ?>.pdf', // Filename based on student name, academic year, and term
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 3,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'letter',
                orientation: 'portrait'
            },
            // Exclude the button tag and its descendants from the PDF
            ignoreElements: function (element) {
                return element.className === 'btn' || element.closest('.btn') !== null;
            }
        };
        html2pdf().set(options).from(content).save(); // Generating PDF from the selected content
    }
</script>

    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="./vendors/chart.js/Chart.min.js"></script>
    <script src="./vendors/daterangepicker/daterangepicker.js"></script>
    <script src="./vendors/chartist/chartist.min.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
    <script src="./js/dashboard.js"></script>
</body>
</html>
