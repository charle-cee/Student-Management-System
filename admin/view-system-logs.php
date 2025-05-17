<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
    exit();
}

// Authenticate every visit
$authenticated = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
    $enteredPassword = md5($_POST['admin_password']);
    $adminId = $_SESSION['sturecmsaid'];

    $sql = "SELECT Password FROM tbladmin WHERE ID = :admin_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $enteredPassword === $result['Password']) {
        $authenticated = true;
    } else {
       echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">
        <title>Verify Admin</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        Swal.fire({
            title: "Enter Your Password",
            input: "password",
            inputLabel: "Incorrect password",
            inputPlaceholder: "Enter your password",
            inputAttributes: {
                maxlength: "100",
                autocapitalize: "off",
                autocorrect: "off"
            },
            background: "#fff",
            confirmButtonColor: "#FFD700",
            cancelButtonColor: "#003366",
            showCancelButton: true,
            confirmButtonText: "Authenticate"
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const form = document.createElement("form");
                form.method = "POST";
                form.style.display = "none";

                const input = document.createElement("input");
                input.name = "admin_password";
                input.value = result.value;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            } else {
                window.location.href = "dashboard.php";
            }
        });
    </script>
    </body>
    </html>';
        exit();
    }
}

if (!$authenticated) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="icon" type="images/ico" href="images/sms.ICO">

        <title>Verify Admin</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        Swal.fire({
            title: "Enter Your Password",
            input: "password",
            inputLabel: "To continue, please verify your password",
            inputPlaceholder: "Enter your password",
            inputAttributes: {
                maxlength: "100",
                autocapitalize: "off",
                autocorrect: "off"
            },
            background: "#fff",
            confirmButtonColor: "#FFD700",
            cancelButtonColor: "#003366",
            showCancelButton: true,
            confirmButtonText: "Authenticate"
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const form = document.createElement("form");
                form.method = "POST";
                form.style.display = "none";

                const input = document.createElement("input");
                input.name = "admin_password";
                input.value = result.value;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            } else {
                window.location.href = "dashboard.php";
            }
        });
    </script>
    </body>
    </html>';
    exit();
}

// Fetch logs after authentication
$recordsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $recordsPerPage;

$sql = "SELECT * FROM admin_logs ORDER BY timestamp DESC LIMIT :limit OFFSET :offset";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$countStmt = $dbh->query("SELECT COUNT(*) FROM admin_logs");
$totalLogs = $countStmt->fetchColumn();
$totalPages = ceil($totalLogs / $recordsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Logs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/ico" href="images/sms.ICO">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .table th {
            background-color: #003366;
            color: white;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .pagination a {
            padding: 8px 16px;
            background: #003366;
            color: white;
            margin: 0 5px;
            text-decoration: none;
            border-radius: 5px;
        }
        .pagination a.active {
            background-color: gold;
            color: black;
        }
        @media(max-width: 768px) {
            .table-responsive {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
<div class="container-scroller">
    <?php include_once('includes/header.php'); ?>
    <div class="container-fluid page-body-wrapper">
        <?php include_once('includes/sidebar.php'); ?>
        <div class="main-panel">
            <div class="content-wrapper">
                <h3 class="text-center text-black">System Logs (Audit Trail)</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Table</th>
                            <th>IP Address</th>
                            <th>Timestamp</th>
                            <th>Method</th>
                            <th>URL</th>
                            <th>Device</th>
                            <th>Details</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sn = $offset + 1;
                        foreach ($logs as $row) {
                            echo "<tr>
                                <td>{$sn}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['action']}</td>
                                <td>{$row['status']}</td>
                                <td>{$row['affected_table']}</td>
                                <td>{$row['ip_address']}</td>
                                <td>{$row['timestamp']}</td>
                                <td>{$row['request_method']}</td>
                                <td>{$row['request_url']}</td>
                                <td>{$row['user_agent']}</td>
                                  <td>{$row['action_details']}</td>
                            </tr>";
                            $sn++;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="vendors/js/vendor.bundle.base.js"></script>
<script src="js/off-canvas.js"></script>
<script src="js/misc.js"></script>
</body>
</html>
