<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Student Management System || Dashboard</title>
    <link rel="icon" type="image/jpg" href="logo.jpg">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Favicon -->
    <link rel="icon" type="images/ico" href="images/sms.ICO">
</head>

<body style="font-family: 'Arial', sans-serif; background-color: #f8f9fa;">
    <div class="container-scroller">
        <!-- Header -->
        <?php include_once('includes/header.php'); ?>

        <!-- Sidebar -->
        <?php include_once('includes/sidebar.php'); ?>

        <!-- Main Content -->
        <div class="main-panel" style="margin-left: 250px; padding: 20px;">
            <div class="content-wrapper">
                <!-- Academic Year Message -->
                <div class="row">
                    <div class="col-md-12">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h5 style="background-color: #003366; color: white; padding: 10px; border-radius: 5px;">
                                <?php echo $academicYearMessage; ?>
                            </h5>
                            <a href="between-dates-reports.php" style="background-color: #003366; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                                Reports <i class="fas fa-sync-alt" style="color: white;"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <?php
                    $sql1 = "SELECT * FROM tblclass";
                    $query1 = $dbh->prepare($sql1);
                    $query1->execute();
                    $totclass = $query1->rowCount();

                    $sql2 = "SELECT * FROM tblstudent";
                    $query2 = $dbh->prepare($sql2);
                    $query2->execute();
                    $totstu = $query2->rowCount();

                    $sql3 = "SELECT * FROM tbladmin";
                    $query3 = $dbh->prepare($sql3);
                    $query3->execute();
                    $totnotice = $query3->rowCount();

                    $sql4 = "SELECT * FROM tblpublicnotice";
                    $query4 = $dbh->prepare($sql4);
                    $query4->execute();
                    $totpublicnotice = $query4->rowCount();
                    ?>

                    <!-- Total Classes Card -->
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div style="background-color: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span style="font-size: 14px; color: #6c757d;">Total Classes</span>
                                    <h3 style="font-size: 24px; font-weight: 600; color: #003366;"><?php echo htmlentities($totclass); ?></h3>
                                </div>
                                <div style="font-size: 30px; color: #003366;">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Students Card -->
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div style="background-color: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span style="font-size: 14px; color: #6c757d;">Total Students</span>
                                    <h3 style="font-size: 24px; font-weight: 600; color: #003366;"><?php echo htmlentities($totstu); ?></h3>
                                </div>
                                <div style="font-size: 30px; color: #003366;">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Staff Card -->
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div style="background-color: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span style="font-size: 14px; color: #6c757d;">Total Staff</span>
                                    <h3 style="font-size: 24px; font-weight: 600; color: #003366;"><?php echo htmlentities($totnotice); ?></h3>
                                </div>
                                <div style="font-size: 30px; color: #003366;">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Announcements Card -->
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div style="background-color: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span style="font-size: 14px; color: #6c757d;">Announcements</span>
                                    <h3 style="font-size: 24px; font-weight: 600; color: #003366;">
                                        <a href="manage-public-notice.php" style="color: inherit; text-decoration: none;">
                                            <?php echo htmlentities($totpublicnotice); ?>
                                        </a>
                                    </h3>
                                </div>
                                <div style="font-size: 30px; color: #003366;">
                                    <i class="fas fa-bell"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistical Graphs -->
                <div class="row">
                    <div class="col-md-12">
                        <div style="background-color: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px;">
                            <h3 style="color: #003366;">Student Enrollment Statistics</h3>
                            <canvas id="studentChart" style="width: 100%; height: 400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php include_once('includes/footer.php'); ?>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script>
        <?php
        // Fetch student counts for each class
        $classes = ['Form 1', 'Form 2', 'Form 3', 'Form 4'];
        $studentCounts = [];

        foreach ($classes as $class) {
            $sql = "SELECT COUNT(*) as count FROM tblstudent WHERE StudentClass = :class AND Status != 'Graduated'";
            $query = $dbh->prepare($sql);
            $query->bindParam(':class', $class, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $studentCounts[] = $result['count'];
        }
        ?>

        const ctx = document.getElementById('studentChart').getContext('2d');
        const studentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Form 1', 'Form 2', 'Form 3', 'Form 4'],
                datasets: [{
                    label: 'Number of Students',
                    data: <?php echo json_encode($studentCounts); ?>,
                    backgroundColor: '#003366',
                    borderColor: '#002244',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>