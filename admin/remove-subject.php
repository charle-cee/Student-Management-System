<?php
session_start();
error_reporting(0);
include 'includes/dbconnection.php';

// Redirect user to logout if not logged in
if (empty($_SESSION['sturecmsaid'])) {
    header('location:logout.php');
    exit();
}

// Check if a removal request is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $year = $_POST['year'];
    $term = $_POST['term'];
    $id = $_POST['stuID'];
    
    // Check if we're removing a specific subject or doing full de-registration
    if (isset($_POST['subID'])) {
        // Specific subject removal logic
        $subjectIDToRemove = $_POST['subID'];
        
        // Fetch subject subtype to check if it's mandatory
        $sqlSubType = "SELECT subtype FROM tblsubject WHERE ID = :subjectIDToRemove";
        $querySubType = $dbh->prepare($sqlSubType);
        $querySubType->bindParam(':subjectIDToRemove', $subjectIDToRemove, PDO::PARAM_INT);
        $querySubType->execute();
        $subtype = $querySubType->fetchColumn();

        // If the subject subtype is mandatory, return with an error
        if ($subtype === "Mandatory") {
            $errorMessage = urlencode("You cannot remove a mandatory subject.");
            header("location: term-register.php?id=" . urlencode($id) . "&error=" . $errorMessage);
            exit();
        }

        // Remove the specific subject
        $sqlRemoveSubject = "DELETE FROM tblstudentreg WHERE stuID = :id AND subID = :subjectIDToRemove AND academic_year = :year AND term = :term";
        $queryRemoveSubject = $dbh->prepare($sqlRemoveSubject);
        $queryRemoveSubject->bindParam(':id', $id, PDO::PARAM_INT);
        $queryRemoveSubject->bindParam(':subjectIDToRemove', $subjectIDToRemove, PDO::PARAM_INT);
        $queryRemoveSubject->bindParam(':year', $year, PDO::PARAM_STR);
        $queryRemoveSubject->bindParam(':term', $term, PDO::PARAM_INT);
        
        if ($queryRemoveSubject->execute()) {
            $successMessage = urlencode("Subject has been removed successfully.");
            header("location: term-register.php?id=" . urlencode($id) . "&success=" . $successMessage);
            exit();
        } else {
            $errorMessage = urlencode("Failed to remove subject.");
            header("location: term-register.php?id=" . urlencode($id) . "&error=" . $errorMessage);
            exit();
        }
    } else {
        // Full de-registration logic
$dbh->beginTransaction();

try {
    // 1. Remove all registered subjects for this student/term/year
    $sqlRemoveAll = "DELETE FROM tblstudentreg WHERE stuID = :id AND academic_year = :year AND term = :term";
    $queryRemoveAll = $dbh->prepare($sqlRemoveAll);
    $queryRemoveAll->bindParam(':id', $id, PDO::PARAM_INT);
    $queryRemoveAll->bindParam(':year', $year, PDO::PARAM_STR);
    $queryRemoveAll->bindParam(':term', $term, PDO::PARAM_INT);
    $queryRemoveAll->execute();

    // 2. Remove all results for this student/term/year
    $sqlRemove = "DELETE FROM tblresults WHERE stuID = :id AND academic_year = :year AND term = :term";
    $queryRemove = $dbh->prepare($sqlRemove);
    $queryRemove->bindParam(':id', $id, PDO::PARAM_INT);
    $queryRemove->bindParam(':year', $year, PDO::PARAM_STR);
    $queryRemove->bindParam(':term', $term, PDO::PARAM_INT);
    $queryRemove->execute();

    // 3. Update student registration status
    $sql8 = "UPDATE tblstudent SET Reg_status = 'No' WHERE ID = :id";
    $query8 = $dbh->prepare($sql8);
    $query8->bindParam(':id', $id, PDO::PARAM_INT);
    $query8->execute();

    // Commit all changes if everything succeeds
    $dbh->commit();

    $successMessage = urlencode("Student has been completely de-registered for this term.");
    header("location: term-register.php?id=" . urlencode($id) . "&success=" . $successMessage);
    exit();
    
} catch (PDOException $e) {
    // Roll back if any operation fails
    $dbh->rollBack();
    
    $errorMessage = urlencode("Failed to de-register student: " . $e->getMessage());
    header("location: term-register.php?id=" . urlencode($id) . "&error=" . $errorMessage);
    exit();
}   }
}
?>