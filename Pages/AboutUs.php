<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_email']);
$userName   = $_SESSION['user_name'] ?? '';
$userRole   = $_SESSION['user_role'] ?? '';
$homeLink   = $isLoggedIn ? "DashBoard.php" : "Home.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Ninja Van</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        /* Additional About Us specific styles */
        .founder-img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        /* (Optional) Timeline styling */
        .timeline {
            position: relative;
            margin: 2rem 0;
            padding-left: 20px;
            /* space for line */
        }

        .timeline::before {
            content: "";
            position: absolute;
            top: 0;
            left: 10px;
            width: 4px;
            height: 100%;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: "●";
            position: absolute;
            left: 0;
            color: #6c757d;
            font-size: 0.8rem;
            line-height: 1;
            /* The bullet point for timeline */
        }
    </style>
</head>

<body class="bg-white">
    <!-- Navigation (consistent with other pages) -->
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
                            <a class="nav-link active" aria-current="page" href="DashBoard.php">Home</a>
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
                                <li><a class="dropdown-item" href="#" data-bs-target="#AddJobModal" data-bs-toggle="modal">Add Job</a></li>
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'staff'): ?>
                                    <div>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="StaffJobBoard.php">Staff Job Board</a></li>
                                    </div>
                                <?php endif; ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="vehicle_site.php">Vechicle Site</a></li>
                            </ul>
                        </li>
                    </ul>

                    <br>
                    <hr>
                    <div class="d-flex flex-column gap-2">
                        <?php if ($isLoggedIn): ?>
                            <?php if (isset($_SESSION['user_name'])): ?>
                                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                    <i class="fas fa-user-circle fa-2x text-dark"></i>
                                    <div>
                                        <span class="fw-bold d-block text-dark"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                        <small class="text-muted"><?php echo ucfirst($_SESSION['user_role']); ?></small>
                                    </div>
                                </div>
                                <a href="../API/logout.php" class="btn btn-outline-danger w-100 mt-2">Logout</a>
                            <?php else: ?>
                                <button type="button" class="btn text-white" style="background-color: #E64A19;" data-bs-toggle="modal" data-bs-target="#LoginModal">Login</button>
                                <a href="RegisterForm.php" class="btn btn-outline-dark">Register</a>
                            <?php endif; ?>
                        <?php else: ?>
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>
    </nav>

    <!-- Spacing to offset fixed navbar (could use padding-top in CSS as well) -->
    <br><br><br><br>

    <!-- Hero Banner Section -->
    <section class="banner d-flex align-items-center text-center" style="background: url('../img/aboutUS.webp') center center no-repeat; background-size: cover;">
        <div class="container position-relative text-white py-5">
            <h1 class="inventory-title">Our Story</h1>
            <p class="inventory-subtitle">From One Van to Southeast Asia’s Smartest Logistics</p>
        </div>
    </section>

    <!-- Founding Story Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="mb-4">How It Started</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <!-- Founders' photo -->
                    <img src="../img/founders.jpg" alt="Ninja Van Founders" class="founder-img mb-4 mb-md-0">
                </div>
                <div class="col-md-6">
                    <?php
                    $foundingText = "Ninja Van started in Singapore in 2014, founded by Lai Chang Wen, Shaun Chong, and Boxian Tan. What began as a support for their own online fashion store quickly turned into a mission to fix e-commerce deliveries. With a single second-hand van and a big vision, our founders set out to make deliveries efficient, reliable, and scalable across Southeast Asia.";
                    echo "<p>$foundingText</p>";
                    ?>
                    <!-- Highlighted founder quote -->
                    <figure class="text-center bg-light p-3 rounded">
                        <blockquote class="blockquote mb-1">
                            <p>"No, we have multiple vans, don’t worry about it."</p>
                        </blockquote><br>
                        <figcaption class="blockquote-footer">
                            Lai Chang Wen, CEO & Co-Founder, recalling Ninja Van’s early days
                        </figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </section>

    <!-- Problem Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-4">The Problem</h2>
            <div class="row g-4">
                <!-- E-commerce Gap Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-shopping-cart fa-2x text-danger"></i>
                            </div>
                            <h5 class="card-title">E-commerce Gap</h5>
                            <p class="card-text">
                                <?php
                                echo "Traditional couriers weren’t meeting the needs of online sellers, leaving a huge gap in e-commerce logistics.";
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Delivery Failures Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-box-open fa-2x text-warning"></i>
                            </div>
                            <h5 class="card-title">Delivery Failures</h5>
                            <p class="card-text">
                                <?php
                                echo "Lost or delayed packages were common with existing services — a frustration we experienced firsthand with our own startup.";
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Solution Section -->
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

    <!-- Timeline Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-4">Timeline</h2>
            <div class="timeline">
                <!-- 2014 -->
                <div class="timeline-item">
                    <h5>2014</h5>
                    <p>Founded in Singapore. Lai Chang Wen and his co-founders begin Ninja Van with one van and a vision.</p>
                </div>
                <!-- 2015-2016 -->
                <div class="timeline-item">
                    <h5>2015–2016</h5>
                    <p>Regional expansion begins – launching in Malaysia and Indonesia (2015), then Vietnam, Philippines, and Thailand (2016).</p>
                </div>
                <!-- 2018 -->
                <div class="timeline-item">
                    <h5>2018</h5>
                    <p>Ninja Van enters Myanmar, extending its footprint to cover all of Southeast Asia.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Drivers Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="mb-4">Success Drivers</h2>
            <ul class="list-inline text-center mb-0">
                <li class="list-inline-item mx-4 my-2">
                    <i class="fas fa-user-friends fa-2x text-success mb-2"></i><br>
                    <span class="fw-semibold">Founders’ Vision</span>
                </li>
                <li class="list-inline-item mx-4 my-2">
                    <i class="fas fa-cogs fa-2x text-primary mb-2"></i><br>
                    <span class="fw-semibold">Technology</span>
                </li>
                <li class="list-inline-item mx-4 my-2">
                    <i class="fas fa-hand-holding-usd fa-2x text-warning mb-2"></i><br>
                    <span class="fw-semibold">Investor Support</span>
                </li>
            </ul>
            <div class="mt-3 text-center">
                <?php
                // Optionally, add brief descriptions
                echo "<p class='text-muted mb-0'>Our founders’ bold vision, a tech-driven model, and strong investor backing have fueled Ninja Van’s success.</p>";
                ?>
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

    <!-- (Optional) Footer could go here if needed -->
     <?php if (!$isLoggedIn): ?>
        <?php include 'LoginPage.php'; ?>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
        <?php include 'AddJobModal.php'; ?>
    <?php endif; ?>

    <script src="../java/jquery-3.7.1.js"></script>
    <script src="../java/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>