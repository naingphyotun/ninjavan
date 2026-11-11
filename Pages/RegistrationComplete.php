<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NinjaVan Logistics | Home</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    <nav class="navbar bg-body-tertiary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="../img/Logo-ninjaVan.png"
                    style="height: 45px; width: auto;"
                    alt="Logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <img src="../img/Logo-ninjaVan.png"
                        style="height: 45px; width: auto;"
                        alt="Logo">
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="Home.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact</a>
                        </li>
                </div>
            </div>
        </div>
    </nav><br><br><br><br><br>

    <div style="justify-self: center;">
        <div class="card" style="width: 18rem;">
            <img src="../img/re.png" class="rounded float-start" alt="...">
            <div class="card-body">
                <h5>Registration Complete</h5>
                <button type="button" class="btn text-white fixed-width-btn"
                    style="background-color: #E64A19;"
                    id="openLogin"
                    data-bs-toggle="modal"
                    data-bs-target="#LoginModal">
                    Login
                </button>
            </div>
        </div>
    </div><br><br>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <a class="navbar-brand" href="#"><img src="../img/Logo-ninjaVan.png"
                            style="height: 45px; width: auto;"
                            alt="Logo"></a>
                    <p class="mt-3"><strong>Address:</strong><br>Harley Street, London<br>London, UK</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-dark"><i class="fa-brands fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-dark"><i class="fa-brands fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-dark"><i class="fa-brands fa-twitter fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="fw-bold">Main</h6>
                    <ul class="list-unstyled mt-3">
                        <li><a href="DashBoard.php" class="text-decoration-none text-muted">Home</a></li>
                        <li><a href="" class="text-decoration-none text-muted">Shipping</a></li>
                        <li><a href="Tracking.php" class="text-decoration-none text-muted">Track</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="fw-bold">Features</h6>
                    <ul class="list-unstyled mt-3">
                        <li><a href="AboutUs.php" class="text-decoration-none text-muted">About Us</a></li>
                        <li><a href="Contact.php" class="text-decoration-none text-muted">Contact</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row text-center text-muted">
                <div class="col-md-12">
                    <p>&copy; 2025 NinjaVan Logistics. All rights reserved.
                        <a href="#" class="ms-3 text-muted text-decoration-none">Privacy Policy</a>
                        <a href="#" class="ms-3 text-muted text-decoration-none">Terms of Service</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <?php include 'LoginPage.php'; ?>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>