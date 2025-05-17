
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <title>Charle Cee Graphix</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <style>
    /* Custom CSS for enhanced styling */
body {
    overflow-x: hidden;
    font-size: 16px;
}

.container {
    width: 100%;
}

.navbar {
    background-color: #343a40;
    position: fixed;
    top: 0;
    width: 100%;
}

.navbar-brand,
.navbar-nav .nav-link {
    color: #ffffff !important;
}
#services, #about, #skills-experiences, #contact {
  padding: 2px; /* Add some padding to give space around the content */
  font-size: 16px; /* Set the font size to your preferred size */
  text-align: justify; /* Justify the text */
  line-height: 1.5; /* Adjust the line height for better readability */
}

.section-header {
    color: #343a40;
    font-size: 2rem;
    margin: 20px 0;
}

.design-item {
    margin: 20px 0;
    text-align: center;
}

.design-item img {
    max-width: 100%;
}

.section-description {
    font-size: 1.2rem;
    margin: 20px 0;
}
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.7);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid blue;
    border-top: 4px solid orange;
    border-radius: 50%;
    animation: spin 2s linear infinite;
}

.loading-text {
    font-size: 18px;
    color: #3498db;
    margin-top: 10px;
    text-align: center;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loaded .loading-overlay {
    display: none;
}

.section {
    margin: 20px 0;
    text-align: center;
}

.section-title {
    font-size: 2rem;
}

.section-icon {
    font-size: 3rem;
}

.section-content {
    font-size: 1.2rem;
}

.show-more-button {
    cursor: pointer;
    color: blue;
    text-decoration: underline;
}
</style>
</head>

<body>
<!-- Loading overlay -->
<div class="loading-overlay">
    <div class="spinner"></div> <br>
    <div class="loading-text">Loading...</div>
</div>

    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <a class="navbar-brand" href="#"> <img src="images/logo1.jpg" alt="Logo" class="rounded-circle"
            style="width: 30px; height: 30px;"> Charle Cee Graphix</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#intro">Intro</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#webdev">Web Development</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#design">Graphic Design</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#services">Other Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact Us</a>
                </li>
            </ul>
        </div>
    </nav>
    <br><br>

    <div class="container dashboard-content" id="intro">
    <div class="text-center">
        <h2 class="dashboard-title">Charle Cee Graphix</h2></div>

        <p class="section-description">It is a technology company which was founded by <b>Charles Chidule </b>in 2020 aims at developing customizable systems and web applications as well as graphic designing. We have extensive experience in building custom websites and web applications for businesses and individuals.</p>
        <p class="section-description">In addition to web development, we specialize in graphic design and branding. Whether you need a logo, business cards, marketing materials, or website design, we can help you create a professional and cohesive brand that reflects your business values and goals.</p>
        <p class="section-description">Our graphic design services include:</p>
        <ul>
            <li>Logo design</li>
            <li>Business card design</li>
            <li>Brochure design</li>
            <li>Flyer design</li>
            <li>Poster design</li>
            <li>Wedding invitation card design</li>
            <li>Website design using Figma</li>
        </ul>
    </div>
    <br><br>

    <div class="container dashboard-content" id="webdev">
    <div class="text-center"> <!-- Add text-center class to center-align content -->
        <i class="fas fa-laptop-code fa-4x dashboard-icon"></i>
        <h2 class="dashboard-title">Web Development</h2>
    </div>
        <p class="section-description">We use a variety of technologies and tools to build high-quality, responsive websites that are optimized for search engines and designed to convert visitors into customers. 

    <p class="section-description">Below are some of the websites we developed. You can take a brief description of each site</p>
        <section id="job-vacancy-website">

            <h3 class="section-header">1. <a class="website-link" href="https://malawi-vacancy-website.epizy.com" target="_blank">Job Vacancy Website </a></h3>
            <div class="section-content">
                <p>Frequently, job vacancies are posted on various platforms like Facebook and WhatsApp. However, joining the right group or liking certain pages to find job openings can be taxing. Additionally, accessibility to newspapers is problematic in different geographical locations.</p>
                <p>To address these issues, we came up with the <strong>MALAWI JOB VACANCY</strong>. This website offers the following features:</p>
                <ul>
                    <li>Users can sign up, log in using their phone numbers, and create job vacancy posts.</li>
                    <li>Upon logging in, users can see notifications or recent posts, edit/delete their own posts, comment on others' posts, search for specific posts, and update their user details.</li>
                    <li>Non-logged-in users can view recent posts, notifications, and search for specific posts, but they do not have the privilege to create a vacancy post, comment, edit, or delete.</li>
                    <li>Upon registration, users undergo a code verification system supported by an SMS sent to their phone numbers. The same system is also used when a user forgets a password or wants to change their registered phone number.</li>
                </ul>
                <p>This job vacancy website caters to job seekers and employers who are looking for employment or new employees, respectively.</p>
                <p>Check it out <a class="website-link" href="https://malawi-vacancy-website.epizy.com" target="_blank">here</a>.</p>
            </div>
        </section>

        <section id="dairy-management-system">
            <p class="section-header">2. <a class="website-link" href="https://okhalhavoinvestiment.great-site.net" target="_blank">Dairy Management System</a></p>
            <div class="section-content">
                <p>The <strong>Dairy Management System</strong> is a private website designed for managing and storing farmers' records at milk collection centers. It also automates activities such as the generation of payslips and computation of the total liters of milk available.</p>
                <p>Key features of this system include:</p>
                <ul>
                    <li>Record management of farmers and their milk collection data.</li>
                    <li>Debits and  Credits management for farmers.</li>
                    <li>Automated payslip generation for farmers.</li>
                    <li>Automated paylist generation for farmers.</li>
                    <li>Automated computation of total litres in each month for the farmers.</li>
                    <li>Computation of the total liters of milk procured at the milk collection center.</li>
                </ul>
                <p>Take a look of this system at <a class="website-link" href="https://okhalhavoinvestiment.great-site.net" target="_blank">here</a>.</p>
            </div>
        </section>

    </div>
    <div class="container dashboard-content" id="design">
        <div class="text-center">
            <i class="fas fa-paint-brush fa-4x dashboard-icon"></i>
            <h2 class="dashboard-title">Graphic Design</h2>
        </div>
        <div class="container">
            <section id="flyers">
                <div class="section-description">
                    Explore our collection of vibrant and eye-catching flyer designs. Click on the images to view in full size.
                </div>
                <div class="row">
                    <?php
                    // List of flyer images
                    $flyers = array(
                        "advert1.jpg",
                        "advert2.jpg",
                        "advert3.jpg",
                        "advert4.jpg",
                        "advert5.jpg",
                        "advert6.jpg",
                        "advert7.jpg",
                        "advert8.jpg",
                        "advert9.jpg",
                        "advert10.png",
                        "advert11.jpg",
                        "advert12.jpg"
                        // Add more flyer images here
                    );

                    // Display only six items initially
                    for ($i = 0; $i < min(6, count($flyers)); $i++) {
                        echo '<div class="col-md-4 design-item flyer"><a href="images/' . $flyers[$i] . '" data-lightbox="designs"><img src="images/' . $flyers[$i] . '" alt="' . $flyers[$i] . '"></a></div>';
                    }
                    ?>
                </div>
                <?php
                // If there are more items, show the "Show More" button
                if (count($flyers) > 6) {
                    echo '<p class="show-more-button" onclick="showAll(\'flyer\')">See More</p>';
                }
                ?>
            </section>

            <section id="golf-shirts">
                <h2 class="section-header">Golf Shirt and T-shirt Designs</h2>
                <div class="section-description">
                    Check out our stylish and trendy golf shirt and t-shirt designs. Click on the images to view in full size.
                </div>
                <div class="row">
                    <?php
                    // List of golf shirt images
                    $golfShirts = array(
                       
                        "golf1.png",
                        "golf2.png",
                        "golf3.png",
                        "golf4.png",
                        "golf5.png",
                        "golf7.png",
                        "golf8.png",
                        "golf6.png"
                        // Add more golf shirt images here
                    );

                    // Display only six items initially
                    for ($i = 0; $i < min(6, count($golfShirts)); $i++) {
                        echo '<div class="col-md-4 design-item golf-shirt"><a href="images/' . $golfShirts[$i] . '" data-lightbox="designs"><img src="images/' . $golfShirts[$i] . '" alt="' . $golfShirts[$i] . '"></a></div>';
                    }
                    ?>
                </div>
                <?php
                // If there are more items, show the "Show More" button
                if (count($golfShirts) > 6) {
                    echo '<p class="show-more-button" onclick="showAll(\'golf-shirt\')">See More</p>';
                }
                ?>
            </section>

            <section id="logos">
                <h2 class="section-header">Logos</h2>
                <div class ="section-description">
                Explore our logo designs that are tailored to represent your brand. Click on the images to view in full size.
                </div>
                <div class="row">
                    <?php
                    // List of logo images
                    $logos = array(
                        "logo7.png",
                        "logo9.png",
                        "logo2.jpeg",
                        "logo3.png",
                        "logo4.png",
                        "logo5.png",
                        "logo6.png"
                       
                        // Add more logo images here
                    );

                    // Display only six items initially
                    for ($i = 0; $i < min(6, count($logos)); $i++) {
                        echo '<div class="col-md-4 design-item logo"><a href="images/' . $logos[$i] . '" data-lightbox="designs"><img src="images/' . $logos[$i] . '" alt="' . $logos[$i] . '"></a></div>';
                    }
                    ?>
                </div>
                <?php
                // If there are more items, show the "Show More" button
                if (count($logos) > 6) {
                    echo '<p class="show-more-button" onclick="showAll(\'logo\')">See More</p>';
                }
                ?>
            </section>
                        <div class="section-description">
                You can also explore more of our graphic designs on our <a href="https://www.facebook.com/charleceegraphix">Facebook page</a>.
            </div>
        </div>
    </div>


<div class="container dashboard-content" id="services">

    <br>
    <section class="section">
            <i class="fas fa-cogs fa-4x dashboard-icon"></i>
            <h2 class="dashboard-title">Other Services</h2>
            <p>At Charle Cee Graphix, we offer a wide range of technical services to meet your needs. Our services include:</p>
            <ul>
                <li><i class="fas fa-shield-alt"></i> Anti-Virus Installation: Protect your computer from malware and security threats with our expert anti-virus installation services.</li>
                <li><i class="fab fa-windows"></i> Windows Operating System Support: We provide assistance and support for various Windows operating systems, including Windows 11, 10, 8, and 7. Whether you need help with installation, troubleshooting, or optimization, we've got you covered.</li>
                <li><i class="fas fa-key"></i> Windows Activation: Ensure your Windows OS is fully activated and up to date. We can help you with Windows activation to access all the features and updates.</li>
                <li><i class="fas fa-desktop"></i> Microsoft Office Installation and Activation: Get Microsoft Office installed and activated for seamless productivity. We assist in setting up and activating Microsoft Office applications.</li>
                <li><i class="fab fa-linux"></i> Linux Installation: If you prefer Linux as your operating system, we can help you with Linux installation and setup to meet your specific requirements.</li>
                <li><i class="fas fa-bug"></i> Virus Removal: If your computer is infected with viruses or malware, our experts can efficiently remove them and restore your system's health.</li>
                <li><i class="fas fa-microchip"></i> Hardware and Software Support: Our technical services extend to hardware and software support. Whether you need assistance with hardware upgrades or software troubleshooting, we've got the expertise you need.</li>
                <!-- Add more services as needed -->
            </ul>
            </section>


<div class="container dashboard-content" id="about">
<br> <br>
        <section class="section">
            <div class="text-center">
                <img src="images/My_pic.jpg" alt="Profile Picture" class="rounded-circle" style="width: 150px; height: 150px;">
            </div>
            <h4>Charles Chidule</h4>
            <div class="section-content">
            <ul>
                <p>Hi, I'm Charles Chidule, also known as Charle Cee Programmer, the founder and C.E.O of <strong>Charle Cee Graphix</strong>. I was born on April 7, 1999, in Thyolo, Malawi. I'm a passionate and skilled individual with a focus on computer science and technology. As a Malawian national, I'm proud to contribute to the world of technology with my knowledge and expertise.</p>
            <p>My journey in education led me to obtain a Bachelor's degree in Computer Science from the University of Malawi in 2023. This academic achievement has equipped me with the necessary skills to excel in the tech industry.</p>
            <p>I possess a diverse set of skills, including graphic design, programming, web development, and database management. My experiences range from web design and development to graphic design and data collection.</p>
            <p>My mission is to use my skills and knowledge to make a positive impact in the world of technology and design. I'm dedicated to delivering high-quality results and creating solutions that meet the needs of individuals and businesses alike.</p>
             </ul>
            </div>
        </section>
    
            
<div class="container dashboard-content">
    
        <section class="section">
        <i class="fas fa-graduation-cap section-icon"></i> <br>
            <h4>Educational Qualifications</h4>
            <div class="section-content">
            <p>I have accumulated a wealth of diverse educational qualifications throughout my professional including:</strong></p>
                <ul>
                <p><strong>1. Bachelor's degree in Computer Science</strong></p>
                <p>University of Malawi (2023)</p>
                <p><strong> 2. Malawi School Certificate of Education (MSCE)</strong></p>
                <p>Luchenza Secondary School (2017)</p>
                <p><strong> 3. Junior Certificate of Education (JCE)</strong></p>
                <p>Luchenza Secondary School (2015)</p>               
                <p><strong> 4.Primary School Certificate of Education (PLSCE)</strong></p>
                <p>Nawita Primary School (2013)</p>
                </ul>
            </div>
        </section>
    


<div class="container dashboard-content" id="skills-experiences">
<div class="text-center">
            <i class="fas fa-cogs section-icon"></i>
            <h2 class="section-title">Skills</h2>
            </div>
            <div class="section-content">
            <p>I have accumulated a wealth of diverse technical skills and competencies throughout my professional including:</strong></p>
                <ul>
                    <li><i class="fas fa-paint-brush"></i> Graphic Design: Proficient in Adobe Photoshop, Figma, Gimp, Adobe Illustrator, InDesign, and CorelDRAW.</li>
                    <li><i class="fas fa-code"></i> Programming Languages: Experienced in Java, JavaScript, PHP, and Python.</li>
                    <li><i class="fas fa-globe"></i> Web Technologies: Skilled in HTML, CSS, Bootstrap, and jQuery for web development.</li>
                    <li><i class="fas fa-database"></i> Database Systems: Proficient in MySQL for efficient data management.</li>
                    <li><i class="fas fa-poll"></i> Data Collection and Data Entry: Experienced in ODK, iFormBuilder, and SPSS for data analysis and management.</li>
                    <li><i class="fas fa-laptop-code"></i> Software: Proficient in QGIS, MATLAB, SPSS, Android Studio, and Microsoft Office for various tasks.</li>
                    <li><i class="fas fa-tasks"></i> Other Skills: Versatile in branding, web development, word processing, and database management.</li>
                </ul>
            </div>
            <div class="text-center">
            <i class="fas fa-briefcase section-icon"></i>
            <h2 class="section-title">Experiences</h2>
            </div>
            <div class="section-content">
            <p>I have accumulated a wealth of diverse experiences throughout my professional journey. Experiences include:</strong></p>
                <ol>
                    <li><i class="fas fa-code"></i> Web Design and Development</li>
                    <ul>
                        <li>Developed customizable websites and web applications using HTML, CSS, JavaScript, PHP, and MySQL. Some of the websites I developed are available at:</li>
                        <li><a class="website-link" href="https://malawi-vacancy-website.epizy.com" target="_blank">Malawi Job Vacancy </a></li>
                        <li><a class="website-link" href="https://okhalhavoinvestiment.great-site.net"  target="_blank">Dairy Management System </a></li>
                        
                        <li>Implemented responsive designs optimized for search engines and enhanced user experience.</li>
                    </ul>
                    <li><i class="fas fa-paint-brush"></i> Graphic Design</li>
                    <ul>
                        <li>Created visually appealing logos, business cards, brochures, flyers, and posters using graphic design tools (Adobe Creative Suite).</li>
                        <li>Assisted clients in establishing a strong brand identity by providing cohesive designs aligned with their business goals.</li>
                        <li>Samples of my work are available at: <a class="website-link" href="https://www.facebook.com/charleceegraphix" target="_blank">Facebook Page</a></li>
                    </ul>
                    <li><i class="fas fa-poll"></i> Data Collection and Entry</li>
                    <ul>
                        <li>Utilized various methods such as surveys, interviews, and questionnaires (ODK, iFormBuilder).</li>
                        <li>Proficient in data entry using SPSS and Microsoft Office.</li>
                        <li>Worked with the Malawi Population Census in 2018.</li>
                        <li>Served as an Enumerator for conducting household interviews in 2022, Thyolo.</li>
                    </ul>
                    <li><i class="fas fa-chalkboard-teacher"></i> Teaching</li>
                    <ul>
                        <li>Worked as a biology and computer studies teacher at Hillside Private Secondary School.</li>
                        <li>Worked as a computer and maths teacher at Luchenza Secondary School.</li>
                    </ul>
                    <li><i class="fas fa-users"></i> Team Working</li>
                    <ul>
                        <li>Collaboratively worked on the development of a Hospital Management System (web app).</li>
                    </ul>
                </ol>
            </div>


    <div class="container contact-content" id="contact">
    <div class="text-center">
        <i class="fas fa-address-book fa-4x section-icon"></i>
        <h4 class="section-title">Contact Details</h4>
            </div>
        <div class="section-content">
            <p>If you're interested in working with us or learning more about our services, please feel free to contact us using the phone numbers and email addresses listed on this page. We would be happy to discuss your project and provide you with a quote.</p>
            <ul>
                <a href="https://web.facebook.com/charleceegraphix"><i class="fab fa-facebook"></i> Facebook</a> &nbsp;
                <a href="https://www.instagram.com/chidulecharles/"><i class="fab fab fa-instagram"></i> Instagram</a> &nbsp;
                <a href="https://wa.me/265882595892"><i class="fab fa-whatsapp"></i> WhatsApp</a> &nbsp;
                <a href="https://www.linkedin.com/in/charles-chidule-46a660178"><i class="fab fa-linkedin"></i> LinkedIn</a>
            </ul>
            <p><i class="fas fa-phone"></i> +265 (0) 882 595 892 | +265 (0) 996 842 414</p>
            <p><i class="fa fa-envelope"></i> charleceegraphix@gmail.com | chidulecharles1@gmail.com</p>
        </div>
    </div>  
      <script>
        function showAll(sectionId) {
            var elements = document.querySelectorAll('#design .' + sectionId);

            // Display all items
            elements.forEach(function (element) {
                element.style.display = 'block';
            });

            // Hide the "Show More" button
            var showMoreButton = document.querySelector('#design .' + sectionId + ' .show-more-button');
            if (showMoreButton) {
                showMoreButton.style.display = 'none';
            }
        }
    </script>
<script>
        // JavaScript to remove the loading overlay once the page is fully loaded
        window.addEventListener("load", function () {
            // Remove the loading overlay
            document.querySelector(".loading-overlay").style.display = "none";
        });

        // JavaScript to handle the button toggler
        document.querySelector(".navbar-toggler").addEventListener("click", function () {
            // Toggle the active class on the button toggler
            this.classList.toggle("active");
            // Toggle the collapse class on the navbar
            document.querySelector(".collapse.navbar-collapse").classList.toggle("active");
        });
    </script>
</body>

</html>
