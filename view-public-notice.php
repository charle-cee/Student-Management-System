<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// File download logic
if (isset($_GET['download']) && isset($_GET['file'])) {
    $file = basename(urldecode($_GET['file'])); // Sanitize input
    $filepath = 'admin/images/' . $file;

    // Allowed file types
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', '7z'];
    $fileExtension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        echo '<script>alert("File type not allowed."); window.history.back();</script>';
        exit;
    }

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        flush();
        readfile($filepath);
        exit;
    } else {
        echo '<script>alert("File not found in admin/images directory!"); window.history.back();</script>';
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/jpg" href="logo.jpg">
     <title>Student Management System || Home Page</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
</head>
<body>
<?php include_once('includes/header.php'); ?>

<div class="container mt-4">
    <?php
    $vid = $_GET['viewid'];
    $sql = "SELECT * FROM tblpublicnotice WHERE ID = :vid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':vid', $vid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $row) {
    ?>
    <table class="table table-bordered">
        <tr class="table-warning text-center">
            <th colspan="2">Announcement Details</th>
        </tr>
        <tr>
            <th>Date</th>
            <td><?php echo htmlspecialchars($row->CreationDate); ?></td>
        </tr>
        <tr>
            <th>Title</th>
            <td><?php echo htmlspecialchars($row->NoticeTitle); ?></td>
        </tr>
        <tr>
            <th>Message</th>
            <td><?php echo nl2br(htmlspecialchars($row->NoticeMessage)); ?></td>
        </tr>
        <?php if (!empty($row->Image)) : ?>
        <tr>
            <th>Attachment</th>
            <td>
                <?php
                $filePath = 'admin/images/' . $row->Image;
                $fileExtension = pathinfo($row->Image, PATHINFO_EXTENSION);
                $icon = 'fa-file-alt text-secondary';

                switch (strtolower($fileExtension)) {
                    case 'pdf': $icon = 'fa-file-pdf text-danger'; break;
                    case 'doc': case 'docx': $icon = 'fa-file-word text-primary'; break;
                    case 'xls': case 'xlsx': $icon = 'fa-file-excel text-success'; break;
                    case 'ppt': case 'pptx': $icon = 'fa-file-powerpoint text-warning'; break;
                    case 'jpg': case 'jpeg': case 'png': case 'gif': $icon = 'fa-file-image text-info'; break;
                    case 'zip': case 'rar': case '7z': $icon = 'fa-file-archive text-muted'; break;
                }

                $formattedSize = file_exists($filePath) ? formatSizeUnits(filesize($filePath)) : 'File not found';
                ?>
                <i class="fas <?php echo $icon; ?> me-2"></i>
                <a href="?download=1&file=<?php echo urlencode($row->Image); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-download"></i> Download Attachment
                </a>
                <div class="text-muted small">Size: <?php echo $formattedSize; ?></div>
                <div class="text-muted small">Type: <?php echo strtoupper($fileExtension); ?></div>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    <?php
        }
    } else {
        echo "<div class='alert alert-danger'>No record found.</div>";
    }

    // Format file size
    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        elseif ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        elseif ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        elseif ($bytes > 1) return $bytes . ' bytes';
        elseif ($bytes == 1) return '1 byte';
        else return '0 bytes';
    }
    ?>
</div>
	</div>
</div>
<br>
<?php include_once('includes/footer.php'); ?>
<!-- Back to Top -->
<a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>
<div id="preloader"></div>

<!--specfication-->

<!-- Optional: Add jQuery and FileSaver.js for better download handling -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
// Alternative download method using FileSaver.js
$(document).ready(function() {
    $('.download-btn').on('click', function(e) {
        e.preventDefault();
        var downloadUrl = $(this).attr('href');
        
        // Option 1: Let the server handle it (standard method)
        window.location.href = downloadUrl;
        
        /* Option 2: Using fetch API (for modern browsers)
        fetch(downloadUrl)
            .then(response => {
                if (!response.ok) throw new Error('File not found');
                return response.blob();
            })
            .then(blob => {
                var filename = new URL(downloadUrl).searchParams.get('file');
                saveAs(blob, filename);
            })
            .catch(error => {
                alert(error.message);
            });
        */
    });
});
</script>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/jquery/jquery.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/jquery.easing/jquery.easing.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/waypoints/jquery.waypoints.min.js"></script>
  <script src="assets/vendor/counterup/counterup.min.js"></script>
  <script src="assets/vendor/owl.carousel/owl.carousel.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

	</body>
</html>
