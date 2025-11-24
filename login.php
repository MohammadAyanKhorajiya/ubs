<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
            <h2 class="text-center text-danger mb-4">Login Account</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error! </strong> <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form id="loginForm" action="backend/login.php" method="post" class="p-4 border rounded">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                            <span class="error" id="emailError"></span>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                            <span class="error" id="passwordError"></span>
                        </div>
                        <div class="text-end mb-3">
                            <a href="forgot_password.php">Forgot Password?</a>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Login</button>
                        <p class="text-center mt-4">
                            Don’t have an account? <a href="register.php">Register</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
    document.getElementById("loginForm").addEventListener("submit", function(e) {
        let valid = true;

        document.querySelectorAll(".error").forEach(el => el.innerText = "");

        let email = document.getElementById("email").value.trim();
        let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if (email === "") {
            document.getElementById("emailError").innerText = "Please enter your email.";
            valid = false;
        } else if (!emailPattern.test(email)) {
            document.getElementById("emailError").innerText = "Please enter a valid email.";
            valid = false;
        }

        let password = document.getElementById("password").value.trim();
        if (password === "") {
            document.getElementById("passwordError").innerText = "Please enter your password.";
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });

    document.getElementById("email").addEventListener("input", function() {
        document.getElementById("emailError").innerText = "";
    });
    document.getElementById("password").addEventListener("input", function() {
        document.getElementById("passwordError").innerText = "";
    });
    </script>
</body>
</html>