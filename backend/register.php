<?php
include 'config.php';
session_start();

// PHPMailer import
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $conn->real_escape_string($_POST['name']);
    $email    = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone    = $conn->real_escape_string($_POST['phone']);
    $state     = $conn->real_escape_string($_POST['state']);
    $city      = $conn->real_escape_string($_POST['city']);
    $pincode   = $conn->real_escape_string($_POST['pincode']);
    $house_no  = $conn->real_escape_string($_POST['house_no']);


    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Email is already registered. Please use another email.";
        header("Location: ../register.php");
        exit();
    } else {
        $otp = rand(100000, 999999);

        $sql = "INSERT INTO users (name, email, password, phone, state, city, pincode, house_no, otp, is_verified)
        VALUES ('$name', '$email', '$password', '$phone', '$state', '$city', '$pincode', '$house_no', '$otp', 0)";

        
        if ($conn->query($sql) === TRUE) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true;
                $mail->Username = 'jishanmarviya07@gmail.com'; 
                $mail->Password = 'arwvgtqwjhsedlhq'; 
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('jishanmarviya07@gmail.com', 'Your Website');
                $mail->addAddress($email, $name);
                $mail->isHTML(true);
                $mail->Subject = "Your OTP Verification Code";
                $mail->Body    = "<h3>Hello $name,</h3>
                                  <p>Your OTP code is: <b>$otp</b></p>";

                $mail->send();

                $_SESSION['email'] = $email; 
                header("Location: ../verify_otp.php");
                exit();

            } catch (Exception $e) {
                $_SESSION['error'] = "Registered but OTP email could not be sent. Error: {$mail->ErrorInfo}";
                header("Location: ../register.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Something went wrong. Please try again.";
            header("Location: ../register.php");
            exit();
        }
    }
}
?>
