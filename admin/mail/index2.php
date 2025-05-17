<?php
include('config.php');

// Initialize response array for AJAX
$response = ['status' => '', 'message' => '', 'redirect' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = "Invalid email address!";
    } else {
        try {
            // Check if the email already exists in the database
            $checkEmailQuery = "SELECT verified FROM registered_users WHERE email = :email";
            $stmt = $dbh->prepare($checkEmailQuery);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Email exists; check if it is verified
                if ($result['verified'] == 'yes') {
                    $response['status'] = 'success';
                    $response['message'] = "Email already registered and verified!. Redirecting to registration completetion....";
                    $response['redirect'] = "reg_complete.php?email=" . urlencode($email);
                } else {
                    // Email exists but is not verified; redirect to OTP verification
                    $response['status'] = 'success';
                    $response['message'] = "Email already registered but not verified! Redirecting to OTP verification...";
                    $response['redirect'] = "otp.php?resend=no&email=" . urlencode($email);
                }
            } else {
                // Email does not exist; insert it into the database
                $insertQuery = "INSERT INTO registered_users (email, verified) VALUES (:email, 0)";
                $stmt = $dbh->prepare($insertQuery);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = "Registration successful! Redirecting to OTP verification...";
                    $response['redirect'] = "otp.php?resend=no&email=" . urlencode($email);
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Error: Unable to register.";
                }
            }
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = "Error: " . $e->getMessage();
        }
    }

    // Send the response back to AJAX
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-bottom: 20px;
            color: #003366;
        }
        input[type="email"], button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #003366;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0055a4;
        }
        .message {
            margin-top: 10px;
            font-size: 14px;
            color: red;
            display: none;
        }
        .message.success {
            color: green;
        }
    </style>
    <script>
        async function handleSubmit(event) {
            event.preventDefault(); // Prevent form submission

            const emailInput = document.querySelector('input[name="email"]');
            const submitButton = document.querySelector('button');
            const messageBox = document.querySelector('.message');

            // Get email value
            const email = emailInput.value.trim();

            // Update button text
            submitButton.textContent = "Registering...";
            submitButton.disabled = true;

            // Send AJAX request
            const response = await fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ email }),
            });

            const result = await response.json();

            // Reset button text
            submitButton.textContent = "Register";
            submitButton.disabled = false;

            // Display message
            messageBox.textContent = result.message;
            messageBox.className = 'message'; // Reset class
            messageBox.style.display = 'block'; // Show message
            if (result.status === 'success') {
                messageBox.classList.add('success');

                // Redirect after 5 seconds
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 3000);
            } else {
                messageBox.classList.add('error');
            }
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Register Your Email</h2>
        <form onsubmit="handleSubmit(event)">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Register</button>
        </form>
        <p class="message"></p>
    </div>
</body>
</html>
