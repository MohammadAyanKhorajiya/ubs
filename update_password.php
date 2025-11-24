<?php
session_start();
include 'backend/config.php';

    // Check token is in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT id, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($id, $expires_at);

    if ($stmt->fetch()) {
        if (strtotime($expires_at) < time()) {
            $_SESSION['error'] = "This reset link has expired.";
            header("Location: forgot_password.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid reset link.";
        header("Location: forgot_password.php");
        exit();
    }
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token'])) {
    // Handle password update form
    $token = $_POST['token'];
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Server-side validation
    if (empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
    } else {
        // Verify the token again 
        $stmt = $conn->prepare("SELECT id, expires_at FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->bind_result($id, $expires_at);

        if ($stmt->fetch()) {
            if (strtotime($expires_at) < time()) {
                $_SESSION['error'] = "Reset link expired. Please try again.";
            } else {
                $stmt->close();

                //  update the new password
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $id);
                $stmt->execute();
                $stmt->close();

                // Delete used token
                $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $stmt->close();

                $_SESSION['message'] = "Password updated successfully!";
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid token!";
        }
    }
} else {
    $_SESSION['error'] = "No token provided.";
    header("Location: forgot_password.php");
    exit();
}
?>