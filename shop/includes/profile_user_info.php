
 <!-- CUSTOM CSS -->
 <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/profile.css">


    
    <div class="container mt-4">
    <h3>Profile Details</h3>
    <p>Manage your profile information here.</p>

    <p>Username: <span>BYD_tester</span></p>

    <label for="fullname" class="mb-1">Fullname</label>
    <div class="input-group mb-3" style="max-width: 1000px; width: 960px;">
        <input type="text" class="form-control" id="fullname" disabled>
    </div>

    <label for="email" class="mb-1">Email</label>
    <div class="input-group mb-3">
        <input type="text" class="form-control" id="email" value="test*****@example.com" disabled>
        <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('email', this)">
            <i class="fas fa-eye"></i>
        </span>
    </div>
    <small class="errorEmail" style="display: none;"></small> <!-- Error message under input -->

    <label for="phone" class="mb-1">Phone Number</label>
    <div class="input-group mb-3">
        <input type="text" class="form-control" id="phone" value="*******85" maxlength="11" disabled>
        <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('phone', this)">
            <i class="fas fa-eye"></i>
        </span>
    </div>
    <small class="errorPhone" style="display: none;"></small> <!-- Error message under input -->

    <p>
        <span id="editProfile" class="text-primary" style="cursor: pointer; text-decoration: underline; display: inline-block; margin-top: 5px;">Edit</span>
    </p>
    <button id="saveProfile" class="btn btn-primary mt-2" style="display: none;">Save</button>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content text-center p-4 position-relative"> <!-- Center text & padding -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button> <!-- Perfectly placed "X" button -->
            <div class="modal-body d-flex flex-column align-items-center"> 
                <i class="fas fa-check-circle text-success" style="font-size: 50px;"></i> <!-- Check icon -->
                <p class="mt-3 fw-bold">Updated successfully!</p>
            </div>
        </div>
    </div>
</div>



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



<script src="js/profile_user_info.js"></script>
<script src="js/url-cleaner.js"></script>

