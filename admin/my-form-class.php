<?php
session_start();
error_reporting(E_ALL); // Add this line to display all errors
ini_set('display_errors', 1); // Add this line to display all errors
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
} else {
    // Check database connection
    if (!$dbh) {
        echo "Failed to connect to the database.";
        exit();
    }

    try {
        if (isset($_GET['class'])) {
            $class = $_GET['class'];
            // Split the class name and section
            $classParts = explode(' ', $class);
            $className = $classParts[0];
            $section = $classParts[1];
        }    

        // Select students based on class name and section
        $sql = "SELECT DISTINCT s.ID, s.StudentName, s.StudentClass, s.Section, s.Gender,s.DOB, s.FatherName, s.ContactNumber, s.Address
                FROM tblstudentreg sr 
                JOIN tblstudent s ON sr.stuID = s.ID 
                WHERE s.StudentClass = :className AND s.Section = :section ORDER BY s.StudentName ASC";

        $query = $dbh->prepare($sql);
        $query->bindParam(':className', $className, PDO::PARAM_STR);
        $query->bindParam(':section', $section, PDO::PARAM_STR);
        $query->execute();

        // Fetch records
        $students = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <title>Student Management System || Manage class</title>
   
    <!-- plugins:css -->
    <link rel="icon" type="images/ico" href="images/sms.ICO">
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
<!-- Include Font Awesome CDN (if not already included) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="./vendors/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="./vendors/chartist/chartist.min.css">
    <!-- End plugin css for this page -->
 <!-- inject:css -->
    <!-- endinject -->
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
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                            <h4 class="card-title mb-sm-0"><?php echo htmlentities($class); ?></h4>
   
                            </ol>
                            <!-- Updated button -->
<button type="button" class="btn" onclick="generatePDF()" 
        style="display: block; margin: 0 auto; background-color: #003366; color: white; border-radius: 40px; padding: 10px 20px;">
    <i class="fas fa-download" style="font-size: 20px;"></i>
</button>
<div class="row" style="text-align: center;">
    <?php
    // Assuming $student['StudentClass'] contains the class (e.g., "1A", "2B", "3C", "4D")
    // Extract the first character to determine the form
    $formNumber = substr($class, 0, 1); // Extracts "1" from "1A"

    // Define the URLs for each form
    $formUrls = [
        1 => 'form1-results.php',
        2 => 'form2-results.php',
        3 => 'form3-results.php',
        4 => 'form4-results.php',
    ];

    // Check if the form number is valid (1, 2, 3, or 4)
    if (array_key_exists($formNumber, $formUrls)) {
        $formUrl = $formUrls[$formNumber];
        echo '<a href="' . $formUrl . '" style="background-color: #003366; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block; text-align: center; transition: background-color 0.3s ease;">
                View Exam Results for Form ' . $formNumber . '
              </a>';
    } else {
        // Handle invalid form numbers (optional)
        echo '<p>Invalid class: ' . htmlentities($studentClass) . '</p>';
    }
    ?>
</div>
                        </nav><!-- Statistics -->
<?php 
    // Count students, males, and females
    $totalStudents = count($students);
    $maleCount = 0;
    $femaleCount = 0;
    $approveStatusNoCount = 0; // Count of students with Approve_status = 'No'
    
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
                        <div class="col-md-4 offset-md-4 text-center"> <!-- Centered div within a 4-column grid -->
                            <input type="text" id="myInput" class="form-control primary-search" placeholder="Start typing..">
                        </div>
                        <div class="col-md-12 grid-margin stretch-card">
                            
                            <div  id="pdf-content" style="font-size: 8px;"class="card">
                                <div class="card-body">
                                    <div class="d-sm-flex align-items-center mb-4">
                                    <div class="table-responsive border rounded p-1">
                                        <table class="table table-striped">
                                            <thead>
                                               <tr style="background-color: #003366; color: white; padding: 10px; text-transform: uppercase;">
                                                <th class="font-weight-bold" style="padding: 10px;">Student #</th>
                                                <th class="font-weight-bold" style="padding: 10px;">Name</th>
                                                <th class="font-weight-bold" style="padding: 10px;">Date OF Birth</th>
                                                <th class="font-weight-bold" style="padding: 10px;">Action</th>
                                            </tr>

                                            </thead>
                                            <tbody id="myTable">
                                                <?php
                                                // Display student details
                                                $cnt = 1;
                                                foreach ($students as $student) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlentities($student['ID']); ?></td>
                                                        <td><?php echo htmlentities($student['StudentName']); ?></td>
                                                        <td><?php echo htmlentities($student['DOB']); ?></td>
                                                        <td>
    <?php
    // Construct the URL dynamically
    $url = 'previous-results2.php?user=known&id=' . urlencode($student['ID']) . '&name=' . urlencode($student['StudentName']) . '&class=' . urlencode($student['StudentClass'] . ' ' . $student['Section']) . '&gen=' . urlencode($student['Gender']);
    ?>
    <a href="<?php echo $url; ?>" class="btn" style="background-color: yellow; color: #003366; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center;">
        <i class="icon-eye" style="margin-right: 5px;"></i> View
    </a>
</td>

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
    function generatePDF() {
        const content = document.getElementById('pdf-content'); // Selecting the unique div
        const options = {
            margin: 2,
            filename: '<?php echo $class ?>.pdf', // Filename based on student name, academic year, and term
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 4,
                letterRendering: true
            },
            jsPDF: {
                unit: 'mm',
                format: 'a3',
                orientation: 'portrait', // Keep orientation as portrait
                putOnlyUsedFonts: true,
                floatPrecision: 16 // Increase precision for better positioning
            },
            // Exclude the button tag and its descendants from the PDF
            ignoreElements: function (element) {
                return element.className === 'btn' || element.closest('.btn') !== null;
            },
            // Adjust font size to fit all content on one page
            // and add page numbers or headers
            onAfterRender: function (pdf) {
                pdf.internal.scaleFactor = 1.0; // Adjust scale factor to fit content
                pdf.setFontSize(1); // Reduce font size

                // Add page numbers or headers
                const totalPages = pdf.internal.pages.length;
                for (let i = 1; i <= totalPages; i++) {
                    pdf.setPage(i);
                    const pageSize = pdf.internal.pageSize;
                    const pageWidth = pageSize.getWidth();
                    const pageHeight = pageSize.getHeight();
                    pdf.text('Page ' + i + ' of ' + totalPages, pageWidth - 1, pageHeight - 0.5, { align: 'right' });
                }
            }
        };
        html2pdf().set(options).from(content).save(); // Generating PDF from the selected content
    }
</script>
    <script src="vendors/js/vendor.bundle.base.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompdf/0.8.4/dompdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
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
    <!-- Custom js for this page -->
    <script src="./js/dashboard.js"></script>
    <!-- End custom js for this page -->
</body>

</html>
