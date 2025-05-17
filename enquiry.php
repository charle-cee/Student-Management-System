<?php
error_reporting(0);
include('includes/dbconnection.php');

// Fetch staff details based on the selected staff member (eid)
$eid = $_GET['eid'];
$stmt = $dbh->prepare("SELECT Name, Image, Role1 FROM tbladmin WHERE ID = :eid");
$stmt->bindParam(':eid', $eid, PDO::PARAM_INT);
$stmt->execute();
$staffDetails = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['send'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $mobilenumber = $_POST['mobnum'];
    $message = $_POST['message'];
    $eid = $_GET['eid'];

    // Insert enquiry into the database
    $query = $dbh->prepare("INSERT INTO tblenquiry (Admid, FullName, Email, MobileNumber, Message, EnquiryDate, Status) VALUES (:eid, :fullname, :email, :mobilenumber, :message, NOW(), 'Not Read')");
    $query->bindParam(':eid', $eid, PDO::PARAM_INT);
    $query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobilenumber', $mobilenumber, PDO::PARAM_STR);
    $query->bindParam(':message', $message, PDO::PARAM_STR);

    if ($query->execute()) {
        echo '<script>alert("Thank you for your message. Your enquiry has been successfully sent.");</script>';
        echo "<script>window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>Student Management System |  Enquiry</title>
    <link rel="icon" type="image/jpg" href="logo.jpg">
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel='stylesheet' type='text/css' media="all">
    <style>
  
        .staff-details {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 30px;
        }
        .staff-details img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #007bff;
        }
        .staff-details h4 {
            margin-top: 15px;
            font-size: 1.5rem;
            color: #333;
        }
        .staff-details p {
            color: #666;
            font-size: 1rem;
        }
        .contact-form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .contact-form .form-control {
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }
        .contact-form .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
        .contact-form textarea {
            resize: none;
        }
        .contact-form .btn {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            transition: background 0.3s ease;
        }
        .contact-form .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php include_once('includes/header.php'); ?>

  <br> <br>
   
    <!-- //Breadcrumb -->

    <!-- Contact Section -->
    <section class="contact py-lg-4 py-md-3 py-sm-3 py-3">
        <div class="container py-lg-5 py-md-4 py-sm-4 py-3">
            <h3 class="title text-center mb-lg-5 mb-md-4 mb-sm-4 mb-3">Enquiry From</h3>
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <!-- Staff Details -->
                    <div class="staff-details">
                        <img src="admin/images/<?php echo htmlspecialchars($staffDetails['Image']); ?>" alt="<?php echo htmlspecialchars($staffDetails['Name']); ?>">
                        <h4><?php echo htmlspecialchars($staffDetails['Name']); ?></h4>
                        <p><?php echo htmlspecialchars($staffDetails['Role1']); ?></p>
                    </div>

                    <!-- Inquiry Form -->
                    <div class="contact-form">
                        <form action="#" method="post">
                            <div class="form-group">
                                <input class="form-control" type="text" name="fullname" placeholder="Your Name" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="email" name="email" placeholder="Your Email" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" name="mobnum" placeholder="Mobile Number" maxlength="10" pattern="[0-9]+" required>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="message" placeholder="Your Message" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-block" name="send">Send Enquiry</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--//contact -->

    <?php include_once('includes/footer.php'); ?>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>