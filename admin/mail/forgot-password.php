<?php
include('../includes/dbconnection.php');

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
            $checkEmailQuery = "SELECT * FROM tbladmin WHERE Email = :email";
            $stmt = $dbh->prepare($checkEmailQuery);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Email exists
                $response['status'] = 'success';
                $response['message'] = "Email entered is registered with the system! Redirecting to OTP verification...";
                $response['redirect'] = "otp.php?resend=no&email=" . urlencode($email);
            } else {
                // Email does not exist
                $response['status'] = 'error';
                $response['message'] = "Email entered is not registered with the system!";
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
    <title>Password Recovery</title>
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
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #003366;
            font-size: 24px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
            font-size: 14px;
        }
        input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="email"]:focus {
            border-color: #003366;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 51, 102, 0.5);
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #003366, #0055a4);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: linear-gradient(135deg, #0055a4, #003366);
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .message {
            margin-top: 15px;
            font-size: 14px;
            color: red;
            display: none;
            animation: fadeIn 0.5s;
        }
        .message.success {
            color: green;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
            submitButton.textContent = "Processing...";
            submitButton.disabled = true;

            // Send AJAX request
            const response = await fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ email }),
            });

            const result = await response.json();

            // Reset button text
            submitButton.textContent = "Submit";
            submitButton.disabled = false;

            // Display message
            messageBox.textContent = result.message;
            messageBox.className = 'message'; // Reset class
            messageBox.style.display = 'block'; // Show message
            if (result.status === 'success') {
                messageBox.classList.add('success');

                // Redirect after 3 seconds
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
        <h2>Password Recovery</h2>
        <form onsubmit="handleSubmit(event)">
            <label for="email">Please enter your registered email</label>
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Reset Password</button>
        </form>
        <p class="message"></p>
    </div>
</body>
</html>