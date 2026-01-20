<?php

require_once __DIR__ . '/../components/mainhead.php';

use Includes\Security\CSRF;
use Model\Utility;

$utility_instance = new Utility($db);
?>

    <?php include_once GUEST_COMPONENT_DIR.'navbar.php';?>
    <div class="container mt-5 bg-light rounded shadow" style="margin-top: 50px; max-width: 500px;">
        <h2 class="text-center baskervville-sc-regular mb-4">Welcome Back</h2>

        <?php echo $utility_instance->displayAlertMessage(); ?>

         <form action="<?php echo CONTROLLER_URL; ?>auth.php" method="POST">
            <?php echo CSRF::csrfField(); ?>
            <input 
            type="hidden" 
            name="reset_code" 
            value="<?php echo htmlspecialchars($resetCode, ENT_QUOTES, 'UTF-8'); ?>"
            >
            <div class="mb-3">
                <label for="new_Password" class="form-label">Password</label>
                <input type="password" name="new_password" class="form-control" id="loginPassword" 
                    placeholder="Enter your password">
            </div>
            <div class="mb-3">
                <label for="confirm_Password" class="form-label">Password</label>
                <input type="password" name="confirm_password" class="form-control" id="signupPassword" 
                    placeholder="Enter your password">
            </div>
            <button type="submit" name="change_password" class="btn btn-success">
                <i class="fa fa-paper-plane"></i> Reset Password
            </button>
        </form>

        <p class="mt-3 text-center">Don't have an account? <a href="signup">Sign up here</a>.</p>
    </div>

<?php include_once GUEST_COMPONENT_DIR.'footer.php';?>