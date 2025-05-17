<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['login'])) {
    // Fetch form data
    $stuID = trim($_POST['stuID']); 
    $dob = trim($_POST['dob']);
    $exam = trim($_POST['exam']);

    // Check if the required fields are filled
    if (empty($stuID) || empty($dob) || empty($exam)) {
        header("Location: results.php?error=All%20fields%20are%20required!");
        exit();
    }

    try {
        // Prepare the query to fetch student details based on ID and DOB
        $sql = "SELECT StudentName, StudentClass, Gender 
                FROM tblstudent 
                WHERE ID = :stuID AND DOB = :dob";
        $query = $dbh->prepare($sql);
        $query->bindParam(':stuID', $stuID, PDO::PARAM_STR);
        $query->bindParam(':dob', $dob, PDO::PARAM_STR);
        $query->execute();

        // Fetch the result
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $name = $result['StudentName'];
            $class = $result['StudentClass'];
            $gen = $result['Gender'];

            // Redirect based on exam type
            if ($exam === "end_term") {
                header("Location: admin/view-performance2.php?user=unknown&id=" . urlencode($stuID) . "&name=" . urlencode($name) . "&class=" . urlencode($class) . "&gen=" . urlencode($gen));
                exit;
            }
        } else {
            // Redirect to login page with an error message if no match found
            header("Location: results.php?error=Invalid%20Student%20Credentials!");
            exit();
        }
    } catch (PDOException $e) {
        // Handle any errors related to the database connection or query
        header("Location: results.php?error=Database%20error!");
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
    <title>Student Management System || Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- Custom CSS -->
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #003366, #3498db);
            font-family: 'Arial', sans-serif;
        }

        .auth-form-light {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .brand-logo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #003366;
        }

        .brand-logo h6 {
            color: #003366;
            font-weight: bold;
            margin-top: 10px;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-group label {
            position: absolute;
            top: -10px;
            left: 10px;
            background: #fff;
            padding: 0 5px;
            font-size: 14px;
            color: #003366;
            font-weight: bold;
            transition: 0.3s;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #003366;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 10px rgba(52, 152, 219, 0.5);
            outline: none;
        }

        .form-group input[type="date"] {
            padding-top: 12px;
        }

        .btn-submit {
            width: 100%;
            background-color: #003366;
            color: #fff;
            border: none;
            border-radius: 40px;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #3498db;
        }

        .alert {
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
    </style>
</head>
<body>
<!-- partial:partials/_navbar.html -->
        <?php include_once('includes/header.php'); ?>
    <div class="auth-form-light">
        <div class="brand-logo">
            <img src="images/logo.jpg" alt="logo">
            <h6>View Student Results</h6>
        </div>
        <form class="pt-3" id="login" method="post" name="login">
            <!-- Error/Success Messages -->
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger alert-dismissible text-center">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <b><?php echo $_GET['error']; ?></b>
                </div>
            <?php } ?>
            <?php if (isset($_GET['success'])) { ?>
                <div class="alert alert-success alert-dismissible text-center">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <b><?php echo $_GET['success']; ?></b>
                </div>
            <?php } ?>

            <!-- Student Number -->
            <div class="form-group">
                <input type="text" class="form-control" id="stuID" name="stuID" required>
                <label for="stuID">Student Number</label>
            </div>

            <!-- Date of Birth -->
            <div class="form-group">
                <input type="date" class="form-control" id="dob" name="dob" required>
                <label for="dob">Date of Birth</label>
            </div>

            <!-- Hidden Exam Type -->
            <div class="form-group">
                <input type="hidden" id="exam" name="exam" value="end_term" required>
            </div>

            <!-- Submit Button -->
            <div class="mt-3">
                <button class="btn-submit" name="login" type="submit">View</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</body>
</html>