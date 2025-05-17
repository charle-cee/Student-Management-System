<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
include('mail_message.php');
// Check if the database connection is already established
if (!isset($dbh)) {
    include_once('../includes/dbconnection.php');
}

$message = ""; // Message to display to the user
$message_class = ""; // CSS class for styling the message
$show_password_form = false; // Flag to show/hide the password form
$redirect = false; // Flag to trigger redirection

if (!empty($_POST["submit_otp"])) {
    try {
        $otp = trim($_POST["otp"]); // Trim user input
        $email = $_POST["email"];
        
        // Query to get the OTP, sent time, and expiry time from tbladmin based on the given email
        $query = "SELECT * FROM tbladmin WHERE Email = :email";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        // Fetch the stored OTP, sent time, and expiry time from the database
        $stored_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stored_otp = trim($stored_data['Otp']); // Trim stored OTP
        $otp_expiry = $stored_data['Otp_Expiry'];
        $staffname = $stored_data['Name'];
        $username = $stored_data['UserName'];
        if ($stored_otp) {
            

            // Check if the OTP is expired
            if (strtotime($otp_expiry) < time()) {
                // OTP expired, redirect to otp.php with resend=no to regenerate new OTP
                header("Location: otp.php?resend=no&email=" . urlencode($email));
                exit();
            }

            // Compare the user input OTP with the stored OTP
            if ((string)$stored_otp === (string)$otp) {
                // OTP matched, show the password form
                $show_password_form = true;
                $message = "OTP verified successfully! Please set a new password.";
                $message_class = "success"; // CSS class for success
            } else {
                $message = "Invalid OTP! Please double-check.";
                $message_class = "error"; // CSS class for error
            }
        } else {
            $message = "No OTP found for this email.";
            $message_class = "error"; // CSS class for error
        }
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
        $message_class = "error"; // CSS class for error
    }
}

// Handle password change
if (isset($_POST["change_password"])) {
    $email = $_POST["email"];
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

        // Query to get the OTP, sent time, and expiry time from tbladmin based on the given email
        $query = "SELECT * FROM tbladmin WHERE Email = :email";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        // Fetch the stored OTP, sent time, and expiry time from the database
        $stored_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $staffname = $stored_data['Name'];
        $username = $stored_data['UserName'];

    // Validate passwords
    if (empty($new_password) || empty($confirm_password)) {
        $message = "Please fill in both password fields.";
        $message_class = "error";
        $show_password_form = true;
    } elseif ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_class = "error";
        $show_password_form = true;
    } else {
        try {
            // Hash the new password using MD5
            $hashed_password = md5($new_password);

            // Update the password in the database
            $update_query = "UPDATE tbladmin SET Password = :password WHERE Email = :email";
            $update_stmt = $dbh->prepare($update_query);
            $update_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $update_stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $update_stmt->execute();
            $mailing = sendPasswordChange($staffname, $username, $email, $hashed_password);
            // Notify the user and set the redirect flag
            $message = "Password changed successfully! Redirecting to login page...";
            $message_class = "success";
            $show_password_form = true;
            $redirect = true; // Set redirect flag to true
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $message_class = "error";
            $show_password_form = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .message-container {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .success {
            color: #4CAF50;
        }
        .error {
            color: #f44336;
        }
        .btn {
            display: block;
            margin: 20px auto 0;
            padding: 10px 20px;
            background-color: #003366;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn:disabled {
            background-color: grey;
            cursor: not-allowed;
        }
        .password-form {
            margin-top: 20px;
        }
        .password-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($redirect) && $redirect): ?>
                // Redirect after 6 seconds if password change is successful
                setTimeout(function() {
                    window.location.href = "../login.php";
                }, 6000);
            <?php endif; ?>
        });
    </script>
</head>
<body>
    <div class="message-container">
        <h2 class="<?php echo $message_class; ?>"><?php echo $message; ?></h2>

        <?php if ($show_password_form): ?>
            <!-- Password Change Form -->
            <form class="password-form" method="POST" action="">
                <h3> Setting New Password </h3>
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <input type="submit" name="change_password" value="Change Password" class="btn">
            </form>
        <?php elseif (!empty($message) && !$redirect): ?>
            <!-- Resend OTP Button -->
            <a href="otp.php?resend=yes&email=<?php echo $email; ?>" class="btn" id="action-btn">Resend OTP</a>
        <?php endif; ?>
    </div>
</body>
</html>