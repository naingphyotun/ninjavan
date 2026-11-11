$(document).ready(function () {
  // Login Form Handler
  $("#ajaxLoginForm").on("submit", function (e) {
    e.preventDefault();
    var formData = {
      login_email: $("#emailInput").val(),
      login_password: $("#passInput").val(),
    };
    $("#emailAjaxError, #passAjaxError").text("");

    $.ajax({
      url: "../API/loginForm.php",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          window.location.href = "../Pages/DashBoard.php";
        } else {
          if (response.type === "email") {
            $("#emailAjaxError").text(response.message);
          } else if (response.type === "password") {
            $("#passAjaxError").text(response.message);
          } else {
            alert(response.message);
          }
        }
      },
      error: function () {
        alert("Critical Error: Could not connect to the login server.");
      },
    });
  });

  // Register Form Handler
  $("#ajaxRegisterForm").on("submit", function (e) {
    e.preventDefault();
    $("#regEmailError, #regPassError").text("");

    $.ajax({
      url: "../API/RegisterProcess.php",
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          window.location.href = "../Pages/RegistrationComplete.php";
        } else {
          if (response.type === "email") {
            $("#regEmailError").text(response.message);
            $("html, body").animate({ scrollTop: 0 }, "slow");
          } else if (response.type === "password") {
            $("#regPassError").text(response.message);
            $("html, body").animate({ scrollTop: 0 }, "slow");
          } else {
            alert("Error: " + response.message);
          }
        }
      },
      error: function () {
        alert("Critical System Error: Could not reach the server.");
      },
    });
  });

  // ===== Add Job Modal Logic =====

  const API_BASE = "../API/";

  function escapeHtml(str) {
    return String(str ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function buildVehicleCarousel(vehicles) {
    if (!vehicles || vehicles.length === 0) {
      return `<div class="alert alert-warning mb-0">
      No suitable vehicles found for this origin site and cargo requirements.
    </div>`;
    }

    const inner = vehicles
      .map((v, idx) => {
        const active = idx === 0 ? "active" : "";
        const img = escapeHtml(v.image_url || "../img/van.jfif");
        const type = escapeHtml(v.vehicle_type);
        const maxW = escapeHtml(v.max_weight);
        const maxS = escapeHtml(v.max_space);
        const available = escapeHtml(v.available_count);
        const invId = escapeHtml(v.inventory_id);

        return `
      <div class="carousel-item ${active}">
        <div class="card border-0 shadow-sm">
          <img src="${img}" class="d-block w-100"
               style="max-height:260px;object-fit:contain;background:#fafafa;"
               alt="${type}">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h5 class="card-title mb-1">${type}</h5>
                <p class="card-text mb-2 text-muted">
                  Capacity: <b>${maxW}</b> kg • Space: <b>${maxS}</b>
                  ${String(v.hazmat_capable) === "1" ? " • Hazmat OK" : ""}
                  • Available: <b>${available}</b>
                </p>
              </div>

              <button type="button"
                      class="btn btn-sm btn-outline-dark selectVehicleBtn"
                      style="position:relative; z-index:10;" 
                      data-inventory-id="${invId}"
                      data-vehicle-type="${type}">
                Select
              </button>
            </div>
            <small class="text-muted">Slide next/previous to compare vehicles.</small>
          </div>
        </div>
      </div>
    `;
      })
      .join("");

    return `
    <div id="vehicleCarousel" class="carousel slide" data-bs-ride="false">
      <div class="carousel-inner">${inner}</div>
      <button class="carousel-control-prev" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  `;
  }

  function loadSitesIntoSelects() {
    return $.getJSON(API_BASE + "get_sites.php")
      .done(function (res) {
        if (!res || !res.success) return;

        const opts = res.data
          .map(
            (s) =>
              `<option value="${escapeHtml(s.site_id)}">${escapeHtml(
                s.site_name
              )}</option>`
          )
          .join("");

        $("#originSite").append(opts);
        $("#destinationSite").append(opts);
      })
      .fail(function () {
        $("#addJobError").text("Failed to load sites.");
      });
  }

  function canLoadVehicles() {
    const origin = $("#originSite").val();
    const q = $("input[name='quantity']").val();
    const w = $("input[name='weight']").val();
    const s = $("input[name='size']").val();
    return (
      origin && q && w && s && Number(q) > 0 && Number(w) > 0 && Number(s) > 0
    );
  }

  function loadAvailableVehicles() {
    $("#addJobError").text("");
    $("#addJobSuccess").text("");
    $("#selectedVehicleId").val("");
    $("#selectedVehicleType").val("");

    if (!canLoadVehicles()) {
      $("#vehicleArea").html(`<div class="alert alert-secondary mb-0">
      Choose origin + enter quantity/weight/size to load vehicles.
    </div>`);
      return;
    }

    const payload = {
      origin_site_id: $("#originSite").val(),
      quantity: $("input[name='quantity']").val(),
      weight: $("input[name='weight']").val(),
      size: $("input[name='size']").val(),
      is_hazardous: $("input[name='is_hazardous']:checked").val(),
    };

    $("#vehicleArea").html(
      `<div class="alert alert-info mb-0">Loading vehicles...</div>`
    );

    $.getJSON(API_BASE + "get_available_vehicles.php", payload)
      .done(function (res) {
        if (!res || !res.success) {
          $("#vehicleArea").html(`<div class="alert alert-danger mb-0">
          ${escapeHtml(res?.message || "Error loading vehicles.")}
        </div>`);
          return;
        }
        $("#vehicleArea").html(buildVehicleCarousel(res.data));
      })
      .fail(function (xhr) {
        $("#vehicleArea").html(`<div class="alert alert-danger mb-0">
        Vehicle API failed (${xhr.status}). Check API path / PHP error.
      </div>`);
      });
  }

  // Handle vehicle selection
  $(document).on("click", ".selectVehicleBtn", function () {
    const invId = $(this).data("inventory-id");
    const vtype = $(this).data("vehicle-type");
    $("#selectedVehicleId").val(String(invId));
    $("#selectedVehicleType").val(String(vtype));
    $("#addJobSuccess").text("Vehicle selected: " + vtype);
  });

  // Load sites once when modal opens
  let sitesLoaded = false;
  $("#AddJobModal").on("shown.bs.modal", function () {
    $("#addJobError").text("");
    $("#addJobSuccess").text("");
    if (!sitesLoaded) {
      sitesLoaded = true;
      loadSitesIntoSelects();
    }
  });

  // Reload vehicles when key inputs change
  $("#originSite").on("change", loadAvailableVehicles);
  $(document).on(
    "input",
    "input[name='quantity'], input[name='weight'], input[name='size']",
    function () {
      clearTimeout(window.__vehTimer);
      window.__vehTimer = setTimeout(loadAvailableVehicles, 250);
    }
  );
  $(document).on("change", "input[name='is_hazardous']", loadAvailableVehicles);

  // Submit job
  $("#ajaxAddJobForm").on("submit", function (e) {
    e.preventDefault();
    $("#addJobError").text("");
    $("#addJobSuccess").text("");

    const formData = $(this)
      .serializeArray()
      .reduce((acc, cur) => {
        acc[cur.name] = cur.value;
        return acc;
      }, {});

    // Validation
    if (!formData.vehicle_id) {
      $("#addJobError").text("Please select a vehicle before submitting.");
      return;
    }
    if (formData.origin_site_id === formData.destination_site_id) {
      $("#addJobError").text("Origin and destination sites must be different.");
      return;
    }
    if (
      formData.start_date &&
      formData.deadline &&
      formData.start_date > formData.deadline
    ) {
      $("#addJobError").text("Start date must be on or before the deadline.");
      return;
    }

    // Send Request
    $.post(API_BASE + "create_job.php", formData)
      .done(function (resText) {
        let res;
        try {
          res = typeof resText === "string" ? JSON.parse(resText) : resText;
        } catch (e) {}
        if (!res || !res.success) {
          $("#addJobError").text(res?.message || "Failed to create job.");
          return;
        }

        $("#addJobSuccess").text("Job created successfully!");
        setTimeout(() => {
          const modalEl = document.getElementById("AddJobModal");
          const modal = bootstrap.Modal.getInstance(modalEl);
          modal?.hide();
        }, 700);

        $("#ajaxAddJobForm")[0].reset();
        $("#selectedVehicleId").val("");
        $("#selectedVehicleType").val("");
        $("#vehicleArea").html(
          `<div class="alert alert-secondary mb-0">No vehicles loaded yet.</div>`
        );
      })
      .fail(function () {
        $("#addJobError").text("Server error while creating job.");
      });
  });

});
