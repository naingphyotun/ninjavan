<?php
session_start();
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$isLoggedIn = isset($_SESSION['user_email']);
$homeLink   = $isLoggedIn ? "DashBoard.php" : "Home.php";
// Must be logged in to access Tracking
if (!isset($_SESSION['user_email'])) {
  header("Location: ../Pages/LoginPage.php");
  exit;
}

// Determine user role from session (default to 'customer'); set staff flag
$userEmail = $_SESSION['user_email'] ?? $_SESSION['email'] ?? '';
$userRole  = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'customer';
$isStaff   = ($userRole === 'staff');
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tracking | NinjaVan Logistics</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../CSS/style.css">

  <style>
    body {
      background: #fff;
    }

    .hero {
      border-radius: 18px;
      overflow: hidden;
      background: #f6f7f9;
      box-shadow: 0 18px 40px rgba(0, 0, 0, .08);
    }

    .hero .left {
      padding: 28px 28px 20px 28px;
    }

    .hero-title {
      font-weight: 800;
      letter-spacing: .2px;
    }

    .hero-sub {
      color: rgba(0, 0, 0, .65);
    }

    .hero-img {
      height: 140px;
      background: url("../img/bg_vehicle.webp") right center / cover no-repeat;
      position: relative;
    }

    .hero-img::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, rgba(246, 247, 249, 1) 0%, rgba(246, 247, 249, .55) 40%, rgba(246, 247, 249, 0) 70%);
    }

    .pill {
      border-radius: 999px;
      font-weight: 700;
      padding: .45rem .85rem;
      border: 1px solid transparent;
      cursor: default;
      display: inline-flex;
      align-items: center;
      gap: .4rem;
    }

    .pill-pending{background:#f1f3f5;color:#495057;border:1px solid #dee2e6;}
    .pill-pending:hover{filter:brightness(0.98);}
    .pill-outstanding {
      background: #ffe5e5;
      color: #b30000;
      border-color: #ffb3b3;
    }

    .pill-completed {
      background: #e7f7ec;
      color: #0f6a2a;
      border-color: #bfe7cb;
    }

    .staff-toggle {
      cursor: pointer;
      transition: transform .08s ease;
    }

    .staff-toggle:active {
      transform: scale(.98);
    }

    .job-card {
      border: 0;
      border-radius: 16px;
      box-shadow: 0 14px 35px rgba(0, 0, 0, .08);
      overflow: hidden;
    }

    .job-meta {
      color: rgba(0, 0, 0, .60);
      font-size: .92rem;
    }

    .vehicle-chip {
      border-radius: 999px;
      padding: .25rem .6rem;
      font-weight: 700;
      background: #f2f4f8;
      display: inline-flex;
      align-items: center;
      gap: .45rem;
    }

    .vehicle-img {
      width: 46px;
      height: 46px;
      object-fit: cover;
      border-radius: 12px;
      border: 1px solid rgba(0, 0, 0, .08);
      background: #fff;
    }

    .table-wrap {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 14px 35px rgba(0, 0, 0, .06);
      overflow: hidden;
    }

    .table thead th {
      background: #f8fafc;
    }

    .small-help {
      color: rgba(0, 0, 0, .55);
      font-size: .9rem;
    }
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar bg-body-tertiary fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><img src="../img/Logo-ninjaVan.png" style="height:45px; width:auto;" alt="Logo"></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">
        <div class="offcanvas-header">
          <img src="../img/Logo-ninjaVan.png" style="height:45px; width:auto;" alt="Logo">
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="DashBoard.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="Tracking.php">Tracking</a></li>
            <li class="nav-item"><a class="nav-link" href="AboutUs.php">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="Contact.php">Contact</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Shipping</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-target="#AddJobModal" data-bs-toggle="modal">Add Job</a></li>
                <?php if ($_SESSION['user_role'] === 'staff'): ?>
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
                <li><a class="dropdown-item" href="vehicle_site.php">Vehicle Site</a></li>
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
                    <span class="fw-bold d-block text-dark"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <small class="text-muted"><?= ucfirst($_SESSION['user_role']) ?></small>
                  </div>
                </div>
                <a href="../API/logout.php" class="btn btn-outline-danger w-100 mt-2">Logout</a>
              <?php else: ?>
                <button type="button" class="btn text-white" style="background-color:#E64A19;" data-bs-toggle="modal" data-bs-target="#LoginModal">Login</button>
                <a href="RegisterForm.php" class="btn btn-outline-dark">Register</a>
              <?php endif; ?>
            <?php else: ?>
              <div class="d-flex flex-row gap-2 ms-lg-auto pt-3 pt-lg-0">
                <button type="button" class="btn text-white fixed-width-btn" style="background-color:#E64A19;" id="openLogin" data-bs-toggle="modal" data-bs-target="#LoginModal">Login</button>
                <a href="RegisterForm.php" class="btn btn-outline-dark fixed-width-btn">Register</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </nav>
  <!-- Spacing to offset fixed navbar -->
  <br><br><br><br>

  <div class="container my-4">
    <!-- HERO Section -->
    <div class="hero mb-4">
      <div class="row g-0 align-items-stretch">
        <div class="col-lg-7">
          <div class="left">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div>
                <h2 class="hero-title mb-1"><i class="fa-solid fa-truck-fast me-2"></i>Tracking</h2>
                <div class="hero-sub">Search by Job ID • Live status updates • Latest jobs shown first</div>
              </div>
              <div class="text-end">
                <span class="badge text-bg-dark rounded-pill px-3 py-2">
                  <?= $isStaff ? "Staff View" : "Customer View" ?>
                </span>
                <div class="small-help mt-1"><?= htmlspecialchars($userEmail) ?></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="hero-img"></div>
        </div>
      </div>
    </div>

    <!-- Search and Summary -->
    <div class="row g-3 align-items-stretch mb-4">
      <div class="col-lg-7">
        <div class="p-4 bg-white border rounded-4 shadow-sm h-100">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fa-solid fa-magnifying-glass"></i>
            <div class="fw-bold">Search Job</div>
          </div>
          <div class="small-help mb-3">Type a Job ID to filter. Clear to show all.</div>
          <div class="d-flex gap-2 flex-wrap">
            <input id="jobIdInput" class="form-control form-control-lg" style="max-width:340px" type="number" min="1" placeholder="e.g. 12">
            <button id="btnFilter" class="btn btn-dark btn-lg"><i class="fa-solid fa-filter me-2"></i>Filter</button>
            <button id="btnClear" class="btn btn-outline-secondary btn-lg"><i class="fa-solid fa-eraser me-2"></i>Clear</button>
          </div><br>
          <select id="sortOrder" class="form-select w-auto d-inline-block">
            <option value="job_id">Sort by ID</option>
            <option value="weight">Sort by Weight</option>
            <option value="origin_site_id">Sort by Site</option>
            <option value="vehicle_type">Sort by Vehicle</option>
          </select>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="p-4 bg-white border rounded-4 shadow-sm h-100">
          <div class="text-muted">Showing</div>
          <div class="display-6 fw-bold" id="countText">0 job(s)</div>
          <div class="mt-2">
            <span class="text-muted">View:</span>
            <span class="fw-bold"><?= $isStaff ? "Staff" : "Customer" ?></span>
          </div>
          <hr>
          <div class="small-help mb-2"><i class="fa-solid fa-circle-info me-2"></i>Status badges:</div>
          <div class="d-flex gap-2 flex-wrap">
            <span class="pill pill-pending"><i class="fa-regular fa-hourglass"></i>Pending</span>
            <span class="pill pill-outstanding"><i class="fa-regular fa-clock"></i>Outstanding</span>
            <span class="pill pill-completed"><i class="fa-regular fa-circle-check"></i>Completed</span>
          </div>
          <?php if ($isStaff): ?>
            <div class="small-help mt-3"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Staff can click a status badge to toggle it.</div>
          <?php else: ?>
            <div class="small-help mt-3"><i class="fa-solid fa-lock me-2"></i>Customers can view status updates only.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Job List Table -->
    <div class="table-wrap p-3 p-md-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <div class="fw-bold fs-4"><i class="fa-solid fa-list-check me-2"></i><?= $isStaff ? "All Jobs" : "My Jobs" ?></div>
          <div class="text-muted">Sorted by <b>Job ID DESC</b></div>
        </div>
        <div class="text-muted small">
          <i class="fa-solid fa-arrow-down-1-9 me-1"></i>Newest first
        </div>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:60px">#</th>
              <th style="width:90px">Job ID</th>
              <th>Job</th>
              <th style="width:70px">Qty</th>
              <th style="width:90px">Weight</th>
              <th style="width:90px">Size</th>
              <th style="width:140px">Type</th>
              <th style="width:140px">Vehicle</th>
              <th>Best for</th>
              <th style="width:160px">Status</th>
            </tr>
          </thead>
          <tbody id="jobsBody">
            <tr>
              <td colspan="10" class="text-muted">Loading...</td>
            </tr>
          </tbody>
        </table>
      </div>
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

  <script>
    const API_BASE = "../API/";
    const isStaff = <?= $isStaff ? "true" : "false" ?>;

    const $jobIdInput = document.getElementById("jobIdInput");
    const $btnFilter = document.getElementById("btnFilter");
    const $btnClear = document.getElementById("btnClear");
    const $jobsBody = document.getElementById("jobsBody");
    const $countText = document.getElementById("countText");
    const $sortOrder = document.getElementById("sortOrder");

    function escapeHtml(str) {
      return String(str ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
    }

    function vehicleIcon(type) {
      const t = (type || "").toLowerCase();
      if (t === "truck") return `<i class="fa-solid fa-truck-front"></i>`;
      if (t === "van") return `<i class="fa-solid fa-van-shuttle"></i>`;
      if (t === "bike") return `<i class="fa-solid fa-motorcycle"></i>`;
      return `<i class="fa-solid fa-bicycle"></i>`;
    }

    function statusPill(status, jobId) {

  // Staff actions
  // Pending -> Approve
  // Outstanding -> Complete
  // Completed -> locked

  if (status === "pending") {
    return `
      <button class="pill pill-pending staff-action"
        data-job-id="${jobId}"
        data-action="approve"
        type="button">
        <i class="fa-solid fa-check"></i> Approve
      </button>
    `;
  }

  if (status === "outstanding") {
    return `
      <button class="pill pill-outstanding staff-action"
        data-job-id="${jobId}"
        data-action="complete"
        type="button">
        <i class="fa-regular fa-circle-check"></i> Complete
      </button>
    `;
  }

  return `
    <span class="pill pill-completed">
      <i class="fa-regular fa-circle-check"></i> Completed
    </span>
  `;
}


    async function loadJobs(jobId = "") {
      $jobsBody.innerHTML = `<tr><td colspan="10" class="text-muted">Loading...</td></tr>`;

      const url = API_BASE + "get_jobs.php" + (jobId ? ("?job_id=" + encodeURIComponent(jobId)) : "");
      let data;
      try {
        const res = await fetch(url);
        data = await res.json();
      } catch (e) {
        $jobsBody.innerHTML = `<tr><td colspan="10" class="text-danger">Failed to load data.</td></tr>`;
        $countText.textContent = "0 job(s)";
        return;
      }

      if (!data.success) {
        $jobsBody.innerHTML = `<tr><td colspan="10" class="text-danger">${escapeHtml(data.message || "Failed to load")}</td></tr>`;
        $countText.textContent = "0 job(s)";
        return;
      }
      const jobs = data.jobs || [];
      // __SORTING_ADDED__
      const sortKey = ($sortOrder?.value || "job_id");
      const byText = (a,b) => String(a ?? "").localeCompare(String(b ?? ""));
      const byNumDesc = (a,b) => (Number(b ?? 0) - Number(a ?? 0));

      jobs.sort((a,b) => {
        if (sortKey === "job_id") return byNumDesc(a.job_id, b.job_id); // newest first
        if (sortKey === "weight") return byNumDesc(a.weight, b.weight);
        if (sortKey === "origin_site_id") {
          return byText(a.origin_site || a.origin_site_id, b.origin_site || b.origin_site_id);
        }
        if (sortKey === "vehicle_type") {
          return byText(a.vehicle_type, b.vehicle_type);
        }
        return byNumDesc(a.job_id, b.job_id);
      });

      $countText.textContent = `${jobs.length} job(s)`;

      if (!jobs.length) {
        $jobsBody.innerHTML = `<tr><td colspan="10" class="text-muted">No jobs found.</td></tr>`;
        return;
      }
      // Render each job row
      $jobsBody.innerHTML = jobs.map((j, idx) => {
        const typeText = j.is_hazardous == 1 ? "Hazardous" : "Non-hazardous";
        const vehicleType = j.vehicle_type || "-";
        const bestFor = j.best_for || "—";
        const img = j.vehicle_image ? `<img class="vehicle-img me-2" src="${escapeHtml(j.vehicle_image)}" alt="">` : "";
        return `
          <tr>
            <td class="fw-bold">${idx + 1}</td>
            <td>${escapeHtml(j.job_id)}</td>
            <td>
              <div class="fw-bold">${escapeHtml(j.job_name || "-")}</div>
              <div class="job-meta">
                <i class="fa-solid fa-location-dot me-1"></i>${escapeHtml(j.origin_site || j.origin_city || "-")}
                <i class="fa-solid fa-arrow-right-long mx-2"></i>
                ${escapeHtml(j.destination_site || j.destination_city || "-")}
              </div>
            </td>
            <td>${escapeHtml(j.quantity)}</td>
            <td>${escapeHtml(j.weight)} kg</td>
            <td>${escapeHtml(j.size)}</td>
            <td><span class="badge text-bg-light border">${escapeHtml(typeText)}</span></td>
            <td>
              <span class="vehicle-chip">
                ${img}${vehicleIcon(vehicleType)} ${escapeHtml(vehicleType)}
              </span>
            </td>
            <td>${escapeHtml(bestFor)}</td>
            <td>${statusPill(j.status, j.job_id)}</td>
          </tr>
        `;
      }).join("");

      // Bind status toggle handler for staff users
      if (isStaff) {
        document.querySelectorAll(".staff-action").forEach(btn => {
          btn.addEventListener("click", async () => {
            const jobId = btn.getAttribute("data-job-id");
            const action = btn.getAttribute("data-action");
            btn.disabled = true;
            try {
              const form = new FormData();
              form.append("job_id", jobId);
              form.append("action", action);
              const r = await fetch(API_BASE + "update_job_status.php", {
                method: "POST",
                body: form
              });
              const out = await r.json();
              if (!out.success) {
                alert(out.message || "Update failed");
              } else {
                // Reload jobs after successful update
                await loadJobs($jobIdInput.value.trim());
              }
            } catch (e) {
              alert("Network error");
            } finally {
              btn.disabled = false;
            }
          });
        });
      }
    }

    // Filter controls
    $btnFilter.addEventListener("click", () => loadJobs($jobIdInput.value.trim()));
    $btnClear.addEventListener("click", () => {
      $jobIdInput.value = "";
      loadJobs("");
    });
    $sortOrder.addEventListener("change", () => loadJobs($jobIdInput.value.trim()));

    // Live filter as user types (debounced)
    let typingTimer = null;
    $jobIdInput.addEventListener("input", () => {
      clearTimeout(typingTimer);
      typingTimer = setTimeout(() => loadJobs($jobIdInput.value.trim()), 250);
    });
    // Initial load
    loadJobs("");
  </script>

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