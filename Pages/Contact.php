<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$isLoggedIn = isset($_SESSION['user_email']);
$userName   = $_SESSION['user_name'] ?? '';
$userRole   = $_SESSION['user_role'] ?? '';
$homeLink   = $isLoggedIn ? "DashBoard.php" : "Home.php";

/**
 * Edit these links to your real accounts:
 */
$channels = [
    [
        "name" => "TikTok",
        "desc" => "Quick updates & short tutorials",
        "url"  => "https://www.tiktok.com/",
        "icon" => "fa-brands fa-tiktok"
    ],
    [
        "name" => "Facebook",
        "desc" => "Announcements & Messenger support",
        "url"  => "https://www.facebook.com/",
        "icon" => "fa-brands fa-facebook"
    ],
    [
        "name" => "Instagram",
        "desc" => "DM support & promotions",
        "url"  => "https://www.instagram.com/",
        "icon" => "fa-brands fa-instagram"
    ],
    [
        "name" => "WhatsApp",
        "desc" => "Fast chat support",
        "url"  => "https://wa.me/",
        "icon" => "fa-brands fa-whatsapp"
    ],
    [
        "name" => "Telegram",
        "desc" => "Chat support & updates",
        "url"  => "https://t.me/",
        "icon" => "fa-brands fa-telegram"
    ],
    [
        "name" => "Viber",
        "desc" => "Customer support hotline",
        "url"  => "https://www.viber.com/",
        "icon" => "fa-brands fa-viber"
    ]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact & Support - NinjaVan</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Your CSS -->
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <style>
        body {
            background: #fff;
        }

        .contact-hero {
            border-radius: 18px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .10);
        }

        .contact-hero img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: block;
        }

        .contact-hero .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(0, 0, 0, .55), rgba(0, 0, 0, .15));
            display: flex;
            align-items: center;
            padding: 24px;
        }

        .contact-hero h1 {
            color: #fff;
            font-weight: 800;
            margin: 0;
        }

        .contact-hero p {
            color: rgba(255, 255, 255, .85);
            margin: 8px 0 0;
            max-width: 720px;
        }

        .channel-card {
            border: 1px solid #eee;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
            height: 100%;
            transition: transform .15s ease;
        }

        .channel-card:hover {
            transform: translateY(-2px);
        }

        .channel-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            object-fit: cover;
            background: #f5f5f5;
            border: 1px solid #eee;
        }

        .support-wrap {
            border: 1px solid #eee;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
            padding: 18px;
        }

        /* Star rating */
        .star-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 6px;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            cursor: pointer;
            font-size: 28px;
            line-height: 1;
            color: #cfcfcf;
            user-select: none;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .contact-card {
            background: #fff;
            border-radius: 14px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            text-decoration: none;
            color: #111;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 40px rgba(0, 0, 0, 0.15);
        }

        .contact-card i {
            font-size: 40px;
            color: #e60023;
            /* NinjaVan red */
            margin-bottom: 15px;
        }

        .contact-card h3 {
            margin: 10px 0 5px;
            font-size: 20px;
        }

        .contact-card p {
            font-size: 14px;
            color: #666;
        }

        .star-rating input:checked~label {
            color: #f5b301;
        }

        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #f5b301;
        }

        .note-muted {
            color: #6c757d;
            font-size: .95rem;
        }
    </style>
</head>

