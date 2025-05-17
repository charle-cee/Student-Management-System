<?php
session_start();
error_reporting(E_ALL); // Add this line to display all errors
ini_set('display_errors', 1); // Add this line to display all errors
include('includes/dbconnection.php');

// Fetch active academic year
$sql = "SELECT AcademicYear, ActiveTerm FROM tblcalendar WHERE Status = 'Active'";
$query = $dbh->prepare($sql);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $academicYear = $result['AcademicYear'];
    $term = $result['ActiveTerm'];
}

// Select student's performance details
$sql = "SELECT DISTINCT stuID FROM tblstudentreg WHERE academic_year = :academicYear AND term = :term";
$query = $dbh->prepare($sql);
$query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
$query->bindParam(':term', $term, PDO::PARAM_STR);
$query->execute();
$students = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($students as $student) {
    $stuID = $student['stuID'];
    
    // Calculate the final score
    $sql = "SELECT * FROM tblstudentreg WHERE stuID = :stuID AND academic_year = :academicYear AND term = :term";
    $query = $dbh->prepare($sql);
    $query->bindParam(':stuID', $stuID, PDO::PARAM_STR);
    $query->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
    $query->bindParam(':term', $term, PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $finalScore = $row['final_grade'];
        $stuclass = $row['class'];
        
$cnt = 1;
if (preg_match('/^[34]/', $stuclass))  {
    // Display total points only if class is 3 or 4

    // Prepare and execute the query to calculate total points using the 6 lowest grades
    $sql3 = "SELECT SUM(grade) AS total_points 
            FROM (
                SELECT grade 
                FROM tblstudentreg 
                WHERE stuID = :stuID 
                    AND academic_year = :academicYear 
                    AND term = :term 
                ORDER BY 
                    CASE 
                        WHEN subname = 'English' THEN 0 -- Put English first
                        ELSE 1 -- Put other subjects after English
                    END,
                    grade ASC
                LIMIT 6
            ) AS lowest_grades";
    $query3 = $dbh->prepare($sql3);
    $query3->bindParam(':stuID', $stuID, PDO::PARAM_STR); // Corrected binding
    $query3->bindParam(':academicYear', $academicYear, PDO::PARAM_STR); // Corrected binding
    $query3->bindParam(':term', $term, PDO::PARAM_STR); // Corrected binding
    $query3->execute();

    // Fetch the result
    $totalPointsResult = $query3->fetch(PDO::FETCH_ASSOC);
    $totalPoints = $totalPointsResult['total_points'];

    // Prepare and execute the query to check the grade of English
    $sqlEnglishGrade = "SELECT grade FROM tblstudentreg WHERE academic_year = :academicYear 
                        AND term = :term AND stuID = :stuID AND subname = 'English'";
    $queryEnglishGrade = $dbh->prepare($sqlEnglishGrade);
    $queryEnglishGrade->bindParam(':stuID', $stuID, PDO::PARAM_STR); // Corrected binding
    $queryEnglishGrade->bindParam(':academicYear', $academicYear, PDO::PARAM_STR); // Corrected binding
    $queryEnglishGrade->bindParam(':term', $term, PDO::PARAM_STR); // Corrected binding
    $queryEnglishGrade->execute();

    // Fetch the English grade
    $englishGrade = $queryEnglishGrade->fetch(PDO::FETCH_ASSOC)['grade'];

    // Initialize $result variable
    $result = '';

    // Check if the English grade is 9
    if ($englishGrade == 9) {
        $result = 'Failed!';
    } else {
        // Check if the total points fall within the passing range
        $result = ($totalPoints >= 6 && $totalPoints < 46) ? 'Passed!' : 'Failed!';
    }

    // Insert data into tblresults
    $sqlInsert = "INSERT INTO tblresults (stuID, name, class, gender, academic_year, term, sub1, score1, grade1, sub2, score2, grade2, sub3, score3, grade3, sub4, score4, grade4, sub5, score5, grade5, sub6, score6, grade6) 
                VALUES (:stuID, :name, :class, :gender, :academicYear, :term, :sub1, :score1, :grade1, :sub2, :score2, :grade2, :sub3, :score3, :grade3, :sub4, :score4, :grade4, :sub5, :score5, :grade5, :sub6, :score6, :grade6)";
    $queryInsert = $dbh->prepare($sqlInsert);
    $queryInsert->bindParam(':stuID', $stuID, PDO::PARAM_INT);
    $queryInsert->bindParam(':name', $name, PDO::PARAM_STR);
    $queryInsert->bindParam(':class', $class, PDO::PARAM_STR);
    $queryInsert->bindParam(':gender', $gender, PDO::PARAM_STR);
    $queryInsert->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
    $queryInsert->bindParam(':term', $term, PDO::PARAM_STR);
    // Bind subject and grade values accordingly
    // ...
    $queryInsert->execute();
} else {
    // Prepare and execute the query to calculate total points using the 6 highest final grades, including English
    $sql3 = "SELECT SUM(final_grade) AS total_points 
            FROM (
                SELECT final_grade 
                FROM tblstudentreg 
                WHERE stuID = :stuID 
                    AND academic_year = :academicYear 
                    AND term = :term 
                ORDER BY 
                    CASE 
                        WHEN subname = 'English' THEN 0 -- Put English first
                        ELSE 1 -- Put other subjects after English
                    END,
                    final_grade DESC -- Sort final grades in descending order to get the best grades
                LIMIT 6
            ) AS highest_grades";
    $query3 = $dbh->prepare($sql3);
    $query3->bindParam(':stuID', $stuID, PDO::PARAM_STR); // Corrected binding
    $query3->bindParam(':academicYear', $academicYear, PDO::PARAM_STR); // Corrected binding
    $query3->bindParam(':term', $term, PDO::PARAM_STR); // Corrected binding
    $query3->execute();

    // Fetch the result
    $totalPointsResult = $query3->fetch(PDO::FETCH_ASSOC);
    $totalPoints = $totalPointsResult['total_points'];

    // Prepare and execute the query to check the grade of English
    $sqlEnglishGrade = "SELECT remark FROM tblstudentreg WHERE academic_year = :academicYear 
                        AND term = :term AND stuID = :stuID AND subname = 'English'";
    $queryEnglishGrade = $dbh->prepare($sqlEnglishGrade);
    $queryEnglishGrade->bindParam(':stuID', $stuID, PDO::PARAM_STR); // Corrected binding
    $queryEnglishGrade->bindParam(':academicYear', $academicYear, PDO::PARAM_STR); // Corrected binding
    $queryEnglishGrade->bindParam(':term', $term, PDO::PARAM_STR); // Corrected binding
    $queryEnglishGrade->execute();

    // Fetch the English grade
    $englishGrade = $queryEnglishGrade->fetch(PDO::FETCH_ASSOC)['remark'];

    // Initialize $result variable
    $result = '';

    // Check if the English grade is Fail
    if ($englishGrade == "Fail") {
        $result = 'Failed!';
    } else {
        $result = 'Passed!';
    }

    // Insert data into tblresults
    $sqlInsert = "INSERT INTO tblresults (stuID, name, class, gender, academic_year, term, sub1, score1, grade1, sub2, score2, grade2, sub3, score3, grade3, sub4, score4, grade4, sub5, score5, grade5, sub6, score6, grade6) 
                VALUES (:stuID, :name, :class, :gender, :academicYear, :term, :sub1, :score1, :grade1, :sub2, :score2, :grade2, :sub3, :score3, :grade3, :sub4, :score4, :grade4, :sub5, :score5, :grade5, :sub6, :score6, :grade6)";
    $queryInsert = $dbh->prepare($sqlInsert);
    $queryInsert->bindParam(':stuID', $stuID, PDO::PARAM_INT);
    $queryInsert->bindParam(':name', $name, PDO::PARAM_STR);
    $queryInsert->bindParam(':class', $class, PDO::PARAM_STR);
    $queryInsert->bindParam(':gender', $gender, PDO::PARAM_STR);
    $queryInsert->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
    $queryInsert->bindParam(':term', $term, PDO::PARAM_STR);
    // Bind subject and grade values accordingly
    // ...
    $queryInsert->execute();
}
    }
}
?>
