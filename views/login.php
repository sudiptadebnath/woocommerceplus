<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

<div class="u-columns col2-set" id="customer_login">
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12">

<?php endif; ?>

		<h2><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>
		
<ul class="nav nav-justified nav-tabs">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#pwdDiv"><?php esc_html_e( 'By Password', 'woocommerce' ); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#mobDiv"><?php esc_html_e( 'By Mobile OTP', 'woocommerce' ); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#mailDiv"><?php esc_html_e( 'By Email OTP', 'woocommerce' ); ?></a>
  </li>
</ul>
<div class="tab-content">

  <div class="tab-pane fade show active" id="pwdDiv">
		<form class="woocommerce-form woocommerce-form-login login" method="post">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
				</label>
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
			</p>
			<p class="woocommerce-LostPassword lost_password">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>
  </div>
  
  <div class="tab-pane fade" id="mobDiv">
		<form class="woocommerce-form woocommerce-form-login login" onsubmit="return ajaxSumbit(this);">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="umob"><?php esc_html_e( 'Registered Mobile', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
<div class="input-group mb-3">
  <input type="hidden" name="action" value="mob_login" />
  <input type="text" class="form-control" name="umob" id="umob" autocomplete="umob" value="<?php echo ( ! empty( $_POST['umob'] ) ) ? esc_attr( wp_unslash( $_POST['umob'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
  <div class="input-group-append">
    <button class="btn btn-success woocommerce-form-login__otp" type="button" onclick="otpRequest(this)">Send OTP</button>
  </div>
</div>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="mailOTP"><?php esc_html_e( 'OTP', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="mobOTP" id="mobOTP" autocomplete="mobOTP" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" id="login1" class="woocommerce-button button woocommerce-form-login__submit" name="login1" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>
  </div>
  
  <div class="tab-pane fade" id="mailDiv">
		<form class="woocommerce-form woocommerce-form-login login" onsubmit="return ajaxSumbit(this);">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="uemail"><?php esc_html_e( 'Registered Email', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
<div class="input-group mb-3">
  <input type="hidden" name="action" value="mail_login" />
  <input type="text" class="form-control" name="uemail" id="uemail" autocomplete="uemail" value="<?php echo ( ! empty( $_POST['uemail'] ) ) ? esc_attr( wp_unslash( $_POST['uemail'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
  <div class="input-group-append">
    <button class="btn btn-success woocommerce-form-login__otp" type="button" onclick="otpRequest(this)">Send OTP</button>
  </div>
</div>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="mailOTP"><?php esc_html_e( 'OTP', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="mailOTP" id="mailOTP" autocomplete="mailOTP" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" id="login2" class="woocommerce-button button woocommerce-form-login__submit" name="login2" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>
  </div>
  
</div>



<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

	</div>

	<div class="col-lg-6 col-md-6 col-sm-12">

		<h2><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>

<ul class="nav nav-justified nav-tabs">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#regUnmDiv"><?php esc_html_e( 'By Email', 'woocommerce' ); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#regMobDiv"><?php esc_html_e( 'By Mobile', 'woocommerce' ); ?></a>
  </li>
</ul>
<div class="tab-content">

  <div class="tab-pane fade show active" id="regUnmDiv">

		<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
				</p>

			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
				</p>

			<?php else : ?>

				<p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="woocommerce-form-row form-row">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>
		
	</div>
	
	
  <div class="tab-pane fade" id="regMobDiv">
		<form class="woocommerce-form woocommerce-form-login register" onsubmit="return ajaxSumbit(this);">

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="umob"><?php esc_html_e( 'Mobile Number', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
<div class="input-group mb-3">
  <input type="hidden" name="action" value="mob_reg" />
  <input type="text" class="form-control" name="umob_reg" id="umob_reg" autocomplete="umob_reg" value="<?php echo ( ! empty( $_POST['umob_reg'] ) ) ? esc_attr( wp_unslash( $_POST['umob_reg'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
  <div class="input-group-append">
    <button class="btn btn-success woocommerce-form-login__otp" type="button" onclick="otpRequest(this)">Send OTP</button>
  </div>
</div>
			</p>
			
			
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="mailOTP_reg"><?php esc_html_e( 'OTP', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="mobOTP_reg" id="mobOTP_reg" autocomplete="mobOTP_reg" />
			</p>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" id="register1" class="woocommerce-button button woocommerce-form-register__submit" name="register1" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in *', 'woocommerce' ); ?></button>
			</p>
			<p class="mainNav text-white p-2"><?php esc_html_e( '* Set password after login (old password will be Mobile No followed by OTP)', 'woocommerce' ); ?></p>
			
			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>
  </div>


	
</div>

	</div>
</div>
</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>


<script>
function ajaxSumbit(frm) {
	jQuery(".woocommerce-notices-wrapper").hide();
	jQuery(".woocommerce-form-login__submit").prop('disabled', true);
	jQuery(".woocommerce-form-register__submit").prop('disabled', true);
	jQuery.ajax({ type: "post", dataType: "json", url: ajax_object.ajax_url,
        data: jQuery(frm).serialize(),
        success: function (data) { 
			if(!data["err"]) {
				window.location.href = data["data"];
			}
			else {
				jQuery(".woocommerce-form-login__submit").prop('disabled', false);
				jQuery(".woocommerce-form-register__submit").prop('disabled', false);
				alert(data["msg"]);
			}
		},
        error: function (st, er) { 
			jQuery(".woocommerce-form-login__submit").prop('disabled', false);
			jQuery(".woocommerce-form-register__submit").prop('disabled', false);
			alert("ERROR !! "+JSON.stringify([st, er]));
		}		
    });
	return false;
}


function otpRequest(btn) {
	var frmDta = jQuery(btn).closest("form").serializeArray();
	frmDta.push({name: 'action2', value: frmDta[0].value });
	frmDta.push({name: 'action', value: 'otp_request'});
	frmDta.splice(0, 1); 
	jQuery(".woocommerce-form-login__otp").prop('disabled', true);
	jQuery.ajax({ type: "post", dataType: "json", url: ajax_object.ajax_url,
        data: frmDta,
        success: function (data) { 
			jQuery(".woocommerce-form-login__otp").prop('disabled', false);
			if(!data["err"]) {
				alert("OK : "+data["msg"]);
			}
			else {
				alert("ERR : "+data["msg"]);
			}
		},
        error: function (st, er) { 
			jQuery(".woocommerce-form-login__otp").prop('disabled', false);
			alert("ERROR !! "+JSON.stringify([st, er]));
		}		
    });
}
</script>