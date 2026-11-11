<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    // If not logged in, redirect (adjust the location as needed in your project)
    header("Location: Home.php");
    exit();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_email']);
$userName   = $_SESSION['user_name'] ?? '';
$userRole   = $_SESSION['user_role'] ?? '';
$homeLink   = $isLoggedIn ? "DashBoard.php" : "Home.php";
require_once "../API/dbinfo.php";  // include database connection (adjust path if needed)

// Prepare SQL to fetch vehicle inventory grouped by site and type
$sql = "SELECT s.site_id, s.site_name,s.image_url AS site_image,
               svi.vehicle_type,
               SUM(svi.available_count) AS available_count,
               SUM(svi.total_count) AS total_count,
               MAX(svi.max_weight) AS max_weight,
               MAX(svi.max_space) AS max_space,
               MAX(svi.image_url) AS image_url
        FROM site_vehicle_inventory AS svi
        JOIN sites AS s ON s.site_id = svi.site_id
        GROUP BY s.site_id, s.site_name, svi.vehicle_type
        ORDER BY s.site_name,
         CASE 
           WHEN svi.vehicle_type='Bicycle' THEN 1
           WHEN svi.vehicle_type='Bike' THEN 2
           WHEN svi.vehicle_type='Van' THEN 3
           WHEN svi.vehicle_type='Truck' THEN 4
           ELSE 5
         END";
$result = $conn->query($sql);

// Organize data by site for easier display
$sites = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $siteId = $row['site_id'];
        $siteName = $row['site_name'];
        if (!isset($sites[$siteId])) {
            $sites[$siteId] = [
                'name' => $siteName,
                'image' => $row['site_image'],
                'vehicles' => []
            ];
        }
        // Add vehicle type info to this site’s list
        $sites[$siteId]['vehicles'][] = [
            'type' => $row['vehicle_type'],
            'available' => $row['available_count'],
            'total' => $row['total_count'],
            'max_weight' => $row['max_weight'],
            'max_space' => $row['max_space'],
            'image' => $row['image_url']
        ];
    }
}

