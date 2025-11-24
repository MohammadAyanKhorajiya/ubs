<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <h2 class="text-center text-danger mb-4">Forgot Password</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error! </strong> <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success! </strong> <?= $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <form id="forgotPasswordForm" action="send_reset_link.php" method="POST" class="p-4 border rounded">
                        <div class="mb-3">
                            <label for="email" class="form-label">Enter your Email</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter your Email">
                            <span class="error" id="emailError"></span>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Send Reset Password Link</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
    document.getElementById("forgotPasswordForm").addEventListener("submit", function(e) {
        let valid = true;
        document.getElementById("emailError").innerText = "";

        let email = document.getElementById("email").value.trim();
        let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if (email === "") {
            document.getElementById("emailError").innerText = "Please enter your email.";
            valid = false;
        } else if (!emailPattern.test(email)) {
            document.getElementById("emailError").innerText = "Please enter a valid email.";
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });

    document.getElementById("email").addEventListener("input", function() {
        document.getElementById("emailError").innerText = "";
    });
    </script>
</body>
</html>