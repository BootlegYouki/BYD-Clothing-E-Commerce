<h2>Change Password</h2>
<p>For your account's security, do not share your password with anyone else</p>

<!-- Current Password -->
<div class="input-group mb-3 d-flex align-items-center">
    <p class="mb-0 mr-2" style="width: 150px;">Current Password</p>
    <input type="text" class="form-control" id="Current-P" data-current="" style="flex-grow: 1;" />
    <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('Current-P', this)">
        <i class="fas fa-eye"></i>
    </span>
</div>

<!-- New Password -->
<div class="input-group mb-3 d-flex align-items-center">
    <p class="mb-0 mr-2" style="width: 150px;">New Password</p>
    <input type="text" class="form-control" id="New-P" data-current="" style="flex-grow: 1;" />
    <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('New-P', this)">
        <i class="fas fa-eye"></i>
    </span>
</div>

<!-- Confirm Password -->
<div class="input-group mb-3 d-flex align-items-center">
    <p class="mb-0 mr-2" style="width: 150px;">Confirm Password</p>
    <input type="text" class="form-control" id="Confirm-P" data-current="" style="flex-grow: 1;" />
    <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('Confirm-P', this)">
        <i class="fas fa-eye"></i>
    </span>
</div>

<button id="saveProfile" class="btn-con btn btn-primary mt-2">Confirm</button>

<!-- CSS --> 
<style>
.btn-con {
    background-color: #000;
    color: white;
    border-color: #000;
}

.btn-con:hover{
    background-color: coral;
  
}

</style>
<script src="js/profile_changePass.js"></script> 




