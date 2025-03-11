<div class="modal fade" id="SignupModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h3 class="modal-title" id="signupModalLabel">Sign Up</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Already have an account? <a href="#loginModal" data-bs-toggle="modal" class="modal-link text-decoration-none">Log in</a></p>
                <form action="#!">
                    <div class="row gy-3">
                        <!-- Grouped name fields in one row -->
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="First Name" required>
                                <label for="firstname" class="form-label">First Name</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="middlename" id="middlename" placeholder="Middle Name">
                                <label for="middlename" class="form-label">Middle Name</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Last Name" required>
                                <label for="lastname" class="form-label">Last Name</label>
                            </div>
                        </div>
                    </div>
                    <div class="row gy-3 mt-2">
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="tel" class="form-control" name="phone" id="phone" placeholder="Phone Number" required>
                                <label for="phone" class="form-label">Phone Number</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                                <label for="email" class="form-label">Email</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="address" id="address" placeholder="Full Address" required>
                                <label for="address" class="form-label">Full Address</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="zipcode" id="zipcode" placeholder="Zipcode" required>
                                <label for="zipcode" class="form-label">Zipcode</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                                <label for="password" class="form-label">Password</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" name="agree_terms" id="agree_terms">
                                <label class="form-check-label text-secondary" for="agree_terms">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="modal-link text-decoration-none">Terms & Conditions</a> and 
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" class="modal-link text-decoration-none">Privacy Policy</a>.
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-grid">
                                <button class="btn-modal btn btn-primary btn-lg" type="submit">Sign up now</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>