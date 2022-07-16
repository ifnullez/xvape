<form id="login" novalidate>
    <div id="login_messages"></div>
    <div class="form-floating mb-3">
        <input type="text" class="form-control" id="floatingInput" name="user_login" placeholder="name@example.com">
        <label for="floatingInput"><?php _e('Email address or login', 'woocommerce'); ?></label>
    </div>
    <div class="form-floating">
        <input type="password" class="form-control" id="floatingPassword" name="user_password" placeholder="<?php _e('Password', 'woocommerce'); ?>">
        <label for="floatingPassword"><?php _e('Password', 'woocommerce'); ?></label>
    </div>
    <div class="remember_forgot_wrapper d-flex flex-wrap justify-content-between align-items-center mt-2">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
            <label class="form-check-label" for="remember_me">
                <?php _e('Remember me', 'woocommerce'); ?>
            </label>
        </div>
        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" id="forgot_password" class="forgot_button">
            <?php _e('Forgot password', 'woocommerce'); ?>
        </a>
    </div>
    <button type="submit" id="login_submit" name="login_submit" class="btn btn-success w-100 mt-3"><?php _e('Login', 'woocommerce'); ?></button>
    <?php wp_nonce_field( 'login_register_nonce_security' ); ?>
</form>
