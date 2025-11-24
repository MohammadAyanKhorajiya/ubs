<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {

        // 🔥 role भी fetch करना है (ONLY THIS CHANGE)
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ? AND is_verified = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {

                // SESSION SAVE
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role; // ← NEW

                // 🔥 REDIRECT LOGIC
                if ($role === 'admin') {
                    header("Location: ../admin/dashboard.php"); 
                    exit();
                } else {
                    header("Location: ../index.php");
                    exit();
                }

            } else {
                $_SESSION['error'] = "Invalid Password!";
                header("Location: ../login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Account not found or not verified!";
            header("Location: ../login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "All fields are required!";
        header("Location: ../login.php");
        exit();
    }
}
?>