// If staff, load raw inventory rows for editing
$inventoryRows = [];
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'staff') {
    $invSql = "SELECT svi.inventory_id, svi.site_id, s.site_name, svi.vehicle_type,
                      svi.available_count, svi.total_count, svi.max_weight, svi.max_space,
                      svi.hazmat_capable, svi.image_url
               FROM site_vehicle_inventory svi
               JOIN sites s ON s.site_id = svi.site_id
               ORDER BY s.site_name ASC, svi.vehicle_type ASC, svi.inventory_id ASC";
    $invRes = $conn->query($invSql);
    if ($invRes) {
        while ($r = $invRes->fetch_assoc()) { $inventoryRows[] = $r; }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vehicle Inventory by Site</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Background banner styling */
        .banner {
            background: url('../img/bg_vehicle.webp') center center no-repeat;
            background-size: cover;
            color: #fff;
        }
    </style>
    <link rel="stylesheet" href="../CSS/style.css">
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
    </nav><br><br><br><br>

    <!-- Banner Section with Background Image -->
    <section class="banner d-flex align-items-center text-dark">
        <div class="container text-center py-5">
            <h1 class="inventory-title">Vehicle Inventory by Site</h1>
            <p class="inventory-subtitle">
                Real-time availability of vehicles across all UK sites
            </p>
        </div>
    
</section>

<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'staff'): ?>
  <div class="container my-4">
    <div class="p-4 bg-white border rounded-4 shadow-sm">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
          <h4 class="mb-1"><i class="fa-solid fa-screwdriver-wrench me-2"></i>Inventory Management</h4>
          <div class="text-muted small">Staff can add sites and edit vehicle inventory (counts / move to another site).</div>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#AddSiteModal">
            <i class="fa-solid fa-location-plus me-1"></i>Add Site
          </button>
          <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#AddInventoryModal">
            <i class="fa-solid fa-plus me-1"></i>Add Inventory
          </button>
        </div>
      </div>

      <hr class="my-3">

      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Site</th>
              <th>Vehicle</th>
              <th>Available</th>
              <th>Total</th>
              <th>Max Weight</th>
              <th>Max Space</th>
              <th>Hazmat</th>
              <th>Image</th>
              <th style="width:120px">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($inventoryRows as $r): ?>
              <tr>
                <td><?= (int)$r['inventory_id'] ?></td>
                <td><?= htmlspecialchars($r['site_name']) ?></td>
                <td><?= htmlspecialchars($r['vehicle_type']) ?></td>
                <td><?= (int)$r['available_count'] ?></td>
                <td><?= (int)$r['total_count'] ?></td>
                <td><?= (int)$r['max_weight'] ?></td>
                <td><?= (int)$r['max_space'] ?></td>
                <td><?= ((int)$r['hazmat_capable'] === 1) ? "Yes" : "No" ?></td>
                <td>
                  <?php if (!empty($r['image_url'])): ?>
                    <img src="<?= htmlspecialchars($r['image_url']) ?>" style="height:38px;width:auto" alt="img">
                  <?php else: ?>
                    <span class="text-muted">—</span>
                  <?php endif; ?>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary editInvBtn"
                    data-bs-toggle="modal" data-bs-target="#EditInventoryModal"
                    data-inventory-id="<?= (int)$r['inventory_id'] ?>"
                    data-site-id="<?= (int)$r['site_id'] ?>"
                    data-vehicle-type="<?= htmlspecialchars($r['vehicle_type']) ?>"
                    data-available="<?= (int)$r['available_count'] ?>"
                    data-total="<?= (int)$r['total_count'] ?>"
                    data-max-weight="<?= (int)$r['max_weight'] ?>"
                    data-max-space="<?= (int)$r['max_space'] ?>"
                    data-hazmat="<?= (int)$r['hazmat_capable'] ?>"
                    data-image-url="<?= htmlspecialchars($r['image_url'] ?? '') ?>"
                  >
                    Edit
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($inventoryRows)): ?>
              <tr><td colspan="10" class="text-muted">No inventory rows found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div id="invMsg" class="small mt-2"></div>
    </div>
  </div>

  <!-- Add Site Modal -->
  <div class="modal fade" id="AddSiteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="addSiteForm">
        <div class="modal-header">
          <h5 class="modal-title">Add Site</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">Site Name</label>
          <input class="form-control" name="site_name" required>
          <label class="form-label mt-3">Image URL (optional)</label>
          <input class="form-control" name="image_url" placeholder="../img/sites/london.jpg">
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-dark" type="submit">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Inventory Modal -->
  <div class="modal fade" id="AddInventoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form class="modal-content" id="addInvForm">
        <div class="modal-header">
          <h5 class="modal-title">Add Inventory</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Site</label>
              <select class="form-select" name="site_id" id="invSiteSelect" required></select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Vehicle Type</label>
              <input class="form-control" name="vehicle_type" placeholder="Van" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Total Count</label>
              <input class="form-control" type="number" name="total_count" min="0" value="1" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Available Count</label>
              <input class="form-control" type="number" name="available_count" min="0" value="1" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Max Weight (kg)</label>
              <input class="form-control" type="number" name="max_weight" min="0" value="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Max Space</label>
              <input class="form-control" type="number" name="max_space" min="0" value="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Hazmat Capable</label>
              <select class="form-select" name="hazmat_capable">
                <option value="0">No</option>
                <option value="1">Yes</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Image URL (optional)</label>
              <input class="form-control" name="image_url" placeholder="../img/van.jfif">
            </div>
          </div>
          <div class="small text-muted mt-2">Tip: Available Count must be ≤ Total Count.</div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-dark" type="submit">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Inventory Modal -->
  <div class="modal fade" id="EditInventoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form class="modal-content" id="editInvForm">
        <div class="modal-header">
          <h5 class="modal-title">Edit Inventory Row</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="inventory_id" id="editInventoryId">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Site</label>
              <select class="form-select" name="site_id" id="editSiteSelect" required></select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Vehicle Type</label>
              <input class="form-control" name="vehicle_type" id="editVehicleType" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Total Count</label>
              <input class="form-control" type="number" name="total_count" id="editTotal" min="0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Available Count</label>
              <input class="form-control" type="number" name="available_count" id="editAvailable" min="0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Max Weight (kg)</label>
              <input class="form-control" type="number" name="max_weight" id="editMaxWeight" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Max Space</label>
              <input class="form-control" type="number" name="max_space" id="editMaxSpace" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Hazmat Capable</label>
              <select class="form-select" name="hazmat_capable" id="editHazmat">
                <option value="0">No</option>
                <option value="1">Yes</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Image URL (optional)</label>
              <input class="form-control" name="image_url" id="editImageUrl">
            </div>
          </div>
          <div class="small text-muted mt-2">Changing Site will move this inventory row to another site.</div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>

    <!-- Main Content: Site Inventory Cards -->

    <div class="container my-5">

        <?php foreach ($sites as $site): ?>

            <div class="card site-card mb-4 shadow">
                <div class="card-body">

                    <!-- Site list-style header -->
                    <div class="site-header">
                        <div class="site-thumb">
                            <img src="<?php echo htmlspecialchars($site['image'] ?? '../img/sites/default.jpg'); ?>"
                                alt="<?php echo htmlspecialchars($site['name']); ?>">
                        </div>
                        <div class="site-meta">
                            <div class="site-title"><?php echo htmlspecialchars($site['name']); ?></div>
                            <div class="site-sub">Real-time vehicle availability</div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Vehicle grid -->
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                        <?php foreach ($site['vehicles'] as $veh): ?>
                            <div class="col text-center">
                                <img src="<?php echo htmlspecialchars($veh['image']); ?>"
                                    alt="<?php echo htmlspecialchars($veh['type']); ?> icon"
                                    class="vehicle-img mb-2" />

                                <h6 class="vehicle-title"><?php echo htmlspecialchars($veh['type']); ?></h6>

                                <p class="vehicle-info mb-1">
                                    Available: <strong><?php echo (int)$veh['available']; ?></strong>
                                    <?php if (!empty($veh['total'])): ?>
                                        / <?php echo (int)$veh['total']; ?>
                                    <?php endif; ?>
                                </p>

                                <p class="vehicle-info mb-0">
                                    Capacity: <?php echo (int)$veh['max_weight']; ?> kg /
                                    <?php echo (int)$veh['max_space']; ?> m<sup>3</sup>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>

        <?php endforeach; ?>

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

    
<script>
  const API_BASE = "../API/";
  const invMsg = document.getElementById("invMsg");

  async function loadSitesInto(selectEl) {
    if (!selectEl) return;
    selectEl.innerHTML = "";
    try {
      const res = await fetch(API_BASE + "get_sites.php", { credentials: "same-origin" });
      const data = await res.json();
      if (!data.success) return;
      data.data.forEach(s => {
        const opt = document.createElement("option");
        opt.value = s.site_id;
        opt.textContent = s.site_name;
        selectEl.appendChild(opt);
      });
    } catch (e) {}
  }

  // Populate selects (staff only)
  document.addEventListener("DOMContentLoaded", () => {
    const invSite = document.getElementById("invSiteSelect");
    const editSite = document.getElementById("editSiteSelect");
    loadSitesInto(invSite);
    loadSitesInto(editSite);

    // Prefill edit modal
    document.querySelectorAll(".editInvBtn").forEach(btn => {
      btn.addEventListener("click", () => {
        document.getElementById("editInventoryId").value = btn.dataset.inventoryId;
        document.getElementById("editVehicleType").value = btn.dataset.vehicleType;
        document.getElementById("editAvailable").value = btn.dataset.available;
        document.getElementById("editTotal").value = btn.dataset.total;
        document.getElementById("editMaxWeight").value = btn.dataset.maxWeight;
        document.getElementById("editMaxSpace").value = btn.dataset.maxSpace;
        document.getElementById("editHazmat").value = btn.dataset.hazmat;
        document.getElementById("editImageUrl").value = btn.dataset.imageUrl || "";
        // set site after sites loaded
        setTimeout(() => { if (editSite) editSite.value = btn.dataset.siteId; }, 150);
      });
    });

    // Add site
    const addSiteForm = document.getElementById("addSiteForm");
    addSiteForm?.addEventListener("submit", async (e) => {
      e.preventDefault();
      invMsg.textContent = "";
      const form = new FormData(addSiteForm);
      const res = await fetch(API_BASE + "add_site.php", { method:"POST", body: form, credentials:"same-origin" });
      const out = await res.json();
      if (!out.success) {
        invMsg.className = "small text-danger mt-2";
        invMsg.textContent = out.message || "Failed to add site.";
        return;
      }
      invMsg.className = "small text-success mt-2";
      invMsg.textContent = "Site added. Reloading…";
      setTimeout(() => location.reload(), 600);
    });

    // Add inventory
    const addInvForm = document.getElementById("addInvForm");
    addInvForm?.addEventListener("submit", async (e) => {
      e.preventDefault();
      invMsg.textContent = "";
      const form = new FormData(addInvForm);
      const res = await fetch(API_BASE + "update_vehicle_inventory.php", { method:"POST", body: form, credentials:"same-origin" });
      const out = await res.json();
      if (!out.success) {
        invMsg.className = "small text-danger mt-2";
        invMsg.textContent = out.message || "Failed to add inventory.";
        return;
      }
      invMsg.className = "small text-success mt-2";
      invMsg.textContent = "Inventory added. Reloading…";
      setTimeout(() => location.reload(), 600);
    });

    // Edit inventory
    const editInvForm = document.getElementById("editInvForm");
    editInvForm?.addEventListener("submit", async (e) => {
      e.preventDefault();
      invMsg.textContent = "";
      const form = new FormData(editInvForm);
      const res = await fetch(API_BASE + "update_vehicle_inventory.php", { method:"POST", body: form, credentials:"same-origin" });
      const out = await res.json();
      if (!out.success) {
        invMsg.className = "small text-danger mt-2";
        invMsg.textContent = out.message || "Failed to update inventory.";
        return;
      }
      invMsg.className = "small text-success mt-2";
      invMsg.textContent = "Saved. Reloading…";
      setTimeout(() => location.reload(), 600);
    });
  });
</script>

  <script src="../java/jquery-3.7.1.js"></script>
    <script src="../java/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>