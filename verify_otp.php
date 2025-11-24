<?php
session_start();
include 'backend/config.php';

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
} else {
    
    header("Location: register.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $conn->real_escape_string($_POST['otp']);

    $sql = "SELECT * FROM users WHERE email='$email' AND OTP='$otp'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $update = "UPDATE users SET is_verified=1 WHERE email='$email'";
        if ($conn->query($update) === TRUE) {
            unset($_SESSION['email']); 
            header("Location: login.php");
            exit;
        } else {
            echo "Error while updating verification status: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Invalid OTP!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center text-danger mb-4">OTP Verification</h2>
            <p class="text-muted text-center">
                A verification code has been sent to your email: <strong><?= htmlspecialchars($email) ?></strong>
            </p>
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <form method="POST" class="p-4 border rounded">
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" class="form-control" name="otp" placeholder="Enter OTP" maxlength="6" required>
                            <?php if (isset($_SESSION['error'])): ?>
                                <small class="text-danger"><?= $_SESSION['error']; ?></small>
                                <?php unset($_SESSION['error']); ?>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
