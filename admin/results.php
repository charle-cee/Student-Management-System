<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
} else {
    // Fetch active academic year and term
    $sql = "SELECT AcademicYear, ActiveTerm, Term1Start, Term1End, Term2Start, Term2End, Term3Start, Term3End FROM tblcalendar WHERE Status = 'Active'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $academicYear = $result['AcademicYear'];
        $activeTerm = $result['ActiveTerm'];

        // Check if the active term is valid
        if ($activeTerm >= 1 && $activeTerm <= 3) {
            // Determine term start and end dates
            switch ($activeTerm) {
                case 1:
                    $termStartDate = $result['Term1Start'];
                    $termEndDate = $result['Term1End'];
                    break;
                case 2:
                    $termStartDate = $result['Term2Start'];
                    $termEndDate = $result['Term2End'];
                    break;
                case 3:
                    $termStartDate = $result['Term3Start'];
                    $termEndDate = $result['Term3End'];
                    break;
            }
            // Construct academic year message
            $academicYearMessage = "$academicYear Academic year, Term $activeTerm from $termStartDate to $termEndDate";
        } else {
            // Redirect to dashboard with an error message
            header('location: dashboard.php?error=Invalid active term');
            exit();
        }
    } else {
        // Redirect to dashboard with an error message
        header('location: dashboard.php?error=Active academic year not found');
        exit();
    }
}
?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/jpg" href="logo.jpg">
     <title>Student Management System || Returning Book</title>
                <!-- plugins:css -->
                <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
                <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
                <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
                <!-- endinject -->
                <!-- Plugin css for this page -->
                <link rel="stylesheet" href="vendors/select2/select2.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                                <h3 class="page-title"> End of Term <?php echo $activeTerm; ?> results </h3>
                <!-- Updated button -->
<button type="button" class="btn" onclick="generatePDF()" 
        style="display: block; margin: 0 auto; background-color: #003366; color: white; border-radius: 40px; padding: 10px 20px;">
    <i class="fas fa-download" style="font-size: 20px;"></i>
</button>
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Performance List</li>
                        </ol>
                    </nav>
                </div>


<div class="row">
     <div class="row" style="text-align: center;">
    <a href="form1-results.php" style="background-color: #003366; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; text-align: center; transition: background-color 0.3s ease;">
        Form 1
    </a>
    <a href="form2-results.php" style="background-color: #003366; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; text-align: center; transition: background-color 0.3s ease;">
        Form 2
    </a>
    <a href="form3-results.php" style="background-color: #003366; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; text-align: center; transition: background-color 0.3s ease;">
        Form 3
    </a>
    <a href="form4-results.php" style="background-color: #003366; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; text-align: center; transition: background-color 0.3s ease;">
        Form 4
    </a>
</div>

<div class="col-md-4 offset-md-4 text-center" style="margin-top: 20px;">
    <input type="text" id="myInput" class="form-control primary-search" placeholder="Start typing..">
</div>


<!-- Table -->
                            <div id="pdf-content" class="col-md-12 grid-margin stretch-card">
    
                                <div class="col-12 grid-margin stretch-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive border rounded p-1">
                        
            
                      <table class="table table-striped">
                      <h5 class="text text-center" style= "text-transform : uppercase; ">Students Results</h5>
                      <h5 class="text text-center" style= "text-transform : uppercase;"><?php echo $academicYearMessage; ?></h5>
                  
                      <thead> 
                          <tr style= "text-transform : uppercase; background : #003366; color : white; padding : 10px;">
                              <th class="font-weight-bold">Position</th>
                              <th class="font-weight-bold">Name</th>
                              <th class="font-weight-bold">Class</th>
                              <th class="font-weight-bold">Gender</th>
                              <th class="font-weight-bold">Points / Marks</th>
                              <th class="font-weight-bold">Result</th>
                          </tr>
                      </thead>
                      <tbody id="myTable">
                          <?php
                           $sqlResults = "SELECT * FROM tblresults 
                           WHERE academic_year = :academicYear 
                           AND term = :term ORDER BY  name ASC";
           $queryResults = $dbh->prepare($sqlResults);
           $queryResults->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
           $queryResults->bindParam(':term', $activeTerm, PDO::PARAM_STR);
           $queryResults->execute();
           $results = $queryResults->fetchAll(PDO::FETCH_ASSOC);

           
                      
                  // Find the position and total positions
                  $position = 1;
                              
                          // Display student details
                          foreach ($results as $student) {
                                 
                          ?>
                              <tr>
                                  <td><?php echo htmlentities($position); ?></td>
                                  <td><?php echo htmlentities($student['name']); ?></td>
                                  <td><?php echo htmlentities($student['class']); ?></td>
                                  <td><?php echo htmlentities($student['gender']); ?></td>
                                  <td><?php echo htmlentities($student['points']); ?></td>
                                  <td><?php echo htmlentities($student['result']); ?></td>
                              </tr>
                          <?php  $position++;
                              
                              } ?>
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

                    <!-- partial -->
                    
                <!-- main-panel ends -->
                <?php include_once('includes/footer.php');?>
                    </div>
                </div>
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

                <!-- inject:js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompdf/0.8.4/dompdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
    function generatePDF() {
        const content = document.getElementById('pdf-content'); // Selecting the unique div
        const options = {
            margin: 0,
            filename: '<?php echo "Result_" . $academicYear . "_term " . $activeTerm; ?>.pdf', // Filename based on student name, academic year, and term
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


<script>
    // Change the background color on hover
    document.querySelectorAll('a').forEach(function (element) {
        element.addEventListener('mouseover', function () {
            this.style.backgroundColor = '#002147';
        });
        element.addEventListener('mouseout', function () {
            this.style.backgroundColor = '#003366';
        });
    });
</script>

            </body>
            </html>
         