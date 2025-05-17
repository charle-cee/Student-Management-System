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
    
$subname = $_POST['subname'];
    $studentID = $_POST['student_id'];
    $academicYear = $_POST['academic_year'];
    $term = $_POST['term'];
    $subID = $_POST['sub_id'];
    $class = $_POST['class'];
    try {
        // Start a transaction
        $dbh->beginTransaction();

        $updatedTests = []; // Array to store the names of updated tests
// Check if test1 is set and validate input
if (isset($_POST['exam']) && $_POST['exam'] == 'Mid') {
    // Retrieve inputs
    $test1 = is_numeric($_POST['test1']) ? $_POST['test1'] : 0;
    

    // Calculate the final grade as a percentage
   $finalGrade = round(($test1 / 40) * 100);
    $score = $test1;
    // Assign a letter grade based on the percentage
    if ($finalGrade >= 0 && $finalGrade <= 39) {
        $grade = "F";
    } elseif ($finalGrade >= 40 && $finalGrade <= 59) {
        $grade = "D";
    } elseif ($finalGrade >= 60 && $finalGrade <= 74) {
        $grade = "C";
    } elseif ($finalGrade >= 75 && $finalGrade <= 84) {
        $grade = "B";
    } else {
        $grade = "A";
    }

    // Check if a record already exists for this student, year, term, and subject
    $sqlCheck = "SELECT ID FROM tblmid WHERE stuID = :studentID AND academic_year = :academicYear AND term = :term AND subID = :subID";
    $stmtCheck = $dbh->prepare($sqlCheck);
    $stmtCheck->bindParam(':studentID', $studentID, PDO::PARAM_INT);
    $stmtCheck->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
    $stmtCheck->bindParam(':term', $term, PDO::PARAM_STR);
    $stmtCheck->bindParam(':subID', $subID, PDO::PARAM_INT);
    $stmtCheck->execute();
    
    if ($stmtCheck->rowCount() > 0) {
        // Record exists, perform an UPDATE
        $sqlUpdate = "UPDATE tblmid 
                      SET test1 = :test1, subname = :subname, grade = :grade
                      WHERE stuID = :studentID 
                      AND academic_year = :academicYear 
                      AND term = :term 
                      AND subID = :subID";
        $stmtUpdate = $dbh->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':test1', $finalGrade, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':subname', $subname, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':grade', $grade, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':studentID', $studentID, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':term', $term, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':subID', $subID, PDO::PARAM_INT);
        $stmtUpdate->execute();
    } else {
        // No record found, perform an INSERT
        $sqlInsert = "INSERT INTO tblmid (academic_year, term, stuID, class, subID, subname, test1, grade) 
                      VALUES (:academicYear, :term, :studentID, :class, :subID, :subname, :test1, :grade)";
        $stmtInsert = $dbh->prepare($sqlInsert);
        $stmtInsert->bindParam(':academicYear', $academicYear, PDO::PARAM_STR);
        $stmtInsert->bindParam(':term', $term, PDO::PARAM_STR);
        $stmtInsert->bindParam(':studentID', $studentID, PDO::PARAM_INT);
        $stmtInsert->bindParam(':class', $class, PDO::PARAM_STR);
        $stmtInsert->bindParam(':subID', $subID, PDO::PARAM_INT);
        $stmtInsert->bindParam(':subname', $subname, PDO::PARAM_STR);
        $stmtInsert->bindParam(':test1', $finalGrade, PDO::PARAM_STR);
        $stmtInsert->bindParam(':grade', $grade, PDO::PARAM_STR);
        $stmtInsert->execute();
    }
    $sqlTest1 = "UPDATE tblstudentreg 
                         SET test1 = :test1, subname = :subname
                         WHERE stuID = :studentID 
                         AND academic_year = :academicYear 
                         AND term = :activeTerm 
                         AND subID = :sub";
            $stmtTest1 = $dbh->prepare($sqlTest1);
            $stmtTest1->bindParam(':test1', $test1, PDO::PARAM_STR);
            $stmtTest1->bindParam(':subname', $subname, PDO::PARAM_STR);
            $stmtTest1->bindParam(':studentID', $studentID, PDO::PARAM_INT);
            $stmtTest1->bindParam(':academicYear', $_POST['academic_year'], PDO::PARAM_STR);
            $stmtTest1->bindParam(':activeTerm', $_POST['term'], PDO::PARAM_INT);
            $stmtTest1->bindParam(':sub', $_POST['sub_id'], PDO::PARAM_STR);
            $stmtTest1->execute();
            $updatedTests[] = 'Mid Term'; // Add to updated tests array

    $updatedTests[] = 'Mid Term'; // Add to updated tests array
}
        // Check if test2 is set and validate input