<body>

    <!-- NAVBAR (session-aware) -->
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

    <div style="height:80px;"></div>

    <div class="container py-4">

        <!-- HERO -->
        <div class="contact-hero mb-4">
            <img src="../img/contant.webp" alt="Contact banner">
            <div class="overlay">
                <div>
                    <h1>Contact & Customer Support</h1>
                    <p>
                        Reach NinjaVan support via social channels. Logged-in users can also submit a support message
                        and rate the support experience at the bottom of this page.
                    </p>
                </div>
            </div>
        </div>

        <!-- channel cards -->
        <div class="contact-grid">
            <?php foreach ($channels as $c): ?>
                <a href="<?= $c['url'] ?>" target="_blank" class="contact-card">
                    <i class="<?= $c['icon'] ?>"></i>
                    <h3><?= $c['name'] ?></h3>
                    <p><?= $c['desc'] ?></p>
                </a>
            <?php endforeach; ?>
        </div>


        <!-- Decoration / supporting image row -->
        <div class="row g-4 mt-2">
            <div class="col-lg-6">
                <div class="channel-card p-3">
                    <img src="../img/Ninja-Van_1.jpeg" class="w-100" style="border-radius:14px; object-fit:cover; max-height:260px;" alt="Support photo">
                    <div class="pt-3">
                        <div class="fw-bold">Fast Support</div>
                        <div class="note-muted">Our team helps with tracking, delivery issues, and job scheduling questions.</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="channel-card p-3">
                    <img src="../img/van.jfif" class="w-100" style="border-radius:14px; object-fit:cover; max-height:260px;" alt="Van photo">
                    <div class="pt-3">
                        <div class="fw-bold">Delivery Network</div>
                        <div class="note-muted">Your assigned vehicle and site availability are managed in real-time.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support form: only after login -->
        <div class="mt-5">
            <h3 class="fw-bold mb-2">Support Request</h3>
            <?php if (!$isLoggedIn): ?>
                <div class="alert alert-warning">
                    Please login to submit a support request and rating.
                </div>
            <?php else: ?>
                <div class="support-wrap">
                    <form id="supportForm">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message</label>
                            <textarea class="form-control" name="message" id="supportMessage" rows="5"
                                placeholder="Describe your issue (tracking, delivery delay, wrong site/vehicle, etc.)" required></textarea>
                        </div>

                        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">Rating</div>
                                <div class="note-muted">How satisfied are you with the support experience?</div>
                            </div>
                            <div class="star-rating" aria-label="Rating">
                                <input type="radio" name="rating" id="star5" value="5"><label for="star5">★</label>
                                <input type="radio" name="rating" id="star4" value="4"><label for="star4">★</label>
                                <input type="radio" name="rating" id="star3" value="3"><label for="star3">★</label>
                                <input type="radio" name="rating" id="star2" value="2"><label for="star2">★</label>
                                <input type="radio" name="rating" id="star1" value="1"><label for="star1">★</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn text-white" style="background:#E64A19;" type="submit">Send to Support</button>
                            <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('supportForm').reset();">
                                Clear
                            </button>
                        </div>

                        <div id="supportStatus" class="mt-3"></div>
                    </form>
                </div>
            <?php endif; ?>
        </div>

    </div>

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

    <?php if (!$isLoggedIn): ?>
        <?php include 'LoginPage.php'; ?>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
        <?php include 'AddJobModal.php'; ?>
    <?php endif; ?>

    <script src="../java/jquery-3.7.1.js"></script>
    <script src="../java/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function copyText(txt) {
            navigator.clipboard.writeText(txt).then(() => alert("Copied: " + txt));
        }

        <?php if ($isLoggedIn): ?>
            document.getElementById('supportForm').addEventListener('submit', async (e) => {
                e.preventDefault();

                const msg = document.getElementById('supportMessage').value.trim();
                const ratingEl = document.querySelector('input[name="rating"]:checked');
                const rating = ratingEl ? ratingEl.value : "";

                const status = document.getElementById('supportStatus');
                status.innerHTML = `<div class="text-muted">Sending...</div>`;

                try {
                    const form = new FormData();
                    form.append("message", msg);
                    form.append("rating", rating);

                    const res = await fetch("../API/create_support_ticket.php", {
                        method: "POST",
                        body: form
                    });

                    const data = await res.json();
                    if (!data.success) {
                        status.innerHTML = `<div class="alert alert-danger mb-0">${data.message || "Failed."}</div>`;
                        return;
                    }
                    status.innerHTML = `<div class="alert alert-success mb-0">Sent successfully. Ticket ID: ${data.ticket_id}</div>`;
                    document.getElementById('supportForm').reset();
                } catch (err) {
                    status.innerHTML = `<div class="alert alert-danger mb-0">Network error.</div>`;
                }
            });
        <?php endif; ?>
    </script>

</body>

</html>