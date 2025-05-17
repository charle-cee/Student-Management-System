<?php
session_start();
error_reporting(1);
include('includes/dbconnection.php');
include('mail/mail_message.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();  // Make sure the script stops after redirect
} else {
    if (isset($_POST['submit'])) {
        $staffname = $_POST['staffname'];
        $gender = $_POST['gender'];
        $userType = $_POST['userType'];
        $role1 = $_POST['role1'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $adminregdate = date('Y-m-d H:i:s');
        $username = strtolower(str_replace(' ', '', $staffname)); // Generate username from staff name

        function generatePassword($length = 8) {
            // Define the character pool
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $password = '';

            // Generate random characters
            for ($i = 0; $i < $length; $i++) {
                $password .= $characters[rand(0, $charactersLength - 1)];
            }

            return $password;
        }

        // Generate an 8-character password
        $password = generatePassword(8);

        // Move uploaded image to images folder and hash the image name
        $image = $_FILES["image"]["name"];
        $extension = pathinfo($image, PATHINFO_EXTENSION);
        $hashed_image_name = md5($image) . time() . "." . $extension;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], "images/" . $hashed_image_name)) {
            // Image uploaded successfully
            $sql = "SELECT UserName, MobileNumber, Email FROM tbladmin WHERE UserName = :username AND MobileNumber = :mobile AND Email = :email";
            $query = $dbh->prepare($sql);
            $query->bindParam(':username', $username, PDO::PARAM_STR);
            $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            if ($query->rowCount() == 0) {
                // Generate a hashed password
                $hashed_password = md5($password);

                // Insert data into tbladmin table
                $sql = "INSERT INTO tbladmin (Name, UserName, UserType, Gender, Role1, MobileNumber, Email, Password, AdminRegdate, Image) 
                        VALUES (:staffname, :username, :userType, :gender, :role1, :mobile, :email, :password, :adminregdate, :image)";
                $query = $dbh->prepare($sql);
                $query->bindParam(':staffname', $staffname, PDO::PARAM_STR);
                $query->bindParam(':username', $username, PDO::PARAM_STR);
                $query->bindParam(':userType', $userType, PDO::PARAM_STR);
                $query->bindParam(':gender', $gender, PDO::PARAM_STR);
                $query->bindParam(':role1', $role1, PDO::PARAM_STR);
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
                $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                $query->bindParam(':adminregdate', $adminregdate, PDO::PARAM_STR);
                $query->bindParam(':image', $hashed_image_name, PDO::PARAM_STR);
                $query->execute();
                
                $lastInsertId = $dbh->lastInsertId();
                if ($lastInsertId > 0) {
                    // Log the action to admin_logs
                    $adminId = $_SESSION['sturecmsaid'];
                    $adminUsername = $_SESSION['username'] ?? 'unknown';
                    $action = "Registered new admin";
                    $actionDetails = "Registered admin with username: $username";
                    $ipAddress = $_SERVER['REMOTE_ADDR'];
                    $userAgent = $_SERVER['HTTP_USER_AGENT'];
                    $requestMethod = $_SERVER['REQUEST_METHOD'];
                    $requestUrl = $_SERVER['REQUEST_URI'];
                    $status = 'SUCCESS';
                    $affectedTable = 'tbladmin';
                    $affectedId = $lastInsertId;

                    // Insert into admin_logs table
                    $log_sql = "INSERT INTO admin_logs (admin_id, username, action, action_details, ip_address, user_agent, request_method, request_url, timestamp, status, affected_table, affected_id)
                                VALUES (:admin_id, :username, :action, :action_details, :ip_address, :user_agent, :request_method, :request_url, NOW(), :status, :affected_table, :affected_id)";
                    $log_query = $dbh->prepare($log_sql);
                    $log_query->bindParam(':admin_id', $adminId);
                    $log_query->bindParam(':username', $adminUsername);
                    $log_query->bindParam(':action', $action);
                    $log_query->bindParam(':action_details', $actionDetails);
                    $log_query->bindParam(':ip_address', $ipAddress);
                    $log_query->bindParam(':user_agent', $userAgent);
                    $log_query->bindParam(':request_method', $requestMethod);
                    $log_query->bindParam(':request_url', $requestUrl);
                    $log_query->bindParam(':status', $status);
                    $log_query->bindParam(':affected_table', $affectedTable);
                    $log_query->bindParam(':affected_id', $affectedId);

                    $log_query->execute();

                    // Send email and redirect to register page
                    $mailing = sendLoginCredentials($staffname, $username, $email, $password);
                    header('location:register.php?success=' . urlencode($staffname) . ' has been registered successfully. An email containing login credentials has been sent to '. urlencode($email));
                    exit();
                } else {
                    header('location:register.php?error=Something went wrong. Please try again.');
                    exit();
                }
            } else {
                // User with the same username already exists
                header('location:register.php?error=' . urlencode($staffname) . ' already exists.');
                exit();
            }
        } else {
            // Image upload failed
            header('location:register.php?error=Failed to upload image. Please try again.');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Student Management System || Add New Staff</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="css/style.css" />
    <style>
/* CSS to style the placeholder text */
::placeholder {
    color: #000000; /* Black placeholder text */
}
.btn-center {
    display: block; /* Make the button a block element */
    margin: 0 auto; /* Center the button horizontally */
}

/* CSS to style select dropdown */
select {
    appearance: none; /* Remove default dropdown arrow */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='4' viewBox='0 0 8 4'%3E%3Cpath fill='%23181824' d='M4 4L0 0h8z'/%3E%3C/svg%3E"); /* Custom dropdown arrow */
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 8px 4px;
}

/* CSS to style textarea */
textarea {
    resize: vertical; /* Allow vertical resizing */
}

    </style>
  </head>
  <body>
    <div class="container-scroller">
      <?php include_once('includes/header.php');?>
      <div class="container-fluid page-body-wrapper">
        <?php include_once('includes/sidebar.php');?>
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title"> Add New Staff </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Add New Staff</li>
                </ol>
              </nav>
            </div>
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title text-center" style="font-weight: bold; background: #003366; color:white; font-size: 24px; margin-bottom: 20px;">
    Register New Staff
</h4>

                    <?php if (isset($_GET['error'])) { ?>
                            <div class="alert alert-danger alert-dismissible text-center">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <b><?php echo $_GET['error']; ?></b>
                            </div> <br>
                        <?php } ?>
                        <?php if (isset($_GET['success'])) { ?>
                            <div class="alert alert-success alert-dismissible text-center">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <b><?php echo $_GET['success']; ?></b>
                            </div>
                        <?php } ?>
                  <form class="forms-sample" method="post" enctype="multipart/form-data" style="max-width: 800px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

    <!-- Row 1: Staff Name and Gender -->
    <div class="row" style="margin-bottom: 20px;">
        <!-- Staff Name -->
        <div class="form-group col-md-6" style="padding: 10px;">
            <input type="text" name="staffname" class="form-control" placeholder="Staff Name" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
        </div>

        <!-- Gender -->
        <div class="form-group col-md-6" style="padding: 10px;">
            <select name="gender" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
                <option selected disabled>Choose Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
    </div>

    <!-- Row 2: User Type and Role 1 -->
    <div class="row" style="margin-bottom: 20px;">
        <!-- User Type -->
        <div class="form-group col-md-6" style="padding: 10px;">
            <select name="userType" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
                <option selected disabled>Choose User Type</option>
                <option value="Head Teacher">Head Teacher</option>
                <option value="Deputy HeadTeacher">Deputy Head</option>
                <option value="Staff Member">Staff Member</option>
                <option value="Exam Officer">Exam Officer</option>
                <option value="Admin">Admin</option>
            </select>
        </div>

        <!-- Role 1 -->
        <div class="form-group col-md-6" style="padding: 10px;">
            <select name="role1" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
                <option selected disabled>Choose Role</option>
                <option value="Science HoD">Science HoD</option>
                <option value="Humanities HoD">Humanities HoD</option>
                <option value="Language HoD">Language HoD</option>
                <option value="Form Teacher">Form Teacher</option>
                <option value="Exam Officer">Exam Officer</option>
            </select>
        </div>
    </div>

    <!-- Row 3: Email and Mobile -->
    <div class="row" style="margin-bottom: 20px;">
        <!-- Email -->
        <div class="form-group col-md-6" style="padding: 10px;">
            <input type="email" name="email" class="form-control" placeholder="Email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
        </div>

        <!-- Mobile -->
        <div class="form-group col-md-6" style="padding: 10px;">
            <input type="tel" name="mobile" class="form-control" placeholder="Mobile Number (e.g., 08899938833)" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;" pattern="^\+?\d+$" title="Please enter a valid mobile number (e.g., +2658899938833or 08899)">
        </div>
    </div>

    <!-- Row 4: Password and Profile Picture -->
    <div class="row" style="margin-bottom: 20px;">
        <!-- Password
        <div class="form-group col-md-6" style="padding: 10px;">
            <input type="password" name="password" class="form-control" placeholder="Password" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
        </div>

        <!-- Profile Picture -->
        <div class="form-group col-md-12" style="padding: 10px;">
            <input type="file" name="image" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
        </div>
    </div>

    <!-- Buttons -->
    <div class="form-group text-center" style="margin-top: 20px;">
        <button type="submit" class="btn btn-primary btn-lg" name="submit" style="background-color: #003366; color: white; border-radius: 40px; padding: 10px 30px; font-size: 16px; margin-right: 10px;">Register</button>
        <a href="javascript:history.back()" class="btn btn-secondary btn-lg" style="background-color: yellow; border-radius: 40px; color: #003366; padding: 10px 30px; font-size: 16px;">Cancel</a>
    </div>
</form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <?php include_once('includes/footer.php');?>
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <script>
        // Auto-hide alerts after 6 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 150); // Remove after fade out
            });
        }, 6000);
    </script>
    <!-- Plugin js for this page -->
    <script src="vendors/select2/select2.min.js"></script>
    <script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>
    <!-- End custom js for this page -->
  </body>
</html>
