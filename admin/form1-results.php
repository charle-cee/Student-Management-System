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
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- Layout styles -->
    <link rel="stylesheet" href="css/style.css" />
    <style>
        th {
            background-color: #003366;
            color: white;
        }
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
    <?php include_once('includes/header.php'); ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <?php include_once('includes/sidebar.php'); ?>
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="page-header">
                    <h3 class="page-title"> END OF TERM <?php echo $activeTerm; ?> RESULTS </h3>
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
                    <!-- Search input -->
                    <div class="col-md-4 offset-md-4 text-center">
                        <input type="text" id="myInput" class="form-control primary-search" placeholder="Start typing..">
                    </div>

                    <!-- Table -->
                    <div id="pdf-content" class="col-md-12 grid-margin stretch-card">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="container-responsive mt-4">
                                        <div class="table-responsive border rounded p-1">
                                            <div class="text-center">
                                                <img src="images/logo.jpg" alt="Logo" style="width: 15% !important; height: 15% !important;">
                                            </div>
                                            <h6 class="text-blue text-center">FORM 1 END OF TERM RESULTS</h6>
                                            <h6 class="text-blue text-center"><?php echo strtoupper($academicYearMessage); ?></h6>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered">
                                                    <thead repeat="true">
                                                        <tr>
                                                            <th class="font-weight-bold">#</th>
                                                            <th class="font-weight-bold">NAME</th>
                                                            <th class="font-weight-bold">AGR</th>
                                                            <th class="font-weight-bold">BIO</th>
                                                            <th class="font-weight-bold">B/K</th>
                                                            <th class="font-weight-bold">CHE</th>
                                                            <th class="font-weight-bold">CHI</th>
                                                            <th class="font-weight-bold">COM</th>
                                                            <th class="font-weight-bold">ENG</th>
                                                            <th class="font-weight-bold">GEO</th>
                                                            <th class="font-weight-bold">HIS</th>
                                                            <th class="font-weight-bold">MAT</th>
                                                            <th class="font-weight-bold">PHY</th>
                                                            <th class="font-weight-bold">SOC</th>
                                                            <th class="font-weight-bold">MARKS</th>
                                                            <th class="font-weight-bold">RESULT</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="myTable">
                                                        <?php
                                                        // SQL query to retrieve distinct student names and their IDs
                                                          // SQL query to retrieve distinct student names and their IDs
                                                            // SQL query to retrieve distinct student names and their IDs
                                                            $sqlResults = "SELECT DISTINCT s.StudentName, sr.stuID, sr.position, sr.points, sr.result
                                                                        FROM tblresults AS sr
                                                                        JOIN tblstudent AS s ON sr.stuID = s.ID
                                                                        WHERE sr.class = 1
                                                                            AND sr.academic_year = :academicYear
                                                                            AND sr.term = :term
                                                                        ORDER BY COALESCE(sr.position, 9999) ASC";

                                                            // Prepare the query
                                                            $queryResults = $dbh->prepare($sqlResults);

                                                            // Bind parameters
                                                            $queryResults->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
                                                            $queryResults->bindParam(':term', $activeTerm, PDO::PARAM_STR);

                                                            // Execute the query
                                                            $queryResults->execute();

                                                            // Fetch all results
                                                            $results = $queryResults->fetchAll(PDO::FETCH_ASSOC);
                                                        // Output each student's name and calculate total scores
                                                        foreach ($results as $record) {
                                                             $position = $record['position'];
                                                            $points = $record['points'];
                                                            $result = $record['result'];
                                                            $stuID = $record['stuID'];
                                                            $fullName = $record['StudentName'];

                                                            // Split the name into parts
                                                            $nameParts = explode(' ', trim($fullName));
                                                            $lastName = array_pop($nameParts);
                                                            $firstNames = implode(' ', $nameParts);

                                                            // Rearrange and convert to uppercase
                                                            $studentName = strtoupper($lastName) . ' ' . strtoupper($firstNames);

                                                            // Define an array of subjects with their corresponding LIKE patterns
                                                            $subjects = [
                                                                'Agriculture' => '%Agricu%',
                                                                'Biology' => '%Biolo%',
                                                                'Bible Knowledge' => '%Bible%',
                                                                'Chemistry' => '%Chemis%',
                                                                'Chichewa' => '%Chichewa%',
                                                                'Computer Studies' => '%Computer%',
                                                                'English' => '%English%',
                                                                'Geography' => '%Geograph%',
                                                                'History' => '%History%',
                                                                'Mathematics' => '%Mathematics%',
                                                                'Physics' => '%Physics%',
                                                                'Social/Life Skills' => '%Social%'
                                                            ];

                                                            // Initialize an array to hold grades and test scores
                                                            $grades = [];
                                                            foreach ($subjects as $subject => $pattern) {
                                                                // Prepare the query to fetch grade and test1 score for the current subject
                                                                $sqlSubjectData = "SELECT final_grade FROM tblstudentreg 
                                                                                   WHERE stuID = :stuID 
                                                                                   AND academic_year = :academicYear 
                                                                                   AND term = :term 
                                                                                   AND class = 1
                                                                                   AND subname LIKE :subname";

                                                                $querySubject = $dbh->prepare($sqlSubjectData);
                                                                $querySubject->bindParam(':stuID', $stuID, PDO::PARAM_INT);
                                                                $querySubject->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
                                                                $querySubject->bindParam(':term', $activeTerm, PDO::PARAM_STR);
                                                                $querySubject->bindValue(':subname', $pattern, PDO::PARAM_STR);

                                                                $querySubject->execute();
                                                                $subjectData = $querySubject->fetch(PDO::FETCH_ASSOC);

                                                                $grades[$subject] = [
                                                                    'final_grade' => $subjectData['final_grade'] ?? '-',
                                                                    'test1' => $subjectData['test1'] ?? '-'
                                                                ];
                                                            }

                                                            echo "<tr>";
                                                            echo "<td>" . htmlentities($position) . "</td>";
                                                            echo "<td>" . htmlentities($studentName) . "</td>";
                                                            foreach ($grades as $subject => $subjectData) {
                                                                echo "<td>" . htmlentities($subjectData['final_grade']) . "</td>";
                                                            }

                                                            echo "<td> <b>" . htmlentities($points) . "</b></td>";
                                                            echo "<td>";

                                                            if ($result === 'PASSED') {
                                                                echo '<button class="btn" style="background-color: #003366; color: white; font-weight: bold; border: none; padding: 5px 10px; border-radius: 5px;">';
                                                                echo 'PASS ✔️';
                                                                echo '</button>';
                                                            } else {
                                                                echo '<button class="btn btn-sm" style="background-color: red; color: white; font-weight: bold; padding: 5px 10px; border-radius: 5px;">';
                                                                echo 'FAIL ❌';
                                                                echo '</button>';
                                                            }

                                                            echo "</td>";
                                                            echo "</tr>";
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
<!-- Plugin js for this page -->
<script src="vendors/select2/select2.min.js"></script>
<script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
<!-- inject:js -->
<script src="js/off-canvas.js"></script>
<script src="js/misc.js"></script>
<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<!-- Custom js for this page -->
<script src="js/typeahead.js"></script>
<script src="js/select2.js"></script>
<!-- PDF Generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompdf/0.8.4/dompdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
    function generatePDF() {
        const content = document.getElementById('pdf-content');
        const options = {
            margin: 0,
            filename: '<?php echo "Form 1 end of term result_" . $academicYear . "_term " . $activeTerm; ?>.pdf',
            image: { type: 'jpeg', quality: 1 },
            html2canvas: { scale: 2, letterRendering: true },
            jsPDF: { unit: 'mm', format: 'a3', orientation: 'landscape' },
            ignoreElements: function (element) {
                return element.className === 'btn' || element.closest('.btn') !== null;
            },
            // Add page numbers
            onAfterPageAdd: (page) => {
                const pageNumber = page.pageNumber;
                const pdf = page.pdf;
                const pageSize = pdf.internal.pageSize;
                const pageWidth = pageSize.width;
                const pageHeight = pageSize.height;

                // Add page number at the bottom center of the page
                pdf.setFontSize(10);
                pdf.text(`Page ${pageNumber}`, pageWidth / 2, pageHeight - 10, { align: 'center' });
            }
        };

        // Generate PDF
        html2pdf().set(options).from(content).save();
    }
</script>
</body>
</html>