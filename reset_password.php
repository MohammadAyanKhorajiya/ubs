<?php
session_start();
if (!isset($_GET['token'])) {
    $_SESSION['error'] = "Invalid reset link.";
    header("Location: forgot_password.php");
    exit();
}
$token = $_GET['token'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
    <style>
        .error {
            color: red; font-size: 14px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center text-danger mb-4">Reset Password</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error! </strong> <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form id="resetPasswordForm" action="update_password.php" method="post" class="p-4 border rounded">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password">
                            <span class="error" id="newPasswordError"></span>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Re-enter New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter new password">
                            <span class="error" id="confirmPasswordError"></span>
                        </div>
                        
                        <button type="submit" class="btn btn-danger w-100">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
    document.getElementById("resetPasswordForm").addEventListener("submit", function(e) {
        let valid = true;

        document.querySelectorAll(".error").forEach(el => el.innerText = "");

        let newPassword = document.getElementById("new_password").value.trim();
        let confirmPassword = document.getElementById("confirm_password").value.trim();

        if (newPassword === "") {
            document.getElementById("newPasswordError").innerText = "Please enter a new password.";
            valid = false;
        } else if (newPassword.length < 8) {
            document.getElementById("newPasswordError").innerText = "Password must be at least 8 characters long.";
            valid = false;
        }

        if (confirmPassword === "") {
            document.getElementById("confirmPasswordError").innerText = "Please re-enter your new password.";
            valid = false;
        } else if (newPassword !== confirmPassword) {
            document.getElementById("confirmPasswordError").innerText = "Passwords do not match.";
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });

    document.getElementById("new_password").addEventListener("input", function() {
        document.getElementById("newPasswordError").innerText = "";
    });
    document.getElementById("confirm_password").addEventListener("input", function() {
        document.getElementById("confirmPasswordError").innerText = "";
    });
    </script>
</body>
</html>