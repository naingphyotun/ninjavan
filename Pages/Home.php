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
                            <a class="nav-link" href="Tracking.php">Tracking</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="AboutUs.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Contact.php">Contact</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Shipping
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Add Job</a></li>
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'staff'): ?>
                                    <div>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">OutStaning Job</a></li>
                                        <li><a class="dropdown-item" href="#">Completed Job</a></li>
                                        <li><a class="dropdown-item" href="#">Late Schedule</a></li>
                                    </div>
                                <?php endif; ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">Vechicle Site</a></li>
                            </ul>
                        </li>
                    </ul>

                    <br>
                    <hr>

                    <div class="d-flex flex-row gap-2 ms-lg-auto pt-3 pt-lg-0">
                        <button type="button" class="btn text-white fixed-width-btn"
                            style="background-color: #E64A19;"
                            id="openLogin"
                            data-bs-toggle="modal"
                            data-bs-target="#LoginModal">
                            Login
                        </button>

                        <a href="RegisterForm.php" class="btn btn-outline-dark fixed-width-btn">
                            Register
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav><br><br>


    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" style="padding-right: 10px;">
                    <i class="fa-sharp-duotone fa-regular fa-360-degrees fa-2x" style="letter-spacing:5px; text-align :left; line-height: 1.5;">
                        "We take the hassle out of your pasle."
                    </i>
                </div>
                <div class="col-lg-6">
                    <div class="hero-img">
                        <img src="../img/United Kingdom.png" alt="" class="fa-regular fa-image fa-4x text-secondary img-fluid" style="height: auto; width:auto;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container text-center">
            <div class="mid-banner-img w-100">
                <img src="../img/background.webp" alt="" style="width: auto; height: auto;" class="img-fluid">
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="mb-4">The Solution</h2>
            <div class="row g-4">
                <!-- Pivot to Logistics Card -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-truck-moving fa-2x text-success"></i>
                            </div>
                            <h5 class="card-title">Pivot to Logistics</h5>
                            <p class="card-text">
                                <?php
                                echo "We shifted gears from our fashion venture and founded Ninja Van to solve last-mile deliveries. We focused 100% on building a reliable parcel network.";
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Tech-Driven Approach Card -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-microchip fa-2x text-primary"></i>
                            </div>
                            <h5 class="card-title">Tech-Driven Approach</h5>
                            <p class="card-text">
                                <?php
                                echo "From day one, Ninja Van is powered by technology – real-time tracking, route optimization algorithms, and automation ensure fast and efficient deliveries.";
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- "Day Zero" Spirit Card -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-bolt fa-2x text-dark"></i>
                            </div>
                            <h5 class="card-title">"Day Zero" Spirit</h5>
                            <p class="card-text">
                                <?php
                                echo "We operate with a Day Zero mindset – staying hungry, fast, and never complacent. No matter how much we grow, we hustle like it’s our first day.";
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Early Growth Card -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-globe-asia fa-2x text-info"></i>
                            </div>
                            <h5 class="card-title">Early Growth</h5>
                            <p class="card-text">
                                <?php
                                echo "Our approach worked. In just two years, we expanded from Singapore to Malaysia, Indonesia, Thailand, Vietnam, and the Philippines – bringing Ninja Van across SEA.";
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <a class="navbar-brand" href="#"><img src="../img/Logo-ninjaVan.png"
                            style="height: 45px; width: auto;"
                            alt="Logo"></a>
                    <p class="mt-3"><strong>Address:</strong><br>123 Main Street, City<br>State Province, Country</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-dark"><i class="fa-brands fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-dark"><i class="fa-brands fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-dark"><i class="fa-brands fa-twitter fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="fw-bold">Main</h6>
                    <ul class="list-unstyled mt-3">
                        <li><a href="Home.php" class="text-decoration-none text-muted">Home</a></li>
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

    <script src="../java/jquery-3.7.1.js"></script>

    <script src="../java/script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>