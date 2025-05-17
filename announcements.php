<?php
include('includes/dbconnection.php');
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/jpg" href="logo.jpg">
<title>Student  Management System-Announcements</title>
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!--bootstrap-->
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
<!--coustom css-->
<link href="css/style.css" rel="stylesheet" type="text/css"/>
<!--script-->
<script src="js/jquery-1.11.0.min.js"></script>
<!-- js -->
<script src="js/bootstrap.js"></script>
<!-- /js -->
<!--fonts-->
<link href='//fonts.googleapis.com/css?family=Open+Sans:300,300italic,400italic,400,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
<!--/fonts-->

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link rel="shortcut icon" href="assets/img/ronk1.jpg" />
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- <link href="assets/vendor/icofont/icofont.min.css" rel="stylesheet"> -->
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/owl.carousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style22.css" rel="stylesheet">

<!--hover-girds-->
<link rel="stylesheet" type="text/css" href="css/default.css" />
<link rel="stylesheet" type="text/css" href="css/component.css" />
<script src="js/modernizr.custom.js"></script>
<!--/hover-grids-->
<script type="text/javascript" src="js/move-top.js"></script>
<script type="text/javascript" src="js/easing.js"></script>
<!--script-->
<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(".scroll").click(function(event){		
					event.preventDefault();
					$('html,body').animate({scrollTop:$(this.hash).offset().top},900);
				});
			});
</script>
<!--/script-->
</head>
	<body>
<?php include_once('includes/header.php');?>

<!-- Page Title -->
<div class="text-center">
    <h2 class="text-primary bg-light py-3">Latest Announcements</h2>
</div>

<!-- Announcement Carousel or Notice List -->
<div class="container py-5">
    <div class="row">
        <?php
        $sql="SELECT * FROM tblpublicnotice ORDER BY ID DESC LIMIT 12";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);

        $cnt=1;
        if($query->rowCount() > 0) {
            foreach($results as $row) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlentities($row->NoticeTitle); ?></h5>
                            <p class="card-text"><?php echo substr(htmlentities($row->NoticeMessage), 0, 100); ?>...</p>
                            <a href="view-public-notice.php?viewid=<?php echo htmlentities($row->ID); ?>" class="btn" style="background : #003366; color:white;">Read More</a>
                            <span class="btn" style="background : yellow; color:#003366;"><?php echo htmlentities($row->CreationDate); ?></span>
                        </div>
                    </div>
                </div>
            <?php $cnt=$cnt+1; }
        } ?>
    </div>
</div>


<!-- Footer -->
<footer id="footer" style="background : #003366; color:white;">
    <div class="footer-top" style="background : #003366; color:white;">
        <div class="container" style="background : #003366; color:white;">
            <div class="row">

                <div class="col-lg-4 col-md-6 footer-contact">
                    <h4 style="background : #003366; color:white;">Address</h4>
                    <p style="background : #003366; color:white;">
                        Nyungwe Girls Catholic Sec. School<br>
                        Private Bag 5<br>
                        Namadzi<br>
                        <strong>Email:</strong> nyungwegirls@gmail.com<br>

                    </p>
                </div>
                <div class="col-lg-4 col-md-6 footer-links">
                    <h4 style="background : #003366; color:white;">Physical Address</h4>
                    <p>
                        Namadzi, Chiradzulu District,<br>
                        Southern Region<br>
                        M3 Road, Malawi
                    </p>
                </div>

                <div class="col-lg-4 col-md-6 footer-links">
                    <h4 style="background : #003366; color:white;">Office Contacts</h4>
                    <p>
                        Principal: +265 (0) 988 301 840<br>
                        Deputy Principal: +265 (0) 888 382 503<br>
                        Bursar: +265 (0) 988 476 486<br>
                        Exam Officer +265 (0) 983 338 578
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container d-md-flex py-4">
        <div class="mr-md-auto text-center text-md-left">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> <strong>Student Management System</strong> - Powered by 
                <a href="https://charleceegraphix.great-site.net" target="_blank" style="color: yellow;">Charle Cee Graphix</a>
            </div>
        </div>
        <div class="social-links text-center text-md-right pt-3 pt-md-0">

            <a href="https://facebook.com/nyungwegirls.cathsecshool" class="facebook"><i class="bx bxl-facebook"></i></a>
            <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
            <a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
            <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
        </div>
    </div>
</footer>

    <!-- Back to Top -->
    
  <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>
  <div id="preloader"></div>


<!--specfication-->

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