else {
             $test1 = filter_var($_POST['test1'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
             $test2 = filter_var($_POST['test2'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

            $score = $test2;
            $subname = $_POST['subname'];
            $sqlTest2 = "UPDATE tblstudentreg 
                         SET test2 = :test2, subname = :subname
                         WHERE stuID = :studentID 
                         AND academic_year = :academicYear 
                         AND term = :activeTerm 
                         AND subID = :sub";
            $stmtTest2 = $dbh->prepare($sqlTest2);
            $stmtTest2->bindParam(':test2', $test2, PDO::PARAM_STR);
            $stmtTest2->bindParam(':subname', $subname, PDO::PARAM_STR);
            $stmtTest2->bindParam(':studentID', $studentID, PDO::PARAM_INT);
            $stmtTest2->bindParam(':academicYear', $_POST['academic_year'], PDO::PARAM_STR);
            $stmtTest2->bindParam(':activeTerm', $_POST['term'], PDO::PARAM_INT);
            $stmtTest2->bindParam(':sub', $_POST['sub_id'], PDO::PARAM_STR);
            $stmtTest2->execute();
  
            $updatedTests[] = 'End of Term'; // Add to updated tests array
           
            $finalGrade = $test1 + $test2;
            $class = $_POST['class'];
            if ($class == 3 || $class == 4) {
                //get remark and grade point
                if ($finalGrade >= 0 && $finalGrade <= 39 ){
                $grade = 9;
                $remark = "Fail";
            }
                elseif($finalGrade >= 40 && $finalGrade <= 45){
                    $grade = 8;
                    $remark = " Bare Pass";   
                }
                elseif($finalGrade >= 46 && $finalGrade <= 49){
                    $grade = 7;
                    $remark = "Pass";   
                }
                elseif($finalGrade >= 50 && $finalGrade <= 59){
                    $grade = 6;
                    $remark = " Weak Credit";   
                }
                elseif($finalGrade >= 60 && $finalGrade <= 64){
                    $grade = 5;
                    $remark = " Weak Credit";   
                }  elseif($finalGrade >= 65 && $finalGrade <= 69){
                    $grade = 4;
                    $remark = "Strong Credit";   
                }
                elseif($finalGrade >= 70 && $finalGrade <= 74){
                    $grade = 3;
                    $remark = "Strong Credit";   
                }
                elseif($finalGrade >= 75 && $finalGrade <= 79){
                    $grade = 2;
                    $remark = "Distinction";   
                }
                else{
                        $grade = 1;
                        $remark = "Distinction";   
                    
                }

            }
            else{
                if ($finalGrade >= 0 && $finalGrade <= 39 ){
                    $grade = "F";
                    $remark = "Fail";}
                    elseif($finalGrade >= 40 && $finalGrade <= 59){
                        $grade = "D";
                        $remark = "Average";   
                    }
                    elseif($finalGrade >= 60 && $finalGrade <= 74){
                        $grade = "C";
                        $remark = "Good";   
                    }
                    elseif($finalGrade >= 75 && $finalGrade <= 84){
                        $grade = "B";
                        $remark = "Very Good";   
                    }
                    else{
                        $grade = "A";
                        $remark = "Excellent!";  
                    }
                               
            $status = $finalGrade != '' ? 'Completed' : ''; // Set status to "Completed" if final grade is provided
            $sqlFinalGrade = "UPDATE tblstudentreg 
                              SET final_grade = :finalGrade, grade = :grade, remark = :remark, sign = :currentSign, status = :status 
                              WHERE stuID = :studentID 
                              AND academic_year = :academicYear 
                              AND term = :activeTerm 
                              AND subID = :sub";
            $stmtFinalGrade = $dbh->prepare($sqlFinalGrade);
            $stmtFinalGrade->bindParam(':finalGrade', $finalGrade, PDO::PARAM_STR);
            $stmtFinalGrade->bindParam(':grade', $grade, PDO::PARAM_STR);
            $stmtFinalGrade->bindParam(':remark', $remark, PDO::PARAM_STR);
            $stmtFinalGrade->bindParam(':currentSign', $currentSign, PDO::PARAM_STR);
            $stmtFinalGrade->bindParam(':status', $status, PDO::PARAM_STR);
            $stmtFinalGrade->bindParam(':studentID', $studentID, PDO::PARAM_INT);
            $stmtFinalGrade->bindParam(':academicYear', $_POST['academic_year'], PDO::PARAM_STR);
            $stmtFinalGrade->bindParam(':activeTerm', $_POST['term'], PDO::PARAM_INT);
            $stmtFinalGrade->bindParam(':sub', $_POST['sub_id'], PDO::PARAM_STR);
            $stmtFinalGrade->execute();
        }
        }


        // Commit the transaction
        $dbh->commit();

      // Generate success message based on updated tests
        $successMessage = 'You have added ' . $score . ' for ' . $_POST['name'] .' for ' . implode(', ', $updatedTests);

        // Redirect back to major1.php with success message
        header("Location: major1.php?year={$_POST['academic_year']}&term={$_POST['term']}&sub={$_POST['sub_id']}&form={$_POST['form']}&exam={$_POST['exam']}&success=" . urlencode($successMessage));
        exit();

    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $dbh->rollback();

        // Return error response and redirect back to major1.php with error message
        header("Location: major1.php?year={$_POST['academic_year']}&term={$_POST['term']}&sub={$_POST['sub_id']}&form={$_POST['form']}&exam={$_POST['exam']}&error=" . urlencode("Error updating scores: " . $e->getMessage()));
        exit();
    }
} else {
    // Return error response if any of the necessary parameters are missing
    header("Location: major1.php?year={$_POST['academic_year']}&term={$_POST['term']}&sub={$_POST['sub_id']}&form={$_POST['form']}&exam={$_POST['exam']}&error=" . urlencode("Missing parameters"));
    exit();
}
?>
