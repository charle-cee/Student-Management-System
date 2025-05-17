<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['book_number'])) {
        $bookNumber = $_POST['book_number'];

        // Update the database to mark the book as returned
        $currentDate = date('Y-m-d');
        $status = 'Available';

        // Update query to mark the book as returned
        $sql = "UPDATE tblbook SET dateReturned = :currentDate, UserID = NULL, takenBy = NULL, status = :status WHERE book_number = :bookNumber";
        $query = $dbh->prepare($sql);
        $query->bindParam(':currentDate', $currentDate, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':bookNumber', $bookNumber, PDO::PARAM_STR);

        // Execute the update query
        if ($query->execute()) {
            // Prepare the success response
            $response = [
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'class' => $_POST['class'],
                'message' => 'Selected Book has been returned successfully.',
                $sucess => $_SESSION['message']
                
            ];
        } else {
            // Prepare the error response if update fails
            $response = [
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'class' => $_POST['class'],
                'message' => 'Failed to return book selected ',
                $error => $_SESSION['message']
            ];
        }

        // Send the response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // Return error message if book number is not provided
        $response = [
            'message' => 'Book number is missing.',
            'error' => 'Book number is missing.',
            $error => $_SESSION['message']
        ];

        // Send back the response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
?>
