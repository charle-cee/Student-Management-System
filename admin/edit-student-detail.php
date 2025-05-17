<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
}

if (isset($_GET['editid'])) {
    $id = $_GET['editid'];

    $sql = "SELECT * FROM tblstudent WHERE ID = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($query->rowCount() > 0) {
        $stuname = $result['StudentName'];
        $current_image = $result['Image'];

        if (isset($_POST['submit'])) {
            // Collect updated values
            $stuname = $_POST['stuname'];
            $stuclass = $_POST['stuclass'];
            $section = $_POST['section'];
            $gender = $_POST['gender'];
            $dob = $_POST['dob'];
            $fname = $_POST['fname'];
            $connum = $_POST['connum'];
            $altconnum = $_POST['altconnum'];
            $address = $_POST['address'];
            $status = $_POST['status'];

            // Handle image upload
            if (!empty($_FILES["image"]["name"])) {
                $image = $_FILES["image"]["name"];
                $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                $allowed_extensions = array("jpg", "jpeg", "png", "gif");

                if (!in_array($extension, $allowed_extensions)) {
                    header('location:edit-student-detail.php?editid=' . urlencode($id) . '&error=' . urlencode('Picture has Invalid format. Only jpg / jpeg/ png /gif allowed.'));
                    exit();
                }

                $image = md5($image) . time() . "." . $extension;
                move_uploaded_file($_FILES["image"]["tmp_name"], "images/" . $image);
            } else {
                $image = $current_image;
            }

            // Update student record
            $sql = "UPDATE tblstudent SET StudentName = :stuname, StudentClass = :stuclass, Section = :section, Gender = :gender, DOB = :dob, FatherName = :fname, ContactNumber = :connum, AltenateNumber = :altconnum, Address = :address, Image = :image, status = :status WHERE ID = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':stuname', $stuname, PDO::PARAM_STR);
            $query->bindParam(':stuclass', $stuclass, PDO::PARAM_STR);
            $query->bindParam(':section', $section, PDO::PARAM_STR);
            $query->bindParam(':gender', $gender, PDO::PARAM_STR);
            $query->bindParam(':dob', $dob, PDO::PARAM_STR);
            $query->bindParam(':fname', $fname, PDO::PARAM_STR);
            $query->bindParam(':connum', $connum, PDO::PARAM_STR);
            $query->bindParam(':altconnum', $altconnum, PDO::PARAM_STR);
            $query->bindParam(':address', $address, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':image', $image, PDO::PARAM_STR);
            $query->bindParam(':id', $id, PDO::PARAM_INT);

            $success = $query->execute();

            // Admin Log
            $adminId = $_SESSION['sturecmsaid'];
            $adminUsername = $_SESSION['username'] ?? 'Unknown';
            $action = 'Edited Student Record';
            $actionDetails = "Edited Student ID: $id - Name: $stuname";
            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $requestUrl = $_SERVER['REQUEST_URI'];
            $statusLog = $success ? 'SUCCESS' : 'FAILED';

            $log = "INSERT INTO admin_logs (admin_id, username, action, action_details, ip_address, user_agent, request_method, request_url, timestamp, status, affected_table, affected_id)
                    VALUES (:admin_id, :username, :action, :action_details, :ip, :user_agent, :request_method, :request_url, NOW(), :status, 'tblstudent', :affected_id)";
            $logQuery = $dbh->prepare($log);
            $logQuery->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
            $logQuery->bindParam(':username', $adminUsername, PDO::PARAM_STR);
            $logQuery->bindParam(':action', $action, PDO::PARAM_STR);
            $logQuery->bindParam(':action_details', $actionDetails, PDO::PARAM_STR);
            $logQuery->bindParam(':ip', $ip, PDO::PARAM_STR);
            $logQuery->bindParam(':user_agent', $userAgent, PDO::PARAM_STR);
            $logQuery->bindParam(':request_method', $requestMethod, PDO::PARAM_STR);
            $logQuery->bindParam(':request_url', $requestUrl, PDO::PARAM_STR);
            $logQuery->bindParam(':status', $statusLog, PDO::PARAM_STR);
            $logQuery->bindParam(':affected_id', $id, PDO::PARAM_INT);
            $logQuery->execute();

            if ($success) {
                header('location: edit-student-detail.php?editid=' . urlencode($id) . '&success=' . urlencode("Records for $stuname updated successfully."));
            } else {
                header('location: edit-student-detail.php?editid=' . urlencode($id) . '&error=' . urlencode("Failed to update student record."));
            }
            exit();
        }
    } else {
        header('location: edit-student-detail.php?editid=' . urlencode($id) . '&error=' . urlencode("No student record found."));
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Student Management System || Edit Students</title>
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container-scroller">
    <?php include_once('includes/header.php');?>
    <div class="container-fluid page-body-wrapper">
        <?php include_once('includes/sidebar.php');?>
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="page-header">
                    <h3 class="page-title"> Edit Students </h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Edit Students</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title" style="text-align: center; background : #003366; color : white; padding : 10px;">Edit Students</h4>
                                
                                <form class="forms-sample" method="post" enctype="multipart/form-data">
    <!-- Row for Student Name and Class -->
    <div class="form-group d-flex justify-content-between">
        <div class="col-md-5 pr-2">
            <label for="stuname">Student Name</label>
            <input type="text" name="stuname" value="<?php echo $stuname; ?>" class="form-control" required>
        </div>
        <div class="col-md-5 pl-2">
            <label for="stuclass">Student Class</label>
            <select name="stuclass" class="form-control" required>
                <option selected disabled>Select Class</option>
                <option value="1" <?php if ($stuclass == '1') echo "selected"; ?>>1</option>
                <option value="2" <?php if ($stuclass == '2') echo "selected"; ?>>2</option>
                <option value="3" <?php if ($stuclass == '3') echo "selected"; ?>>3</option>
                <option value="4" <?php if ($stuclass == '4') echo "selected"; ?>>4</option>
            </select>
        </div>
    </div>

    <!-- Row for Section and Gender -->
    <div class="form-group d-flex justify-content-between">
        <div class="col-md-5 pr-2">
            <label for="section">Class Section</label>
            <select name="section" class="form-control" required>
                <option selected disabled>Select Section</option>
                <option value="A" <?php if ($section == 'A') echo "selected"; ?>>A</option>
                <option value="B" <?php if ($section == 'B') echo "selected"; ?>>B</option>
            </select>
        </div>
        <div class="col-md-5 pl-2">
            <label for="gender">Gender</label>
            <select name="gender" class="form-control" required>
                <option selected disabled>Choose Gender</option>
                <option value="Male" <?php if ($gender == "Male") echo "selected"; ?>>Male</option>
                <option value="Female" <?php if ($gender == "Female") echo "selected"; ?>>Female</option>
            </select>
        </div>
    </div>
    <div class="form-group d-flex justify-content-between">
        <div class="col-md-5 pr-2">
            <label for="section">Date of Birth</label>
             <input type="date" name="dob" value="<?php echo $dob; ?>" class="form-control" required>
        </div>
        <div class="col-md-5 pl-2">
            <label for="gender">Status</label>
            <select name="status" class="form-control" required>
            <option value="Active" <?php if ($status == "Active") echo "selected"; ?>>Active</option>
            <option value="Inactive" <?php if ($status == "Inactive") echo "selected"; ?>>Inactive</option>
            <option value="Expelled" <?php if ($status == "Expelled") echo "selected"; ?>>Expelled</option>
            <option value="Suspended" <?php if ($status == "Suspended") echo "selected"; ?>>Suspended</option>
            <option value="Withdrawn" <?php if ($status == "Withdrawn") echo "selected"; ?>>Withdrawn</option>
            <option value="Graduated" <?php if ($status == "Graduated") echo "selected"; ?>>Graduated</option>
        </select>
        </div>
    </div>

    <!-- Student Photo -->
    <div class="form-group">
        <label for="image">Student Photo</label>
        <input type="file" name="image" value="" class="form-control">
    </div>

    
   <!-- Parents/Guardian's Details Section -->
<div class="form-section mb-4">
    <h3 class="text-center font-weight-bold mb-3" style="color : #003366;">Parents/Guardian's Details</h3>
    <hr class="mb-4" style="border-top: 2px solid #003366;">
</div>

    
    <!-- Father's Name and Contact Number -->
    <div class="form-group d-flex justify-content-between">
        <div class="col-md-5 pr-2">
            <label for="fname">Father's Name</label>
            <input type="text" name="fname" value="<?php echo $fname; ?>" class="form-control" required>
        </div>
        <div class="col-md-5 pl-2">
            <label for="connum">Contact Number</label>
            <input type="text" name="connum" value="<?php echo $connum; ?>" class="form-control" required maxlength="14" pattern="[+0-9]{0,14}" title="Please enter a valid alternate contact number (up to 14 characters, including '+').">
        </div>
    </div>

    <!-- Alternate Contact Number and Address -->
    <div class="form-group d-flex justify-content-between">
        <div class="col-md-5 pr-2">
            <label for="altconnum">Alternate Contact Number</label>
            <input type="text" name="altconnum" value="<?php echo $altconnum; ?>" class="form-control" maxlength="14" pattern="[+0-9]{0,14}" title="Please enter a valid alternate contact number (up to 14 characters, including '+').">
        </div>
        <div class="col-md-5 pl-2">
            <label for="address">Address</label>
            <textarea name="address" class="form-control" required><?php echo $address; ?></textarea>
        </div>
    </div>

    <!-- Submit and Cancel Buttons -->
<div class="form-group d-flex justify-content-between">
    <button type="submit" class="btn"  style =" color : white; background : #003366; border-radius : 40px; "name="submit">Update</button>
    <button type="button" class="btn "style =" color : #003366; background : yellow; border-radius : 40px; " onclick="window.history.back();">Cancel</button>
</div>

</form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once('includes/footer.php');?>
        </div>
    </div>
</div>
<script src="vendors/js/vendor.bundle.base.js"></script>
<script src="vendors/select2/select2.min.js"></script>
<script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
<script src="js/off-canvas.js"></script>
<script src="js/misc.js"></script>
<script src="js/typeahead.js"></script>
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
    <?php if (isset($_GET['success']) || isset($_GET['error'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: "<?php echo isset($_GET['success']) ? 'success' : 'error'; ?>",
        title: "<?php echo isset($_GET['success']) ? 'Success!' : 'Error!'; ?>",
        text: "<?php echo isset($_GET['success']) ? $_GET['success'] : $_GET['error']; ?>",
        confirmButtonColor: '#003366'
    });
</script>
<?php endif; ?>
<script src="js/select2.js"></script>
</body>
</html>
