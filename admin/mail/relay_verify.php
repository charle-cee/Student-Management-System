<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../includes/dbconnection.php');

$email = isset($_GET['email']) ? $_GET['email'] : '';
$otp = isset($_GET['otp']) ? $_GET['otp'] : '';

$message = '';
$status = false;
$redirectUrl = ''; // Variable to store the redirect URL

if (!empty($email) && !empty($otp)) {
    try {
        // Clean the OTP to ensure no leading/trailing spaces
        $otp = trim($otp);
        $email = trim($email);

        // Prepare the query
        $query = "SELECT Otp, Otp_Expiry FROM tbladmin WHERE Email = :email";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $dbOtp = trim($result['Otp']);  // Ensure the OTP from the database is trimmed
            $otpExpiry = trim($result['Otp_Expiry']);  // Trim the expiry time

            // Compare OTP and check if it has expired
            if ($dbOtp === $otp && strtotime($otpExpiry) > time()) {
                // OTP is valid and not expired
                $message = "Verification successful! Redirecting to login...";
                $status = true;
                $redirectUrl = "../login.php?email=" . urlencode($email);
            } else {
                // OTP is invalid or expired
                $message = "Invalid or expired OTP. Please request a new OTP.";
                 $redirectUrl = "resend_otp.php?resend=yes&email=" . urlencode($email);
            }
        } else {
            $message = "No user found with this email address.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
} else {
    $message = "Invalid request. Missing required parameters.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: black;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h2 {
            color: #003366;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            margin-top: 20px;
            color: #333;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #003366;
            border-radius: 5px;
            font-size: 16px;
            color: #003366;
            background-color: #f0f0f0;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #003366;
            color: gold;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0055a4;
        }
        .resend-btn {
            margin-top: 20px;
            padding: 10px;
            background-color: gold;
            color: #003366;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .resend-btn:hover {
            background-color: #e6b800;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let redirectUrl = "<?php echo $redirectUrl; ?>";
            if (redirectUrl) {
                setTimeout(function () {
                    window.location.href = redirectUrl;
                }, 3000); // Redirect in 3 seconds
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Email Verification</h2>
        <form id="verify-form" method="POST" action="">
            <div class="form-group">
                <input type="text" id="otp" name="otp" placeholder="Enter OTP" value="<?php echo htmlspecialchars($otp); ?>" readonly>
            </div>
            <div class="form-group">
                <button type="submit" id="verify-btn" class="btn">Verify</button>
            </div>
        </form>

        <?php if ($message): ?>
            <p class="message <?php echo $status ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <?php if (!$status): // Show button only when status is false (error) ?>
            <button onclick="triggerResend()" class="resend-btn">Request New OTP</button>
        <?php endif; ?>
    </div>
</body>
</html>
