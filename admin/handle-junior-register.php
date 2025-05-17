<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
set_time_limit(0);
include('includes/dbconnection.php');

// Disable output buffering
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);
while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_implicit_flush(true);

// Ensure only AJAX requests are allowed
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(403);
    echo json_encode(['error' => 'Direct access not allowed']);
    exit();
}

if (strlen($_SESSION['sturecmsaid']) == 0) {
    echo json_encode(['error' => 'Session expired. Please log in again.']);
    exit();
}

// Fetch active academic year and term
$sql = "SELECT AcademicYear, ActiveTerm FROM tblcalendar WHERE Status = 'Active'";
$query = $dbh->prepare($sql);
$query->execute();
$academicYearData = $query->fetch(PDO::FETCH_ASSOC);

if (!$academicYearData) {
    echo json_encode(['error' => 'Active academic year not found.']);
    exit();
}

$academicYear = $academicYearData['AcademicYear'];
$activeTerm = $academicYearData['ActiveTerm'];

// Fetch students in classes 1 and 2
$sql = "SELECT * FROM tblstudent WHERE StudentClass IN (1, 2) AND Section IN ('A', 'B', 'C')";
$query = $dbh->prepare($sql);
$query->execute();
$students = $query->fetchAll(PDO::FETCH_ASSOC);

if (!$students) {
    echo json_encode(['error' => 'No students found to register.']);
    exit();
}

// Fetch all subjects
$sql = "SELECT ID, Name AS subname FROM tblsubject";
$subjectQuery = $dbh->prepare($sql);
$subjectQuery->execute();
$subjects = $subjectQuery->fetchAll(PDO::FETCH_ASSOC);

// Start a transaction
$dbh->beginTransaction();

$successCount = 0;
$errorCount = 0;
$totalStudents = count($students);
$totalSubjects = count($subjects);

try {
    foreach ($students as $studentIndex => $student) {
        $id = $student['ID'];
        $stuname = $student['StudentName'];
        $stuclass = $student['StudentClass'];
        $section = $student['Section'];

        foreach ($subjects as $subjectIndex => $subject) {
            $subjectID = $subject['ID'];
            $subname = $subject['subname'];

            if ($section == 'A' && strpos($subname, 'Bible Knowledge') !== false) {
                continue;
            } elseif (($section == 'B' || $section == 'C') && strpos($subname, 'Computer Studies') === 0) {
                continue;
            }

            $sqlCheckSubject = "SELECT * FROM tblstudentreg WHERE stuID = :id AND academic_year = :academicYear AND term = :activeTerm AND subID = :subjectID";
            $queryCheckSubject = $dbh->prepare($sqlCheckSubject);
            $queryCheckSubject->bindParam(':id', $id);
            $queryCheckSubject->bindParam(':academicYear', $academicYear);
            $queryCheckSubject->bindParam(':activeTerm', $activeTerm);
            $queryCheckSubject->bindParam(':subjectID', $subjectID);
            $queryCheckSubject->execute();

            if (!$queryCheckSubject->fetch(PDO::FETCH_ASSOC)) {
                $sqlInsertSubject = "INSERT INTO tblstudentreg (academic_year, term, stuID, class, subID, subname, final_grade, test1, test2, reg_date, grade, remark, sign, status) 
                VALUES (:academicYear, :activeTerm, :id, :class, :subjectID, :subname, 'NA', 'NA', 'NA', NOW(), '', '', '', 'In Progress')";
                $queryInsertSubject = $dbh->prepare($sqlInsertSubject);
                $queryInsertSubject->bindParam(':academicYear', $academicYear);
                $queryInsertSubject->bindParam(':activeTerm', $activeTerm);
                $queryInsertSubject->bindParam(':id', $id);
                $queryInsertSubject->bindParam(':class', $stuclass);
                $queryInsertSubject->bindParam(':subjectID', $subjectID);
                $queryInsertSubject->bindParam(':subname', $subname);

                if ($queryInsertSubject->execute()) {
                    $successCount++;
                } else {
                    $errorCount++;
                    throw new Exception("Database insertion failed.");
                }
            }

            $overallProgress = (($studentIndex * $totalSubjects) + ($subjectIndex + 1)) / ($totalStudents * $totalSubjects) * 100;
            echo json_encode([
                'success' => true,
                'message' => "Registering: $stuname, Subject: $subname (" . ($subjectIndex + 1) . "/$totalSubjects)",
                'overallProgress' => $overallProgress
            ]) . "\n";
            ob_flush();
            flush();
        }

        $sqlUpdateStatus = "UPDATE tblstudent SET Reg_status = 'Yes' WHERE ID = :id";
        $queryUpdateStatus = $dbh->prepare($sqlUpdateStatus);
        $queryUpdateStatus->bindParam(':id', $id);
        if (!$queryUpdateStatus->execute()) {
            $errorCount++;
            throw new Exception("Database update failed.");
        }
    }

    $dbh->commit();

    // ✅ Log admin success
    $adminId = $_SESSION['sturecmsaid'] ?? 0;
    $username = $_SESSION['username'] ?? 'unknown';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
    $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $logSql = "INSERT INTO admin_logs (admin_id, username, action, action_details, ip_address, user_agent, request_method, request_url, status, affected_table, affected_id)
        VALUES (:admin_id, :username, :action, :action_details, :ip_address, :user_agent, :request_method, :request_url, :status, :affected_table, :affected_id)";
    $logStmt = $dbh->prepare($logSql);
    $logStmt->execute([
        ':admin_id' => $adminId,
        ':username' => $username,
        ':action' => 'Bulk Student Registration',
        ':action_details' => "Registered students in classes 1 and 2 for Academic Year $academicYear, Term $activeTerm. Success: $successCount, Errors: $errorCount.",
        ':ip_address' => $ip,
        ':user_agent' => $agent,
        ':request_method' => $method,
        ':request_url' => $url,
        ':status' => 'SUCCESS',
        ':affected_table' => 'tblstudentreg',
        ':affected_id' => null
    ]);

    echo json_encode([
        'success' => true,
        'message' => "All students have been registered for {$academicYear} term {$activeTerm}.",
        'successCount' => $successCount,
        'errorCount' => $errorCount
    ]);
    exit();
} catch (Exception $e) {
    $dbh->rollBack();

    // ❗ Log admin failure
    $adminId = $_SESSION['sturecmsaid'] ?? 0;
    $username = $_SESSION['username'] ?? 'unknown';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
    $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $logSql = "INSERT INTO admin_logs (admin_id, username, action, action_details, ip_address, user_agent, request_method, request_url, status, affected_table, affected_id)
        VALUES (:admin_id, :username, :action, :action_details, :ip_address, :user_agent, :request_method, :request_url, :status, :affected_table, :affected_id)";
    $logStmt = $dbh->prepare($logSql);
    $logStmt->execute([
        ':admin_id' => $adminId,
        ':username' => $username,
        ':action' => 'Bulk Student Registration (Failure)',
        ':action_details' => "Transaction rolled back. Error: " . $e->getMessage(),
        ':ip_address' => $ip,
        ':user_agent' => $agent,
        ':request_method' => $method,
        ':request_url' => $url,
        ':status' => 'FAILURE',
        ':affected_table' => 'tblstudentreg',
        ':affected_id' => null
    ]);

    echo json_encode([
        'success' => false,
        'message' => "Transaction rolled back due to error: " . $e->getMessage(),
        'successCount' => $successCount,
        'errorCount' => $errorCount
    ]);
    exit();
}
?>
