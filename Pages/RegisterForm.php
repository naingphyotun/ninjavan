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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ninja Van</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
    crossorigin="anonymous" />
  <style>
    body {
      background-color: #f8f9fa;
    }

    .login-container {
      background-color: #e84223;
      min-height: 800px;
      display: flex;
      align-items: center;
      border-radius: 8px;
      padding: 20px;
    }

    .login-card {
      background-color: #f8f9fa;
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      border: 1px solid #000;
    }

    .form-control {
      background-color: white;
      border: 1px solid #000;
      color: black;
      border-radius: 10px;
      height: 50px;
    }

    .btn-custom {
      border: 1px solid #000;
      background-color: white;
      color: black;
      font-weight: bold;
      padding: 5px 20px;
    }

    .top-bar {
      background-color: #e84223;
      width: 100%;
      margin-bottom: 20px;
    }
  </style>
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
              <a class="nav-link" href="AboutUs.php">About Us</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="Contact.php">Contact</a>
            </li>
        </div>
      </div>
    </div>
  </nav><br><br>
  <div class="container py-4">

    <div class="login-container">
      <div class="container-fluid">
        <div class="row">
          <div class="col">
            <div class="login-card">
              <form class="row g-3" action="../API/RegisterProcess.php" id="ajaxRegisterForm">
                <div class="col-md-6">
                  <label for="F-name" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="F-name" name="f_name" require />
                </div>
                <div class="col-md-6">
                  <label for="L-name" class="form-label">Last Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="L-name"
                    name="s_name" />
                </div>
                <div class="col-12">
                  <label for="inputEmail4" class="form-label">Email</label>
                  <input type="email" class="form-control" id="inputEmail4" name="email" require />
                  <div id="regEmailError" class="text-danger"></></div>
                </div>
                <div class="col-md-6">
                  <label for="inputPassword" class="form-label">Password</label>
                  <input
                    type="password"
                    class="form-control"
                    id="inputPassword"
                    name="password"
                    require />
                </div>
                <div class="col-md-6">
                  <label for="C-inputPassword" class="form-label">Comfirm Password</label>
                  <input
                    type="password"
                    class="form-control"
                    id="C-inputPassword"
                    name="confirm_password"
                    require />
                  <div id="regPassError" class="text-danger"></div>
                </div>
                <div class="col-12">
                  <label for="inputAddress" class="form-label">Address</label>
                  <input
                    type="text"
                    class="form-control"
                    id="inputAddress"
                    placeholder="1234 Main St"
                    name="address1"
                    require />
                </div>
                <div class="col-12">
                  <label for="inputAddress2" class="form-label">Address 2</label>
                  <input
                    type="text"
                    class="form-control"
                    id="inputAddress2"
                    placeholder="Apartment, studio, or floor"
                    name="address2" />
                </div>
                <div class="col-md-6">
                  <label for="inputCity" class="form-label">City</label>
                  <input type="text" class="form-control" id="inputCity" name="city" require />
                </div>
                <div class="col-md-4">
                  <label for="inputState" class="form-label">State</label>
                  <input type="text" id="inputState" class="form-control" name="state" require>
                </div>
                <div class="col-md-2">
                  <label for="inputZip" class="form-label">Zip</label>
                  <input type="text" class="form-control" id="inputZip" name="zip" require />
                </div>
                <div class="col-12 d-flex justify-content-between align-items-center">
                  <button type="submit" class="btn btn-outline-dark">
                    Register
                  </button>
                  <small class="text-end">Already Register?<br /><a href="LoginPage.php" class="text-dark" data-bs-toggle="modal"
                      data-bs-target="#LoginModal">/Login</a></small>
                </div>
              </form>
            </div>
          </div>
        </div>
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

    <script src="../java/jquery-3.7.1.js"></script>
    <script src="../java/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>