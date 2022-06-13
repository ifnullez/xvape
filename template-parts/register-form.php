<form id="register" novalidate>
    <div id="register_messages"></div>
    <div class="form-floating mb-3">
        <input type="email" class="form-control" id="user_email" name="user_email" placeholder="name@example.com">
        <label for="user_email"><?php _e('Email address', 'woocommerce'); ?></label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" class="form-control" id="user_pass" name="user_pass" placeholder="<?php _e('Password', 'woocommerce'); ?>">
        <label for="user_pass"><?php _e('Password', 'woocommerce'); ?></label>
    </div>
    <button type="button" id="forgot_password" class="forgot_button">
        <?php _e('Forgot password', 'woocommerce'); ?>
    </button>
    <button type="submit" id="register_submit" name="login_submit" class="btn btn-success w-100 mt-3"><?php _e('Register', 'woocommerce'); ?></button>
    <?php wp_nonce_field( 'register_nonce_security' ); ?>
</form>