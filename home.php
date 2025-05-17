<?php
include('includes/dbconnection.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <link rel="icon" type="image/jpg" href="logo.jpg">
  <title>Student Management System</title>
  <meta content="" name="descriptison">
  <meta content="" name="keywords">

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
  <style>
    /* Basic styles for slider */
    .category-slider {
        display: none; /* Hide all categories initially */
        margin-bottom: 20px;
    }
    .category-slider.active {
        display: block; /* Show only the active category */
    }
    .slider {
        position: relative;
        width: 100%;
        overflow: hidden; /* Hide images outside the slider */
        border: 2px solid #003366; /* Border color */
        border-radius: 8px; /* Rounded corners */
        background-color: #f1f1f1; /* Background color */
        margin-top: 80px; /* Push the slider down from the top */
    }
    .slider img {
        display: none; /* Hide all images initially */
        width: 100%; /* Make images responsive */
        height: auto; /* Maintain aspect ratio */
    }
    .slider img.active {
        display: block; /* Show only active image */
    }
    .category-header {
        font-size: 24px;
        color: #007BFF; /* Heading color */
        margin-bottom: 10px;
    }
    .no-images {
        font-size: 20px;
        color: #ff0000; /* Red color for no images message */
    }
</style>
</head>

<body>

<?php include_once('includes/header.php');?>
<br><br><br>
  <!-- ======= Hero Section ======= -->
<section id="hero" class="d-flex justify-content-center align-items-center" style="background-color: #003366;">
  <div class="container position-relative" style="padding: 30px; background-color: yellow;"> 
    <div class="category-slider active">
      <h2 class="category-header" style="color: black;">Students</h2>
      <div class="slider">
          <img src="images/image1.jpg" alt="Administration Image 1" class="active">
          <img src="images/image2.jpg" alt="Administration Image 2">
          <img src="images/image3.jpg" alt="Administration Image 3">
          <img src="images/image0.jpg" alt="Administration Image 3">
      </div>
    </div>

    <div class="category-slider">
        <h2 class="category-header" style="color: black;">Graduation</h2>
        <div class="slider">
            <img src="images/image4.jpg" alt="Classes Image 1" class="active">
            <img src="images/image6.jpg" alt="Classes Image 2">
            <img src="images/image7.jpg" alt="Classes Image 3">
        </div>
    </div>

    <div class="category-slider">
        <h2 class="category-header" style="color: black;">Sports</h2>
        <div class="slider">
            <img src="images/image5.jpg" alt="Classes Image 1" class="active">
            <img src="images/image8.jpg" alt="Classes Image 2">
        </div>
    </div>
  </div>
</section><!-- End Hero -->


  <main id="main">

  <!-- ======= About Section ======= -->
<section id="about" class="about">
  <div class="container" data-aos="fade-up">

    <div class="section-title">
      <h2>Our Mission</h2>
      <p>Our mission is to educate Malawian woman</p>
    </div>

    <div class="row">
      <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-left" data-aos-delay="100">
        <img src="images/image0.jpg" style="height:400px;" class="img-fluid" alt="">
      </div>
      <div class="col-lg-6 pt-4 pt-lg-0 order-2 order-lg-1 content">
        <p>
          Our school is committed to providing quality education that fosters academic excellence and personal growth. Our mission extends beyond traditional classroom learning to empower students with the skills, knowledge, and values they need to succeed in an ever-changing world. We strive to prepare our students not only to excel in national exams but also to become responsible global citizens who contribute positively to society.
        </p>
        <a href="#" class="learn-more-btn">Read More</a>
      </div>
    </div>

  </div>
</section>


    <!-- ======= Counts Section ======= -->
    <section id="counts" class="counts section-bg" >
      <div class="container">

        <div class="row counters">

          <div class="col-lg-3 col-6 text-center">
            <?php             
    $sql1 ="SELECT * from  tblstudent";
    $query1 = $dbh -> prepare($sql1);
    $query1->execute();
    $results1=$query1->fetchAll(PDO::FETCH_OBJ);
    $totalstudents=$query1->rowCount();
    ?>
            <span data-toggle="counter-up">
            <?php echo htmlentities($totalstudents); ?></span>
            <p>Students</p>
          </div>

          <div class="col-lg-3 col-6 text-center">
          <?php             
    $sql2 ="SELECT * from  tbladmin";
    $query2 = $dbh -> prepare($sql2);
    $query2->execute();
    $results2=$query2->fetchAll(PDO::FETCH_OBJ);
    $totalstaff=$query2->rowCount();
    ?>
            <span data-toggle="counter-up"><?php echo htmlentities($totalstaff); ?></span>
            <p>Staff Members</p>
          </div>

          <div class="col-lg-3 col-6 text-center">
          <?php             
    $sql3 ="SELECT * from  tblclass";
    $query3 = $dbh -> prepare($sql3);
    $query3->execute();
    $results3=$query3->fetchAll(PDO::FETCH_OBJ);
    $totalclass=$query3->rowCount();
    ?>
            <span data-toggle="counter-up"><?php echo htmlentities($totalclass); ?></span>
            <p>Classes</p>
          </div>

          <div class="col-lg-3 col-6 text-center">
          <?php $sql4 ="SELECT * from  tblpublicnotice";
    $query4 = $dbh -> prepare($sql4);
    $query4->execute();
    $results4=$query4->fetchAll(PDO::FETCH_OBJ);
    $totalnotice=$query4->rowCount();
    ?>
            <span data-toggle="counter-up"><?php echo htmlentities($totalnotice); ?></span>
            <p>Announcements</p>
          </div>

        </div>

      </div>
    </section>
<!-- ======= Why Us Section ======= -->
<section id="why-us" class="why-us">
  <div class="container" data-aos="fade-up">

    <div class="row">
      <div class="col-lg-4 d-flex align-items-stretch">
        <div class="content">
          <h3>Why Choose Us?</h3>
          <p>
            We stand out as a premier secondary school committed to academic excellence and holistic development. Here are some compelling reasons why you should choose our institution:
          </p>
          <ul>
            <li><strong>Academic Excellence:</strong> Our rigorous curriculum and dedicated faculty ensure that students receive a top-notch education, preparing them for success in higher education and beyond.</li>
            <li><strong>Qualified and Caring Faculty:</strong> Our team of experienced educators are not only experts in their fields but also deeply committed to nurturing the intellectual, emotional, and social growth of each student.</li>
            <li><strong>Holistic Development:</strong> We believe in educating the whole child, fostering creativity, critical thinking, leadership skills, and character development alongside academic achievement.</li>
          </ul>
          <div class="text-center">
            <a href="about.html" class="more-btn">Learn More <i class="bx bx-chevron-right"></i></a>
          </div>
        </div>
      </div>
      <div class="col-lg-8 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
        <div class="icon-boxes d-flex flex-column justify-content-center">
          <div class="row">
            <div class="col-xl-4 d-flex align-items-stretch" style="background-color: #003366; color: yellow;">
              <div class="icon-box mt-4 mt-xl-0">
                <i class="bx bx-receipt"></i>
                <h4>Comprehensive Curriculum</h4>
                <p>Our curriculum is designed to meet the diverse needs of learners, offering a wide array of subjects, electives, and enrichment opportunities.</p>
              </div>
            </div>
            <div class="col-xl-4 d-flex align-items-stretch" style="background-color: #003366; color: yellow;">
              <div class="icon-box mt-4 mt-xl-0">
                <i class="bx bx-cube-alt"></i>
                <h4>Supportive Community</h4>
                <p>We foster a supportive and inclusive community where students feel respected, valued, and empowered to thrive academically and personally.</p>
              </div>
            </div>
            <div class="col-xl-4 d-flex align-items-stretch"style="background-color: #003366; color: yellow;">
              <div class="icon-box mt-4 mt-xl-0">
                <i class="bx bx-images"></i>
                <h4>State-of-the-Art Facilities</h4>
                <p>Our modern campus features cutting-edge facilities, technology-enabled classrooms, libraries, laboratories, and recreational spaces to enhance the learning experience.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
<section id="popular-courses" class="courses">
  <div class="container" data-aos="fade-up">

    <div class="section-title">
      <h2>Subjects</h2>
      <p>Subjects offered</p>
    </div>

    <div class="row" data-aos="zoom-in" data-aos-delay="100">

      <!-- Science -->
      <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
        <div class="course-item" style="background-color: #003366; color: yellow;">
          <i class="bx bxl-react "></i>
          <div class="course-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4>Science</h4>
              <p class="price">Form 1-4</p>
            </div>
            <?php $sql5 ="SELECT * from  tblstudentreg WHERE subname='Agriculture' OR subname='Chemistry' OR subname='Computer Studies' OR subname='Business Studies' OR subname='Physics' OR subname='Mathematics'";
    $query5 = $dbh -> prepare($sql5);
    $query5->execute();
    $results5=$query5->fetchAll(PDO::FETCH_OBJ);
    $totalcounts=$query5->rowCount();
    ?>
          
            <h3 style="background-color: #003366; color: yellow;">Agriculture, Computer Studies, Chemistry, Physics, Mathematics</h3>
            <p>Science subjects cover a range of disciplines including agriculture, computer studies, chemistry, physics, and mathematics.</p>
            <div class="trainer d-flex justify-content-between align-items-center">
              <div class="trainer-profile d-flex align-items-center">
                <img src="assets/img/trainers/trainer-1.jpg" class="img-fluid" alt="">
                <span>Students</span>
              </div>
              <div class="trainer-rank d-flex align-items-center">
                <i class="bx bx-user text-primary"></i>&nbsp;<?php
// Calculate the value and round it
$value = round($totalcounts);

// Check if the value is above 1000
if ($value >= 1000) {
    // Format the value to display in 'k' format with one decimal place
    $formatted_value = number_format(($value / 1000), 1) . 'K';
} else {
    // Keep the value as is
    $formatted_value = $value;
}
?>

<?php echo htmlentities($formatted_value); ?>
                &nbsp;&nbsp;
                <i class="bx bx-heart text-danger"></i>&nbsp;<?php
// Calculate the value and round it
$value = round($totalcounts * 0.75);

// Check if the value is above 1000
if ($value >= 1000) {
    // Format the value to display in 'k' format with one decimal place
    $formatted_value = number_format(($value / 1000), 1) . 'K';
} else {
    // Keep the value as is
    $formatted_value = $value;
}
?>

<?php echo htmlentities($formatted_value); ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Humanities -->
      <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
        <div class="course-item"style="background-color: #003366; color: yellow;">
          <i class="bx bx-book-reader"style="background-color: #003366; color: yellow;"></i>
          <div class="course-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4>Humanities</h4>
              <p class="price">Form 1-4</p>
            </div> 
            <h3 style="background-color: #003366; color: yellow;">History, Social/Life Skills, Geography, Bible Knowledge</h3>
            <p>Humanities subjects delve into subjects such as history, geography, social studies, bible knowledge, and life skills.</p>
             <br><div class="trainer d-flex justify-content-between align-items-center">
              <div class="trainer-profile d-flex align-items-center">
                <img src="assets/img/trainers/trainer-1.jpg" class="img-fluid" alt="">
                <span>Students</span>
              </div> <?php $sql6 ="SELECT * from  tblstudentreg WHERE subname='History' OR subname='Geography' OR subname='Bible Knowledge' OR subname='Social/Life Skills'";
    $query6 = $dbh -> prepare($sql6);
    $query6->execute();
    $results6=$query6->fetchAll(PDO::FETCH_OBJ);
    $totalcounts2=$query6->rowCount();
    ?>
              <div class="trainer-rank d-flex align-items-center">
                <i class="bx bx-user style="background-color: #003366; color: yellow;></i>&nbsp;<?php
// Calculate the value and round it
$value = round($totalcounts2);

// Check if the value is above 1000
if ($value >= 1000) {
    // Format the value to display in 'k' format with one decimal place
    $formatted_value = number_format(($value / 1000), 1) . 'K';
} else {
    // Keep the value as is
    $formatted_value = $value;
}
?>

<?php echo htmlentities($formatted_value); ?>
                &nbsp;&nbsp;
                <i class="bx bx-heart text-danger"></i>&nbsp;<?php
// Calculate the value and round it
$value = round($totalcounts2 * 0.8);

// Check if the value is above 1000
if ($value >= 1000) {
    // Format the value to display in 'k' format with one decimal place
    $formatted_value = number_format(($value / 1000), 1) . 'K';
} else {
    // Keep the value as is
    $formatted_value = $value;
}
?>

<?php echo htmlentities($formatted_value); ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Languages -->
      <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
        <div class="course-item"style="background-color: #003366; color: yellow;">
          <i class="bx bx-globe-alt"style="background-color: #003366; color: yellow;"></i>
          <div class="course-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4>Languages</h4>
              <p class="price">Form 1-4</p>
            </div>
            <h3 style="background-color: #003366; color: yellow;">English & Chichewa</h3>
            <p>Language subjects focus on enhancing communication skills and cultural appreciation with subjects such as English and Chichewa.</p>
            <br> <br><div class="trainer d-flex justify-content-between align-items-center">
              <div class="trainer-profile d-flex align-items-center">
                <img src="assets/img/trainers/trainer-1.jpg" class="img-fluid" alt="">
                <span>Students</span>
              </div> <?php $sql7 ="SELECT * from  tblstudentreg WHERE subname='English' OR subname='Chichewa'";
    $query7 = $dbh -> prepare($sql7);
    $query7->execute();
    $results7=$query7->fetchAll(PDO::FETCH_OBJ);
    $totalcounts3=$query6->rowCount();
    ?>
              <div class="trainer-rank d-flex align-items-center">
                <i class="bx bx-user text-primary"></i>&nbsp;<?php
// Calculate the value and round it
$value = round($totalcounts3);

// Check if the value is above 1000
if ($value >= 1000) {
    // Format the value to display in 'k' format with one decimal place
    $formatted_value = number_format(($value / 1000), 1) . 'K';
} else {
    // Keep the value as is
    $formatted_value = $value;
}
?>

<?php echo htmlentities($formatted_value); ?>
                &nbsp;&nbsp;
                <i class="bx bx-heart text-danger"></i>&nbsp;<?php
// Calculate the value and round it
$value = round($totalcounts3 * 0.95);

// Check if the value is above 1000
if ($value >= 1000) {
    // Format the value to display in 'k' format with one decimal place
    $formatted_value = number_format(($value / 1000), 1) . 'K';
} else {
    // Keep the value as is
    $formatted_value = $value;
}
?>

<?php echo htmlentities($formatted_value); ?>

              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
<?php include('staff.php'); ?>
  </main><!-- End #main -->
<?php include_once('includes/footer.php'); ?>
<!-- Back to Top -->
<a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>
<div id="preloader"></div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const categorySliders = document.querySelectorAll('.category-slider');
        let currentCategoryIndex = 0; // Start with the first category
        let currentImageIndex = 0;    // Start with the first image in that category
        let currentInterval;           // Timer for image display

        // Function to show the current category
        function showCurrentCategory() {
            categorySliders.forEach((slider, index) => {
                slider.classList.toggle('active', index === currentCategoryIndex);
            });
        }

        // Function to show the current image in the current category
        function showCurrentImage() {
            const currentCategory = categorySliders[currentCategoryIndex];
            const images = currentCategory.querySelectorAll('img');
            
            // Hide all images in the current category
            images.forEach(image => image.classList.remove('active'));
            // Show the current image
            images[currentImageIndex].classList.add('active');
        }

        // Function to move to the next image or category
        function next() {
            const currentCategory = categorySliders[currentCategoryIndex];
            const images = currentCategory.querySelectorAll('img');

            // Move to the next image within the current category
            currentImageIndex++;

            // If we've reached the end of the images in this category
            if (currentImageIndex >= images.length) {
                // Reset the image index and move to the next category
                currentImageIndex = 0;
                currentCategoryIndex++;

                // If we've reached the last category, loop back to the start
                if (currentCategoryIndex >= categorySliders.length) {
                    currentCategoryIndex = 0;
                }
                // Show the new category
                showCurrentCategory();
            }

            // Show the updated image
            showCurrentImage();
        }

        // Initialize the slideshow
        showCurrentCategory();
        showCurrentImage();

        // Set interval to move to the next image every 6 seconds
        currentInterval = setInterval(next, 6000); // 6 seconds
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