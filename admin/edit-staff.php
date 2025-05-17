
<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Fetch data from the database based on the provided ID
        $sql = "SELECT * FROM tbladmin WHERE ID = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Retrieve data from the database and assign to variables
            $staffname = $result['Name'];
            $gender = $result['Gender'];
            $userType = $result['UserType'];
            $role1 = $result['Role1'];
            $email = $result['Email'];
            $mobile = $result['MobileNumber'];
            $existingImage = $result['Image'];  // Save the existing image path
        } else {
            // No record found with the provided ID
            header('location:edit-staff.php?error=No record found for the provided ID.');
            exit();
        }

        if (isset($_POST['submit'])) {
            $staffname = $_POST['staffname'];
            $username = strtolower(str_replace(' ', '', $staffname)); // Generate username from staff name
            $gender = $_POST['gender'];
            $userType = $_POST['userType'];
            $role1 = $_POST['role1'];
            $email = $_POST['email'];
            $mobile = $_POST['mobile'];

            // Check if a new image was uploaded
            if ($_FILES['image']['name']) {
                // Process new image
                $image = $_FILES["image"]["name"];
                $extension = pathinfo($image, PATHINFO_EXTENSION);
                $hashed_image_name = md5($image) . time() . "." . $extension;
                move_uploaded_file($_FILES["image"]["tmp_name"], "images/" . $hashed_image_name);
                $imageToUpdate = $hashed_image_name;
            } else {
                // Keep the existing image if no new image is uploaded
                $imageToUpdate = $existingImage;
            }

            // Update data in tbladmin table
            $sql = "UPDATE tbladmin SET Name=:staffname, UserName=:username, UserType=:userType, Gender=:gender, Role1=:role1, MobileNumber=:mobile, Email=:email, Image=:image WHERE ID=:id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':staffname', $staffname, PDO::PARAM_STR);
            $query->bindParam(':username', $username, PDO::PARAM_STR);
            $query->bindParam(':userType', $userType, PDO::PARAM_STR);
            $query->bindParam(':gender', $gender, PDO::PARAM_STR);
            $query->bindParam(':role1', $role1, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
            $query->bindParam(':image', $imageToUpdate, PDO::PARAM_STR);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();

            $affected_rows = $query->rowCount();
            if ($affected_rows > 0) {
                header('location:edit-staff.php?id=' . urlencode($id) . '&success=' . urlencode($staffname) . ' details have been updated successfully.');
                exit();
            } else {
                header('location:edit-staff.php?id=' . urlencode($id) . '&error=Failed to update ' . urlencode($staffname) . ' details. Please try again.');
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
    <title>Student Management System || Edit Staff Details</title>
    <link rel="icon" type="image/jpg" href="logo.jpg">
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
              <h3 class="page-title"> Edit Staff Details </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page"> Edit Staff Details</li>
                </ol>
              </nav>
            </div>
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title" style="text-align: center;">Edit Staff Details</h4>
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
                   <form class="forms-sample" method="post" enctype="multipart/form-data">
                   
    <!-- Staff Name and Gender aligned -->
    <div class="form-group row">
        <div class="col-md-6">
            <input type="text" name="staffname" class="form-control" placeholder="Staff Name" value="<?php echo $staffname; ?>" required>
        </div>
        <div class="col-md-6">
            <select name="gender" class="form-control" required>
                <option selected disabled>Choose Gender</option>
                <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
            </select>
        </div>
    </div>

    <!-- User Type and Role1 aligned -->
    <div class="form-group row">
        <div class="col-md-6">
 <select name="userType" class="form-control" required>
            <option value="" disabled <?php if (empty($userType)) echo 'selected'; ?>>Choose User Type</option>
            <option value="Staff Member" <?php echo ($userType == 'Staff Member') ? 'selected' : ''; ?>>Staff Member</option>
            <option value="Exam Officer" <?php echo ($userType == 'Exam Officer') ? 'selected' : ''; ?>>Exam Officer</option>
            <option value="Head Teacher" <?php echo ($userType == 'Head Teacher') ? 'selected' : ''; ?>>Head Teacher</option>
            <option value="Deputy HeadTeacher" <?php echo ($userType == 'Deputy HeadTeacher') ? 'selected' : ''; ?>>Deputy Head Teacher</option>
             <option value="Admin" <?php echo ($userType == 'Admin') ? 'selected' : ''; ?>>Admin</option>
        </select>
        
        </div>
        <div class="col-md-6">
            <select name="role1" class="form-control" required>
                <option selected disabled>Choose Role 1</option>
                <option value="Science HoD" <?php if ($role1 == 'Science HoD') echo 'selected'; ?>>Science HoD</option>
                <option value="Humanities HoD" <?php if ($role1 == 'Humanities HoD') echo 'selected'; ?>>Humanities HoD</option>
                <option value="Language HoD" <?php if ($role1 == 'Language HoD') echo 'selected'; ?>>Language HoD</option>
                <option value="Form Teacher" <?php if ($role1 == 'Form Teacher') echo 'selected'; ?>>Form Teacher</option>
                <option value="Exam Officer" <?php echo ($userType == 'Exam Officer') ? 'selected' : ''; ?>>Exam Officer</option>
            </select>
        </div>
    </div>

    <!-- Email and Mobile aligned -->
    <div class="form-group row">
        <div class="col-md-6">
            <input type="email" name="email" class="form-control" placeholder="Email" value="<?php echo $email; ?>" required>
        </div>
        <div class="col-md-6">
            <input type="tel" name="mobile" class="form-control" placeholder="Mobile Number" value="<?php echo $mobile; ?>" required>
        </div>
    </div>
     <div class="form-group row">
       <div class="form-group col-md-12">
            <input type="file" name="image" class="form-control">
        </div> 
        </div>
   <!-- Action Buttons: Update and Cancel -->
<div class="form-group">
    <!-- Update Button with custom styles -->
    <button type="submit" class="btn" style="background-color: #003366; color: white; border-radius: 40px;" name="submit">Update</button>
    
    <!-- Cancel Button with custom styles -->
    <a href="javascript:history.back()" class="btn" style="background-color: yellow; color: #003366; border-radius: 40px; text-decoration: none;">Cancel</a>
</div>

</form>
                  </div>
                </div>
              </div>
              <!-- content-wrapper ends -->
          <?php include_once('includes/footer.php');?>
            </div>
          </div>
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="vendors/select2/select2.min.js"></script>
    <script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
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
    <!-- Custom js for this page -->
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>
    <!-- End custom js for this page -->
  </body>
</html>
