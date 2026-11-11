<div class="modal fade" id="LoginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0" style="background-color: #ff6347; color: white">
      <div class="modal-body p-0">
        <div class="row g-0">
          <div class="col-md-6 d-none d-md-block">
            <img src="../img/Ninja-Van_1.jpeg" class="img-fluid w-100 h-100 rounded-start" style="object-fit: cover; min-height: 400px" alt="Login Image" />
          </div>

          <div class="col-12 col-md-6 p-5">
            <button type="button" class="btn-close btn-close-white float-end" data-bs-dismiss="modal" aria-label="Close"></button>
            
            <form id="ajaxLoginForm">
              <div class="mt-4">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control mb-1" name="login_email" id="emailInput" required />
                <div id="emailAjaxError" class="text-warning small mb-3 fw-bold"></div>

                <label class="form-label">Password</label>
                <input type="password" class="form-control mb-1" name="login_password" id="passInput" required />
                <div id="passAjaxError" class="text-warning small mb-4 fw-bold"></div>

                <div class="d-flex justify-content-between align-items-center">
                  <small>Don't have account?<br /><a href="RegisterForm.php" class="text-white">/Register</a></small>
                  <button type="submit" class="btn btn-dark px-4 py-2" id="doLogin">
                    Login
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>