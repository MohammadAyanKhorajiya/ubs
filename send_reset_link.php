<?php
session_start();
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'backend/config.php'; // database connection mate

    $email = trim($_POST['email']);

    if (!empty($email)) {
        //email exists in users table
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            // Generate reset token
            $token = bin2hex(random_bytes(16));
            $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Insert into password_resets table
            $insert = $conn->prepare("INSERT INTO password_resets (id, token, expires_at) VALUES (?, ?, ?)");
            $insert->bind_param("iss", $user_id, $token, $expires_at);
            $insert->execute();

            // Reset link
            $reset_link = "http://localhost/UBS/reset_password.php?token=" . $token;

            // PHPMailer setup
            $mail = new PHPMailer(true);

            try {
                // SMTP settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username = 'jishanmarviya07@gmail.com';
                $mail->Password = 'arwvgtqwjhsedlhq';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Email settings
                $mail->setFrom('jishanmarviya07@gmail.com', 'UBS System');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "
                    Hi,<br><br>

                    Click the link below to reset your password:<br>
                    <a href='$reset_link'>$reset_link</a>
                ";

                $mail->send();
                $_SESSION['message'] = "Password reset link sent to your email.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Mail not be sent. Error: {$mail->ErrorInfo}";
            }
        } else {
            $_SESSION['error'] = "Email not found.";
        }
    } else {
        $_SESSION['error'] = "Please enter your email.";
    }

    header("Location: forgot_password.php");
    exit();
}
?>