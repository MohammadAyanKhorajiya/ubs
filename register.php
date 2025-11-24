<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
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
            <h2 class="text-center text-danger mb-4">Register Account</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error! </strong> <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form id="registerForm" action="backend/register.php" method="post" class="p-4 border rounded">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name">
                            <span class="error" id="nameError"></span>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                            <span class="error" id="emailError"></span>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create your password">
                            <span class="error" id="passwordError"></span>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter your password">
                            <span class="error" id="confirmPasswordError"></span>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter your phone number">
                            <span class="error" id="phoneError"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">State</label>
                            <select class="form-control" id="state" name="state">
                                <option value="">Select State</option>
                                <option value="Gujarat">Gujarat</option>
                                <option value="Maharashtra">Maharashtra</option>
                                <option value="Rajasthan">Rajasthan</option>
                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                                <option value="Delhi">Delhi</option>
                            </select>
                            <span class="error" id="stateError"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="Enter city">
                            <span class="error" id="cityError"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pincode</label>
                            <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter pincode" maxlength="6">
                            <span class="error" id="pincodeError"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">House No / Building Name</label>
                            <input type="text" class="form-control" id="house_no" name="house_no" placeholder="Enter house/building details">
                            <span class="error" id="houseError"></span>
                        </div>


                        <button type="submit" class="btn btn-danger w-100">Register</button>
                        <p class="text-center mt-4">
                            I have an account? <a href="login.php">Login</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
    document.getElementById("registerForm").addEventListener("submit", function(e) {
        let valid = true;

        document.querySelectorAll(".error").forEach(el => el.innerText = "");

        let name = document.getElementById("name").value.trim();
        if (name === "") {
            document.getElementById("nameError").innerText = "Please enter your name.";
            valid = false;
        } else if (name.length < 3) {
            document.getElementById("nameError").innerText = "Name must be at least 3 characters.";
            valid = false;
        }

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
        } else if (password.length < 6) {
            document.getElementById("passwordError").innerText = "Password must be at least 6 characters.";
            valid = false;
        }

        let confirmPassword = document.getElementById("confirm_password").value.trim();
        if (confirmPassword === "") {
            document.getElementById("confirmPasswordError").innerText = "Please confirm your password.";
            valid = false;
        } else if (confirmPassword !== password) {
            document.getElementById("confirmPasswordError").innerText = "Passwords do not match.";
            valid = false;
        }

        let phone = document.getElementById("phone").value.trim();
        let phonePattern = /^[0-9]{10}$/;
        if (phone === "") {
            document.getElementById("phoneError").innerText = "Please enter your phone number.";
            valid = false;
        } else if (!phonePattern.test(phone)) {
            document.getElementById("phoneError").innerText = "Please enter a valid 10-digit phone number.";
            valid = false;
        }

        let state = document.getElementById("state").value.trim();
        if (state === "") {
            document.getElementById("stateError").innerText = "Please select your state.";
            valid = false;
        }

        let city = document.getElementById("city").value.trim();
        if (city === "") {
            document.getElementById("cityError").innerText = "Please enter your city.";
            valid = false;
        } else if (!/^[A-Za-z\s]+$/.test(city)) {
            document.getElementById("cityError").innerText = "City can contain only letters.";
            valid = false;
        }

        let pincode = document.getElementById("pincode").value.trim();
        if (pincode === "") {
            document.getElementById("pincodeError").innerText = "Please enter your pincode.";
            valid = false;
        } else if (!/^[0-9]{6}$/.test(pincode)) {
            document.getElementById("pincodeError").innerText = "Enter valid 6-digit pincode.";
            valid = false;
        }

        let house = document.getElementById("house_no").value.trim();
        if (house === "") {
            document.getElementById("houseError").innerText = "Please enter house/building details.";
            valid = false;
        }


        if (!valid) {
            e.preventDefault();
        }
    });

    document.getElementById("name").addEventListener("input", function() {
        document.getElementById("nameError").innerText = "";
    });
    document.getElementById("email").addEventListener("input", function() {
        document.getElementById("emailError").innerText = "";
    });
    document.getElementById("password").addEventListener("input", function() {
        document.getElementById("passwordError").innerText = "";
    });
    document.getElementById("confirm_password").addEventListener("input", function() {
        document.getElementById("confirmPasswordError").innerText = "";
    });
    document.getElementById("phone").addEventListener("input", function() {
        document.getElementById("phoneError").innerText = "";
    });

    document.getElementById("city").addEventListener("input", function() {
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
    document.getElementById("cityError").innerText = "";
    });

    document.getElementById("pincode").addEventListener("input", function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        document.getElementById("pincodeError").innerText = "";
    });

    document.getElementById("house_no").addEventListener("input", function() {
        document.getElementById("houseError").innerText = "";
    });

    document.getElementById("state").addEventListener("change", function() {
        document.getElementById("stateError").innerText = "";
    });

    </script>



</body>
</html>
