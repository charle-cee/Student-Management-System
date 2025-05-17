<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
}

// Fetch sign from the database
$sid = $_SESSION['sturecmsaid'];
$sql = "SELECT sign FROM tbladmin WHERE ID = :sid";
$query = $dbh->prepare($sql);
$query->bindParam(':sid', $sid, PDO::PARAM_INT);
$query->execute();
$row = $query->fetch(PDO::FETCH_ASSOC);
$currentSign = $row['sign'];

// Check if all necessary parameters are set
if (isset($_POST['student_id'], $_POST['academic_year'], $_POST['term'], $_POST['exam'], $_POST['sub_id'], $_POST['form'], $_POST['subname'])) {
    
    $studentID = $_POST['student_id'];
    $name = $_POST['name'];
    $academicYear = $_POST['academic_year'];
    $term = $_POST['term'];
    $subID = $_POST['sub_id'];
    $class = $_POST['class'];
    $test2 = filter_var($_POST['test2'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $finalGrade = $test2; // Assuming final_grade is the same as test2

    // Determine grade and remark based on finalGrade and class
    if ($class == 3 || $class == 4) {
        if ($finalGrade >= 0 && $finalGrade <= 39) {
            $grade = 9;
            $remark = "Fail";
        } elseif ($finalGrade >= 40 && $finalGrade <= 45) {
            $grade = 8;
            $remark = "Bare Pass";
        } elseif ($finalGrade >= 46 && $finalGrade <= 49) {
            $grade = 7;
            $remark = "Pass";
        } elseif ($finalGrade >= 50 && $finalGrade <= 59) {
            $grade = 6;
            $remark = "Weak Credit";
        } elseif ($finalGrade >= 60 && $finalGrade <= 64) {
            $grade = 5;
            $remark = "Weak Credit";
        } elseif ($finalGrade >= 65 && $finalGrade <= 69) {
            $grade = 4;
            $remark = "Strong Credit";
        } elseif ($finalGrade >= 70 && $finalGrade <= 74) {
            $grade = 3;
            $remark = "Strong Credit";
        } elseif ($finalGrade >= 75 && $finalGrade <= 79) {
            $grade = 2;
            $remark = "Distinction";
        } else {
            $grade = 1;
            $remark = "Distinction";
        }
    } else {
        if ($finalGrade >= 0 && $finalGrade <= 39) {
            $grade = "F";
            $remark = "Fail";
        } elseif ($finalGrade >= 40 && $finalGrade <= 59) {
            $grade = "D";
            $remark = "Average";
        } elseif ($finalGrade >= 60 && $finalGrade <= 74) {
            $grade = "C";
            $remark = "Good";
        } elseif ($finalGrade >= 75 && $finalGrade <= 84) {
            $grade = "B";
            $remark = "Very Good";
        } else {
            $grade = "A";
            $remark = "Excellent!";
        }
    }

    $status = $finalGrade != '' ? 'Completed' : ''; // Set status to "Completed" if final grade is provided

    try {
        // Start a transaction
        $dbh->beginTransaction();

        // Single query to update test2, final_grade, grade, remark, sign, and status
        $sql = "UPDATE tblstudentreg 
                SET test2 = :test2, 
                    final_grade = :finalGrade, 
                    grade = :grade, 
                    remark = :remark, 
                    sign = :currentSign, 
                    status = :status 
                WHERE stuID = :studentID 
                AND academic_year = :academicYear 
                AND term = :activeTerm 
                AND subID = :sub";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':test2', $test2, PDO::PARAM_STR);
        $stmt->bindParam(':finalGrade', $finalGrade, PDO::PARAM_STR);
        $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);
        $stmt->bindParam(':remark', $remark, PDO::PARAM_STR);
        $stmt->bindParam(':currentSign', $currentSign, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':studentID', $studentID, PDO::PARAM_INT);
        $stmt->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
        $stmt->bindParam(':activeTerm', $term, PDO::PARAM_INT);
        $stmt->bindParam(':sub', $subID, PDO::PARAM_STR);
        $stmt->execute();

        // Commit the transaction
        $dbh->commit();

        // Generate success message
        $successMessage = 'You have added ' . $test2 . ' for ' . $name;

        // Redirect back to major4.php with success message
        header("Location: major4.php?year={$academicYear}&term={$term}&sub={$subID}&form={$_POST['form']}&exam={$_POST['exam']}&success=" . urlencode($successMessage));
        exit();

    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $dbh->rollback();

        // Redirect back to major4.php with error message
        header("Location: major4.php?year={$academicYear}&term={$term}&sub={$subID}&form={$_POST['form']}&exam={$_POST['exam']}&error=" . urlencode("Error updating scores: " . $e->getMessage()));
        exit();
    }
} else {
    // Redirect back to major4.php with error message if parameters are missing
    header("Location: major4.php?year={$_POST['academic_year']}&term={$_POST['term']}&sub={$_POST['sub_id']}&form={$_POST['form']}&exam={$_POST['exam']}&error=" . urlencode("Missing parameters"));
    exit();
}
?>