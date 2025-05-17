<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Staff Members</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #003366;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }
        .section-title h2 {
            font-size: 36px;
            font-weight: bold;
        }
        .card {
            background-color: #003366;
            color: yellow;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }
        .card:hover { transform: translateY(-10px); }
        .card img {
            width: 120px;
            height: 120px;
            border: 4px solid yellow;
            border-radius: 50%;
        }
        .contact-links a {
            display: inline-block;
            margin: 10px;
            font-size: 20px;
            color: yellow;
            text-decoration: none;
        }
        .contact-links a:hover { color: #ffcc00; }
        .btn-primary {
            background-color: yellow;
            color: #003366;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .btn-primary:hover { background-color: #ffcc00; }
        .staff-container, .management-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
    </style>
</head>
<body>
<section id="trainers" class="trainers">
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2>Management</h2>
            <p>Our Professionals</p>
        </div>
        
        <div class="management-container">
            <?php
                function formatPhoneNumber($number) {
                    return '+265' . substr($number, 1);
                }
                
                $query = "SELECT * FROM tbladmin WHERE UserType IN ('Head Teacher', 'Deputy HeadTeacher') ORDER BY FIELD(UserType, 'Head Teacher', 'Deputy HeadTeacher')";
                $stmt = $dbh->prepare($query);
                $stmt->execute();
                $management = $stmt->fetchAll(PDO::FETCH_OBJ);
                
                foreach ($management as $row) {
                    $formattedNumber = formatPhoneNumber($row->MobileNumber);
                    echo "<div class='card' data-aos='zoom-in'>";
                    echo "<img src='admin/images/" . htmlspecialchars($row->Image) . "' alt='" . htmlspecialchars($row->Name) . "'>";
                    echo "<h3>" . htmlspecialchars($row->Name) . "</h3>";
                    echo "<span>" . htmlspecialchars($row->UserType) . "</span>";
                    echo "<p>Our " . htmlspecialchars($row->UserType) . " is dedicated to excellence.</p>";
                    echo "<div class='contact-links'>";
                    echo "<a href='tel:$formattedNumber'><i class='fas fa-phone'></i></a>";
                    echo "<a href='https://wa.me/$formattedNumber?text=Hey,%20I've%20got%20your%20contact%20from%20Nyungwe%20Girls%20website' target='_blank'><i class='fab fa-whatsapp'></i></a>";
                    echo "<a href='mailto:" . htmlspecialchars($row->Email) . "'><i class='fas fa-envelope'></i></a>";
                    echo "</div>";
                    echo "<a href='enquiry.php?eid=" . htmlspecialchars($row->ID) . "' class='btn-primary'>Enquiry</a>";
                    echo "</div>";
                }
            ?>
        </div>

        <div class="staff-container">
            <?php
                $query = "SELECT * FROM tbladmin WHERE UserType NOT IN ('Head Teacher', 'Deputy HeadTeacher')";
                $stmt = $dbh->prepare($query);
                $stmt->execute();
                $staffMembers = $stmt->fetchAll(PDO::FETCH_OBJ);
                
                foreach ($staffMembers as $row) {
                    $formattedNumber = formatPhoneNumber($row->MobileNumber);
                    echo "<div class='card' data-aos='zoom-in'>";
                    echo "<img src='admin/images/" . htmlspecialchars($row->Image) . "' alt='" . htmlspecialchars($row->Name) . "'>";
                    echo "<h3>" . htmlspecialchars($row->Name) . "</h3>";
                    echo "<span>" . htmlspecialchars($row->UserType) . "</span>";
                    echo "<p>Our " . htmlspecialchars($row->UserType) . " is dedicated to excellence.</p>";
                    echo "<div class='contact-links'>";
                    echo "<a href='tel:$formattedNumber'><i class='fas fa-phone'></i></a>";
                    echo "<a href='https://wa.me/$formattedNumber?text=Hey,%20I%20have%20got%20your%20contact%20from%20Nyungwe%20Girls%20website' target='_blank'><i class='fab fa-whatsapp'></i></a>";
                    echo "<a href='mailto:" . htmlspecialchars($row->Email) . "'><i class='fas fa-envelope'></i></a>";
                    echo "</div>";
                    echo "<a href='enquiry.php?eid=" . htmlspecialchars($row->ID) . "' class='btn-primary'>Enquiry</a>";
                    echo "</div>";
                }
            ?>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script> AOS.init({ duration: 1000, once: true }); </script>
</body>
</html>
