<?php
require_once __DIR__ . '/../components/mainhead.php';
?>
<body>
    <div class="container mt-5 bg-light rounded shadow" style="margin-top: 50px; max-width: 500px;">
        <h2 class="text-center baskervville-sc-regular mb-4">Welcome Back</h2>
        <div class="bg-white p-5 rounded shadow" style="width: 100%; max-width: 600px;">

        </div>
    </div>



    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="bg-white p-5 rounded shadow" style="width: 100%; max-width: 600px;">

            <h3 class="text-center mb-4">Create Your Account</h3>

            <?php if(isset($_SESSION['userserror'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['userserror']; unset($_SESSION['userserror']); ?></div>
            <?php endif; ?>

            <?php if(isset($_SESSION['usersmessage'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['usersmessage']; unset($_SESSION['usersmessage']); ?></div>
            <?php endif; ?>

            <form action="process/process_signup.php" method="post">
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="fname" class="form-label">First Name</label>
                        <input type="text" name="fname" class="form-control p-3" id="fname" placeholder="First name" style="background-color:#dff6dc;">
                    </div>
                    <div class="col-md-6">
                        <label for="lname" class="form-label">Last Name</label>
                        <input type="text" name="lname" class="form-control p-3" id="lname" placeholder="Last name" style="background-color:#dff6dc;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control p-3" id="email" placeholder="you@example.com" style="background-color:#dff6dc;">
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control p-3" id="phone" placeholder="Phone number" style="background-color:#dff6dc;">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Create Password</label>
                        <input type="password" name="pass1" class="form-control p-3" id="password" placeholder="Password" style="background-color:#dff6dc;">
                    </div>
                    <div class="col-md-6">
                        <label for="confirmpassword" class="form-label">Confirm Password</label>
                        <input type="password" name="pass2" class="form-control p-3" id="confirmpassword" placeholder="Confirm password" style="background-color:#dff6dc;">
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" name="btn" class="btn btn-success py-3 fs-5" style="border-radius: 30px;">Sign Up</button>
                </div>
            </form>

            <p class="text-center mt-3">
                Already have an account? 
                <a href="login.php" style="text-decoration: none; color: #2E8B57;">Login here</a>.
            </p>
        </div>
    </div>