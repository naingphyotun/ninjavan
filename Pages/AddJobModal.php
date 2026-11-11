<?php
// AddJobModal.php
// Requires: Bootstrap 5 + jQuery loaded in the page
?>

<div class="modal fade" id="AddJobModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0">
      <div class="modal-header" style="background:#E64A19;color:#fff;">
        <h5 class="modal-title">Add New Job</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="ajaxAddJobForm">
        <div class="modal-body" style="overflow-y:auto; max-height: calc(90vh - 140px); -webkit-overflow-scrolling: touch;">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Job name</label>
              <input type="text" class="form-control" name="job_name" required maxlength="150" placeholder="e.g., Electronics delivery">
            </div>

            <div class="col-md-2">
              <label class="form-label">Quantity</label>
              <input type="number" class="form-control" name="quantity" required min="1" step="1" value="1">
            </div>

            <div class="col-md-2">
              <label class="form-label">Weight (kg)</label>
              <input type="number" class="form-control" name="weight" required min="1" step="1" placeholder="e.g., 120">
            </div>

            <div class="col-md-2">
              <label class="form-label">Size (space units)</label>
              <input type="number" class="form-control" name="size" required min="1" step="1" placeholder="e.g., 50">
            </div>

            <div class="col-md-4">
              <label class="form-label d-block">Type</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_hazardous" id="nonHaz" value="0" checked>
                <label class="form-check-label" for="nonHaz">Non-hazardous</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_hazardous" id="haz" value="1">
                <label class="form-check-label" for="haz">Hazardous</label>
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Start date</label>
              <input type="date" class="form-control" name="start_date" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Deadline</label>
              <input type="date" class="form-control" name="deadline" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">From site (UK city)</label>
              <select class="form-select" name="origin_site_id" id="originSite" required>
                <option value="" disabled selected>Select origin city...</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">To site (UK city)</label>
              <select class="form-select" name="destination_site_id" id="destinationSite" required>
                <option value="" disabled selected>Select destination city...</option>
              </select>
            </div>
          </div>

          <hr class="my-4">

          <div class="d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Available vehicles at selected origin site</h6>
            <small class="text-muted" id="vehicleHint">Choose origin + enter weight/size to load vehicles</small>
          </div>

          <div class="mt-3" id="vehicleArea">
            <div class="alert alert-secondary mb-0">
              No vehicles loaded yet.
            </div>
          </div>

          <!-- Selected vehicle stored here -->
          <input type="hidden" name="vehicle_id" id="selectedVehicleId" value="">
          <input type="hidden" name="vehicle_type" id="selectedVehicleType" value="">

          <div class="text-danger small mt-2 fw-bold" id="addJobError"></div>
          <div class="text-success small mt-2 fw-bold" id="addJobSuccess"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark" id="btnCreateJob">Add Job</button>
        </div>
      </form>

    </div>
  </div>
</div>