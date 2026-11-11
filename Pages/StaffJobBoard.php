<?php
session_start();
// Only staff can access this page
$userRole = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'customer';
if ($userRole !== 'staff') {
    header("Location: ../Pages/Home.php");
    exit;
}
$isLoggedIn = isset($_SESSION['user_email']);

// Because this file is in /Pages, API calls use ../API as base path
$API_BASE = "../API";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Staff Job Board - NinjaVan</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="bg-white">

  <!-- NAVBAR (Staff view) -->
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
            <li class="nav-item"><a class="nav-link active" href="DashBoard.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="Tracking.php">Tracking</a></li>
            <li class="nav-item"><a class="nav-link" href="AboutUs.php">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="Contact.php">Contact</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Shipping</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-target="#AddJobModal" data-bs-toggle="modal">Add Job</a></li>
                <?php if ($_SESSION['user_role'] === 'staff'): ?>
                <div>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="StaffJobBoard.php">Staff Job Board</a></li>
                </div>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="vehicle_site.php">Vehicle Site</a></li>
              </ul>
            </li>
          </ul>
          <br><hr>
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
  <br><br><br><br>

  <header class="tracking-hero">
    <div class="container py-4">
      <div class="tracking-hero-card d-flex flex-column flex-lg-row align-items-center justify-content-between">
        <div>
          <h1 class="tracking-title mb-1"><i class="fa-solid fa-user-gear me-2"></i>Staff Job Board</h1>
          <div class="tracking-sub">Outstanding • Completed • Late schedule — grouped by origin site</div>
        </div>
        <img class="tracking-hero-img" src="../img/bg_vehicle.webp" alt="banner">
      </div>
    </div>
  </header>

  <main class="container pb-5">
    <!-- Search and legend -->
    <div class="row g-3 align-items-stretch">
      <div class="col-lg-8">
        <div class="card shadow-sm border-0 p-3">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fa-solid fa-magnifying-glass"></i>
            <div class="fw-semibold">Search Job ID</div>
          </div>
          <div class="d-flex gap-2">
            <input id="jobIdInput" class="form-control" placeholder="e.g. 12" inputmode="numeric">
            <button id="btnFilter" class="btn btn-dark px-3"><i class="fa-solid fa-filter me-1"></i>Filter</button>
            <button id="btnClear" class="btn btn-outline-secondary px-3"><i class="fa-solid fa-eraser me-1"></i>Clear</button>
          </div>
          <small class="text-muted mt-2 d-block">Type a Job ID to filter. Clear to show all.</small>
          <div class="mt-3">
            <label class="form-label small text-muted mb-1">Sort</label>
            <select id="sortOrder" class="form-select w-auto">
              <option value="job_id">Sort by ID</option>
              <option value="weight">Sort by Weight</option>
              <option value="origin_site_id">Sort by Site</option>
              <option value="vehicle_type">Sort by Vehicle</option>
            </select>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card shadow-sm border-0 p-3 h-100">
          <div class="text-muted">Quick legend</div>
          <div class="mt-2 d-flex flex-wrap gap-2">
            <span class="badge rounded-pill text-bg-secondary px-3 py-2">
              <i class="fa-regular fa-hourglass me-1"></i>Pending
            </span>
            <span class="badge rounded-pill text-bg-danger px-3 py-2">
              <i class="fa-regular fa-clock me-1"></i>Outstanding
            </span>
            <span class="badge rounded-pill text-bg-success px-3 py-2">
              <i class="fa-regular fa-circle-check me-1"></i>Completed
            </span>
            <span class="badge rounded-pill text-bg-warning px-3 py-2">
              <i class="fa-solid fa-triangle-exclamation me-1"></i>Late
            </span>
          </div>
          <small class="text-muted mt-3">Pending jobs can be approved, and outstanding/late jobs can be completed.</small>
        </div>
      </div>
    </div>

    <!-- Site accordions -->
    <div class="mt-4">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="mb-0"><i class="fa-solid fa-layer-group me-2"></i>Sites</h4>
        <span id="loadingText" class="text-muted">Loading…</span>
      </div>
      <div class="accordion" id="siteAccordion"></div>
    </div>
  </main>
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
    const API_BASE = "<?= $API_BASE ?>";
    const elAccordion = document.getElementById("siteAccordion");
    const elLoading = document.getElementById("loadingText");
    const elJobId = document.getElementById("jobIdInput");
    const elSort = document.getElementById("sortOrder");

    document.getElementById("btnFilter").addEventListener("click", () => loadBoard(elJobId.value.trim()));
    document.getElementById("btnClear").addEventListener("click", () => {
      elJobId.value = "";
      loadBoard("");
    });
    elJobId.addEventListener("input", () => {
      // Live filter as user types (debounce)
      clearTimeout(window.__t);
      window.__t = setTimeout(() => loadBoard(elJobId.value.trim()), 250);
    });

    function escapeHtml(str) {
      return String(str ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
    }

    function renderJobTable(jobs, tabId, isActive, isLate = false, action = "none") {
      const active = isActive ? "show active" : "";
      if (!jobs.length) {
        return `
          <div class="tab-pane fade ${active}" id="${tabId}">
            <div class="alert ${isLate ? "alert-warning" : "alert-light"} mb-0">
              ${isLate ? "No late jobs for this site." : "No jobs in this section."}
            </div>
          </div>
        `;
      }
      let rows = "";
      jobs.forEach((j, index) => {
        const status = (j.status || "outstanding").toLowerCase();
        const badgeClass = status === "completed" ? "text-bg-success" : "text-bg-danger";
        const badgeText = status === "completed" ? "Completed" : "Outstanding";
        rows += `
          <tr>
            <td class="text-muted">${index + 1}</td>
            <td class="fw-semibold">${escapeHtml(j.job_id)}</td>
            <td>${escapeHtml(j.job_name || "-")}</td>
            <td>${escapeHtml(j.quantity || 1)}</td>
            <td>${escapeHtml(j.weight || 0)}</td>
            <td>${escapeHtml(j.size || 0)}</td>
            <td>${j.is_hazardous == 1 ? "Hazardous" : "Non-hazardous"}</td>
            <td>${escapeHtml(j.vehicle_type || "-")}</td>
            <td class="text-muted">${escapeHtml(j.best_for || "—")}</td>
            <td>
              <button class="btn btn-sm rounded-pill ${status === "completed" ? "btn-success" : "btn-danger"} status-btn"
                      data-job-id="${escapeHtml(j.job_id)}"
                      data-current="${escapeHtml(status)}"
                      ${clickable ? "" : "disabled"}
                      title="Click to toggle">
                ${badgeText}
              </button>
            </td>
          </tr>
        `;
      });
      return `
        <div class="tab-pane fade ${active}" id="${tabId}">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width:60px">#</th>
                  <th>Job ID</th>
                  <th>Job</th>
                  <th>Qty</th>
                  <th>Weight</th>
                  <th>Size</th>
                  <th>Type</th>
                  <th>Vehicle</th>
                  <th>Best for</th>
                  <th style="width:140px">Status</th>
                </tr>
              </thead>
              <tbody>${rows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    function bindStatusButtons() {
      document.querySelectorAll(".status-btn").forEach(btn => {
        btn.addEventListener("click", async () => {
          const jobId = btn.dataset.jobId;
          btn.disabled = true;
          const form = new FormData();
          form.append("job_id", jobId);
          const action = btn.dataset.action || "";
          if (action) form.append("action", action);
          try {
            const res = await fetch(API_BASE + "/update_job_status.php", {
              method: "POST",
              body: form,
              credentials: "same-origin"
            });
            const data = await res.json();
            if (!data.success) {
              alert(data.message || "Failed to update.");
            }
          } catch (e) {
            alert("Network error.");
          } finally {
            // Always reload current view (with same filter) after attempting update
            loadBoard(elJobId.value.trim());
          }
        });
      });
    }

    async function loadBoard(jobId) {
      elLoading.textContent = "Loading…";
      elAccordion.innerHTML = "";

      const url = new URL(API_BASE + "/get_site_job_board.php", window.location.href);
      if (jobId) url.searchParams.set("job_id", jobId);
      let data;
      try {
        const res = await fetch(url.toString(), { credentials: "same-origin" });
        data = await res.json();
      } catch (err) {
        elLoading.textContent = "Error";
        elAccordion.innerHTML = `<div class="alert alert-danger">Failed to load data.</div>`;
        return;
      }

      if (!data.success) {
        elLoading.textContent = "Error";
        elAccordion.innerHTML = `<div class="alert alert-danger">${escapeHtml(data.message || "Server error")}</div>`;
        return;
      }
      elLoading.textContent = "";
      // Render sites accordion
      const sites = data.sites || [];
      if (!sites.length) {
        elAccordion.innerHTML = `<div class="alert alert-info">No sites found.</div>`;
        return;
      }
      let html = "";
      sites.forEach((site, idx) => {
        // __SORTING_SJB__
        const sortKey = (elSort?.value || "job_id");
        const byText = (a,b) => String(a ?? "").localeCompare(String(b ?? ""));
        const byNumDesc = (a,b) => (Number(b ?? 0) - Number(a ?? 0));
        const sorter = (a,b) => {
          if (sortKey === "job_id") return byNumDesc(a.job_id, b.job_id);
          if (sortKey === "weight") return byNumDesc(a.weight, b.weight);
          if (sortKey === "origin_site_id") return byText(site.site_name, site.site_name); // grouped already
          if (sortKey === "vehicle_type") return byText(a.vehicle_type, b.vehicle_type);
          return byNumDesc(a.job_id, b.job_id);
        };
        ["pending","outstanding","late","completed"].forEach(k => {
          if (site.jobs && Array.isArray(site.jobs[k])) site.jobs[k].sort(sorter);
        });

        const id = "site" + site.site_id;
        const c = site.counts || { pending: 0, outstanding: 0, completed: 0, late: 0 };
        html += `
          <div class="accordion-item border-0 shadow-sm mb-3">
            <h2 class="accordion-header" id="h${id}">
              <button class="accordion-button ${idx === 0 ? "" : "collapsed"}" type="button" data-bs-toggle="collapse" data-bs-target="#c${id}">
                <div class="w-100 d-flex align-items-center justify-content-between">
                  <div class="fw-semibold"><i class="fa-solid fa-location-dot me-2"></i>${escapeHtml(site.site_name)}</div>
                  <div class="d-flex gap-2">
                    <span class="badge rounded-pill text-bg-secondary">${c.pending} Pending</span>
                    <span class="badge rounded-pill text-bg-danger">${c.outstanding} Outstanding</span>
                    <span class="badge rounded-pill text-bg-warning">${c.late} Late</span>
                    <span class="badge rounded-pill text-bg-success">${c.completed} Completed</span>
                  </div>
                </div>
              </button>
            </h2>
            <div id="c${id}" class="accordion-collapse collapse ${idx === 0 ? "show" : ""}" data-bs-parent="#siteAccordion">
              <div class="accordion-body">
                <ul class="nav nav-pills mb-3" role="tablist">
                  <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#${id}-pend" type="button"><i class="fa-regular fa-hourglass me-1"></i>Pending</button></li>
                  <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#${id}-out" type="button"><i class="fa-regular fa-clock me-1"></i>Outstanding</button></li>
                  <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#${id}-late" type="button"><i class="fa-solid fa-triangle-exclamation me-1"></i>Late</button></li>
                  <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#${id}-done" type="button"><i class="fa-regular fa-circle-check me-1"></i>Completed</button></li>
                </ul>
                <div class="tab-content">
                  ${renderJobTable(site.jobs?.pending || [], `${id}-pend`, true, false, "approve")}
                  ${renderJobTable(site.jobs?.outstanding || [], `${id}-out`, false, false, "complete")}
                  ${renderJobTable(site.jobs?.late || [], `${id}-late`, false, true, "complete")}
                  ${renderJobTable(site.jobs?.completed || [], `${id}-done`, false, false, "none")}
                </div>
              </div>
            </div>
          </div>
        `;
      });
      elAccordion.innerHTML = html;
      bindStatusButtons();
    }

    // Initial load of job board
    loadBoard("");
  </script>
</body>
</html>
