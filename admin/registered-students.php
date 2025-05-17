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
            // Redirect to accounts.php with an error
            header('location: accounts.php?error=Invalid active term');
            exit();
        }
    } else {
        // Redirect to accounts.php with an error
        header('location: accounts.php?error=Active academic year not found');
        exit();
    }

    // Define SQL query to count total students
    $sql = "SELECT COUNT(DISTINCT stuID) as totalStudents FROM tblstudentreg WHERE term = :activeTerm AND academic_year = :academicYear";

    // Prepare and execute the query
    $query = $dbh->prepare($sql);
    $query->bindParam(':activeTerm', $activeTerm, PDO::PARAM_INT);
    $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
    $query->execute();

    // Fetch the result
    $result = $query->fetch(PDO::FETCH_ASSOC);

    // Extract total count of students
    $totalStudents = $result['totalStudents'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" type="image/jpg" href="logo.jpg">
    <!-- Head content -->
</head>
<body>
    <!-- HTML content -->
    <!-- Total number of students -->
    <h5>Total number of students: <?php echo $totalStudents; ?></h5>
    <!-- Student table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Student Class</th>
                <th>Gender</th>
                <th>Reg. Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Define SQL query to fetch student details
            $sql = "SELECT StudentName, StudentClass, Section, Gender, reg_date 
                    FROM tblstudentreg 
                    WHERE term = :activeTerm AND academic_year = :academicYear";

            // Prepare and execute the query
            $query = $dbh->prepare($sql);
            $query->bindParam(':activeTerm', $activeTerm, PDO::PARAM_INT);
            $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
            $query->execute();

            // Fetch and display the results
            $cnt = 1;
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?php echo $cnt; ?></td>
                    <td><?php echo htmlentities($row['StudentName']); ?></td>
                    <td><?php echo htmlentities($row['StudentClass']); ?></td>
                    <td><?php echo htmlentities($row['Gender']); ?></td>
                    <td><?php echo htmlentities($row['reg_date']); ?></td>
                </tr>
                <?php
                $cnt++;
            }
            ?>
        </tbody>
    </table>
    <!-- End HTML content -->
</body>
</html>
