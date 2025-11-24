<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg p-3">
        <div class="container-fluid">

            <div class="d-flex align-items-center gap-2 fs-3 text-danger">
                <i class="fa-solid fa-book"></i>
                <a href="index.php" class="text-decoration-none text-danger"><h2 class="fw-bold m-0">Rebooks</h2></a>
            </div>

            <button class="navbar-toggler border-0 fs-3" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="books.php">All Old Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="donatedbooks.php">Donated Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact us</a>
                    </li>
                </ul>

                <div class="fs-4 d-flex gap-4">
                    
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="text-dark"><i class="fa-solid fa-user"></i></a>
                    <?php else: ?>
                        <div class="dropdown">
                            <a href="#" class="text-dark" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>


                    <a href="cart.php" class="text-dark"><i class="fa-solid fa-cart-shopping"></i></a>
                </div>
            </div>
        </div>
    </nav>
</body>
</html>
