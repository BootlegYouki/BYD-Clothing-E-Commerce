<h2>Change Password</h2>
<p class="text-muted">For your account's security, do not share your password with anyone else.</p>

<!-- Current Password -->
<div class="mb-3 d-flex align-items-center">
    <label class="me-3" style="width: 150px;">Current Password</label>
    <div class="input-group flex-grow-1">
        <input type="password" class="form-control password-input" id="current-P">
        <span class="input-group-text toggle-password" data-target="current-P"></span>
    </div>
</div>

<!-- New Password -->
<div class="mb-3 d-flex align-items-center">
    <label class="me-3" style="width: 150px;">New Password</label>
    <div class="input-group flex-grow-1">
        <input type="password" class="form-control password-input" id="new-P">
        <span class="input-group-text toggle-password" data-target="new-P"></span>
    </div>
</div>

<!-- Confirm Password -->
<div class="mb-3 d-flex align-items-center">
    <label class="me-3" style="width: 150px;">Confirm Password</label>
    <div class="input-group flex-grow-1">
        <input type="password" class="form-control password-input" id="confirm-P">
        <span class="input-group-text toggle-password" data-target="confirm-P"></span>
    </div>
</div>

<!-- Confirm Button -->
<button class="btn-body btn btn-primary">Confirm</button>


<!-- CSS--> <!-- CSS-->  <!-- CSS--> <!-- CSS--> <!-- CSS--> <!-- CSS--> <!-- CSS--> <!-- CSS--> <!-- CSS-->

<!--  Remove Border -->
<style>
    .toggle-password {
        background: none !important;
        border: none !important;
        cursor: pointer;
    }

    .btn-body {
    font-size: 0.8rem;
    font-weight: 700;
    outline: none;
    border: none;
    background-color: coral;
    color: white;
    padding: 10px 20px;
    cursor: pointer;
    text-transform: uppercase;
    transition: background-color 0.3s ease-in-out; 
}

.btn-body:hover {
    background-color: rgb(255, 150, 115); 
}

</style>

<!-- Show/Hide Password Script -->
<script>
    document.querySelectorAll(".toggle-password").forEach(button => {
        let icon = document.createElement("i"); // Create the icon dynamically
        button.appendChild(icon); // Append icon to the button

        button.addEventListener("click", function () {
            let targetInput = document.getElementById(this.dataset.target);
            
            if (targetInput.type === "password") {
                targetInput.type = "text";
                icon.classList.replace("bi-eye", "bi-eye-slash");
            } else {
                targetInput.type = "password";
                icon.classList.replace("bi-eye-slash", "bi-eye");
            }
        });
    });
</script>

<!-- Bootstrap Icons (Make sure this is included in your project) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
