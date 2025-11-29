<?php

require_once __DIR__ . '/../components/mainhead.php';

use Includes\Security\CSRF;
use Model\Utility;

$utility_instance = new Utility($db);
?>

<body>
    <div class="container mt-5 bg-light rounded shadow" style="margin-top: 50px; max-width: 500px;">
        <h2 class="text-center baskervville-sc-regular mb-4">Welcome Back</h2>

        <?php echo $utility_instance->displayAlertMessage(); ?>

        <form action="<?php echo CONTROLLER_URL;?>auth.php" method="post">
            <?php echo CSRF::csrfField(); ?>

            <div class="mb-3">
                <label for="loginEmail" class="form-label">Email or Username</label>
                <input type="text" name="email" class="form-control" id="loginEmail" 
                    placeholder="Enter your email or username" 
                    value="<?php echo $utility_instance->returnFormInput('email');?>"
                >
            </div>

            <div class="mb-3">
                <label for="loginPassword" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="loginPassword" 
                    placeholder="Enter your password">
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" name="corper_login" class="btn btn-success">
                <i class="fa fa-paper-plane"></i> Login
            </button>
        </form>

        <p class="mt-3 text-center">Don't have an account? <a href="signup">Sign up here</a>.</p>
    </div>

<?php include_once GUEST_COMPONENT_DIR.'footer.php';?>