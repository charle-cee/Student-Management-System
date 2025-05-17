<?php
include('config.php');

$response = ['status' => '', 'message' => '', 'redirect' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    if ($action === "register") {
        $fullname = $_POST["fullname"];

        if (empty($fullname)) {
            $response['status'] = 'error';
            $response['message'] = "Full name is required!";
            echo json_encode($response);
            exit;
        }

        try {
            // Check if email exists
            $stmt = $dbh->prepare("SELECT verified FROM registered_users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                if ($result['verified'] == 'yes') {
                    $response['status'] = 'success';
                    $response['message'] = "Email is already registered and verified!";
                } else {
                    $response['status'] = 'success';
                    $response['message'] = "Email exists but not verified. Redirecting to OTP verification.";
                    $response['redirect'] = "otp.php?resend=no&email=" . urlencode($email);
                }
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $dbh->prepare("INSERT INTO registered_users (email, name, password) VALUES (:email, :name, :password)");
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':name', $fullname);
                $stmt->bindParam(':password', $hashed_password);

                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = "Registration successful! Redirecting to OTP verification...";
                    $response['redirect'] = "otp.php?resend=no&email=" . urlencode($email);
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Registration failed. Try again.";
                }
            }
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = "Database error: " . $e->getMessage();
        }
    }

    if ($action === "login") {
        try {
            $stmt = $dbh->prepare("SELECT password FROM registered_users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $response['status'] = 'success';
                $response['message'] = "Login successful! Redirecting...";
                $response['redirect'] = "dashboard.php"; 
            } else {
                $response['status'] = 'error';
                $response['message'] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $response['status'] = 'error';
            $response['message'] = "Database error: " . $e->getMessage();
        }
    }

    echo json_encode($response);
    exit;
}
?>
