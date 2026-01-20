<?php

require_once __DIR__ . '/../components/mainhead.php';

use Includes\Security\CSRF;
use Model\Utility;

$utility_instance = new Utility($db);
?>

    <?php include_once GUEST_COMPONENT_DIR.'navbar.php';?>
    <div class="container mt-5 bg-light rounded shadow" style="margin-top: 50px; max-width: 500px;">
    <h2 class="text-center baskervville-sc-regular mb-4">Forgot Password</h2>

    <?php echo $utility_instance->displayAlertMessage(); ?>

    <form action="<?php echo CONTROLLER_URL; ?>auth.php" method="post">
        <?php echo CSRF::csrfField(); ?>

        <div class="mb-3">
            <label for="loginEmail" class="form-label">Email</label>
            <input
                type="email"
                name="email"
                class="form-control"
                id="loginEmail"
                placeholder="Enter your email"
                value="<?php echo $utility_instance->returnFormInput('email'); ?>"
            >
        </div>

        <button type="submit" name="request_reset_link" class="btn btn-success w-100">
            <i class="fa fa-paper-plane"></i> Send Reset Link
        </button>
    </form>

    <p class="mt-3 text-center">
        Remembered your password? <a href="login">Login</a>
    </p>
    </div>


<?php include_once GUEST_COMPONENT_DIR.'footer.php';?>