<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_GET['id']) && (isset($_GET['sub1']) || isset($_GET['sub2']) || isset($_GET['sub3']))) {
        $id = $_GET['id'];
        $sub1 = $_GET['sub1'] ?? null;
        $sub2 = $_GET['sub2'] ?? null;
        $sub3 = $_GET['sub3'] ?? null;

        // Fetch data from the database based on the provided ID
        $sql = "SELECT * FROM tbladmin WHERE ID = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Retrieve data from the database and assign to variables
            $staffname = $result['Name'];
        } else {
            // No record found with the provided ID
            header('location: manage-allocation.php?error=No record found for the provided ID.');
            exit();
        }

        if (isset($_POST['submit'])) {
            // Initialize empty strings for subjects
            $subj1 = '';
            $subj2 = '';
            $subj3 = '';

            // Handle subject1
            if (!empty($_POST['subject1'])) {
                $subj1 = $sub1 . '-' . implode('-', array_map(function($item) {
                    return explode('-', $item)[1]; // Get the form number
                }, $_POST['subject1']));
            }

            // Handle subject2
            if (!empty($_POST['subject2'])) {
                $subj2 = $sub2 . '-' . implode('-', array_map(function($item) {
                    return explode('-', $item)[1]; // Get the form number
                }, $_POST['subject2']));
            }

            // Handle subject3
            if (!empty($_POST['subject3'])) {
                $subj3 = $sub3 . '-' . implode('-', array_map(function($item) {
                    return explode('-', $item)[1]; // Get the form number
                }, $_POST['subject3']));
            }

            // Prepare dynamic SQL statement
            $updates = [];
            if (!empty($subj1)) {
                $updates[] = "Major = :subj1";
            }
            if (!empty($subj2)) {
                $updates[] = "Minor = :subj2";
            }
            if (!empty($subj3)) {
                $updates[] = "Other = :subj3";
            }

            if (count($updates) > 0) {
                // Construct the SQL update statement
                $sql = "UPDATE tbladmin SET " . implode(', ', $updates) . " WHERE ID = :id";
                $query = $dbh->prepare($sql);
                $query->bindParam(':id', $id, PDO::PARAM_INT);

                // Bind parameters conditionally
                if (!empty($subj1)) {
                    $query->bindParam(':subj1', $subj1, PDO::PARAM_STR);
                }
                if (!empty($subj2)) {
                    $query->bindParam(':subj2', $subj2, PDO::PARAM_STR);
                }
                if (!empty($subj3)) {
                    $query->bindParam(':subj3', $subj3, PDO::PARAM_STR);
                }

                // Execute the update
                if ($query->execute()) {
                    header('location: manage-allocation.php?success=Subject Allocation edited successfully.');
                    exit();
                } else {
                    header('location: manage-allocation.php?error=Failed to reallocate subjects.');
                    exit();
                }
            } else {
                header('location: manage-allocation.php?error=No subjects selected to update.');
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
    <title>Student Management System || Edit Allocation</title>
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
                  <li class="breadcrumb-item active" aria-current="page"> Edit Allocation</li>
                </ol>
              </nav>
            </div>
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title" style="text-align: center; background-color: #003366; color: white; padding: 10px">Edit Subject Allocation</h4>
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
    <div class="form-group">
        <input type="text" name="staffname" class="form-control" placeholder="Staff Name" value="<?php echo $staffname; ?>" required>
    </div>

    <?php
    // Fetch existing subject names
    if (!is_null($sub1)) {
        $sql = "SELECT Name FROM tblsubject WHERE ID = '$sub1'";
        $query = $dbh->prepare($sql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $subject1 = $result->Name;
    }
    if (!is_null($sub2)) {
        $sql = "SELECT Name FROM tblsubject WHERE ID = '$sub2'";
        $query = $dbh->prepare($sql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $subject2 = $result->Name;
    }
    if (!is_null($sub3)) {
        $sql = "SELECT Name FROM tblsubject WHERE ID = '$sub3'";
        $query = $dbh->prepare($sql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $subject3 = $result->Name;
    }
    ?>
<div class="form-group">
    <select name="subject1[]" class="form-control" multiple <?php if (is_null($sub1)) echo 'disabled'; ?>>
        <option value="" disabled selected>Select Class(es) for <?php echo htmlentities($subject1); ?></option>
        <option value="<?php echo htmlentities($sub1 . '-1'); ?>">Form 1</option>
        <option value="<?php echo htmlentities($sub1 . '-2'); ?>">Form 2</option>
        <option value="<?php echo htmlentities($sub1 . '-3'); ?>">Form 3</option>
        <option value="<?php echo htmlentities($sub1 . '-4'); ?>">Form 4</option>
    </select>
</div>

<div class="form-group">
    <select name="subject2[]" class="form-control" multiple <?php if (is_null($sub2)) echo 'disabled'; ?>>
        <option value="" disabled selected>Select Class(es) for <?php echo htmlentities($subject2); ?></option>
        <option value="<?php echo htmlentities($sub2 . '-1'); ?>">Form 1</option>
        <option value="<?php echo htmlentities($sub2 . '-2'); ?>">Form 2</option>
        <option value="<?php echo htmlentities($sub2 . '-3'); ?>">Form 3</option>
        <option value="<?php echo htmlentities($sub2 . '-4'); ?>">Form 4</option>
    </select>
</div>

<div class="form-group">
    <select name="subject3[]" class="form-control" multiple <?php if (is_null($sub3)) echo 'disabled'; ?>>
        <option value="" disabled selected>Select Class(es) for <?php echo htmlentities($subject3); ?></option>
        <option value="<?php echo htmlentities($sub3 . '-1'); ?>">Form 1</option>
        <option value="<?php echo htmlentities($sub3 . '-2'); ?>">Form 2</option>
        <option value="<?php echo htmlentities($sub3 . '-3'); ?>">Form 3</option>
        <option value="<?php echo htmlentities($sub3 . '-4'); ?>">Form 4</option>
    </select>
</div>
    <!-- Buttons Section -->
        <div class="d-flex justify-content-between mt-4">
            <!-- Update Book Button -->
            <button type="submit" class="btn" name="submit" style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 40px; border: none;">
                Update
            </button>

            <!-- Cancel Button -->
            <button type="button" class="btn" style="background-color: yellow; color: #003366; padding: 10px 20px; border-radius: 40px; border: none;" onclick="window.history.back();">
                Cancel
            </button>
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
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>
    <!-- End custom js for this page -->
  </body>
</html>
