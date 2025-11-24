<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="contact-section py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h2 class="text-center text-danger mb-4">Contact Us</h2>
            <p class="text-center mb-5">Have questions or need help? You can reach at 
                <a href="rebooks@gmail.com" class="text-danger">rebooks@gmail.com</a>or call on
                <a href="#" class="text-danger">+91 98765 43210</a>.You can also fill out the form below and we'll get back to your contact.
            </p>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="send_message.php" method="POST" class="p-4 rounded shadow-sm"
                        style="background: #fff;">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Your Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-danger px-4">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>


</body>

</html>