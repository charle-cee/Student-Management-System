<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="icon" type="image/jpg" href="logo.jpg">
<title>Student Management System | Contact Us Page</title>
<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
<link href="assets/css/style22.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans|Poppins" rel="stylesheet">
<script src="assets/vendor/jquery/jquery.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<!-- Header -->
<?php include_once('includes/header.php'); ?>

<!-- Banner -->
<div class="banner banner5">
  <div class="container">
    <h2>Contact Us</h2>
  </div>
</div>

<!-- Contact Section -->
<div class="contact">
  <div class="container">
    <div class="contact-info">
    <br>
      <h3 class="text-center">Feel Free to Contact Us!</h3>
      <p class="text-center">We are here to assist you. Reach out to us anytime.</p>
    </div>

    <div class="row">
      <!-- Contact Form -->
      <div class="col-md-6">
      <!--
        <form method="POST" action="send_message.php">
          <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="message">Message:</label>
            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
          </div>
          <button type="submit" class="btn btn-warning btn-block">Send Message</button>
        </form>-->
      </div>

      <!-- Contact Details -->
      <div class="col-md-6"> <!--
        <h4>Contact Information</h4>
        <p><i class="bx bx-map"></i> Nyungwe Girls Catholic Sec. School, Namadzi, Chiradzulu District, Malawi</p>
        <p><i class="bx bx-phone"></i> Principal: +265 (0) 988 301 840</p>
        <p><i class="bx bx-phone"></i> Deputy Principal: +265 (0) 888 382 503</p>
        <p><i class="bx bx-phone"></i> Bursar: +265 (0) 988 476 486</p>
        <p><i class="bx bx-envelope"></i> Email: nyungwegirls@gmail.com</p>
        <h5>Follow Us</h5>
        <div class="social-links">
          <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
          <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
          <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
          <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
        </div> -->
      </div>
    </div>
  </div>
</div>
<?php include_once('includes/footer.php'); ?>    <!-- Back to Top -->
    
  <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>
<script src="assets/vendor/aos/aos.js"></script>
</body>
</html>
