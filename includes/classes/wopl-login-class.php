<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

class Woplloginclass {
    private $woplcommon;

    public function __construct() {
        global $woplcommon;
        $this->woplcommon = $woplcommon;    
        $this->addLoginForm();        
    }
	
	private function addLoginForm() {		
		add_shortcode('login', function($atts, $content = null) {
			return $this->woplcommon->loadView("login");
		});
		$this->displayAndSaveMobile();
		$this->addLoginAjaxHooks();
		$this->addOTPAjaxHooks();
	}
	
	private function addLoginAjaxHooks() {
		add_action('wp_ajax_nopriv_mob_login', function () {
			$users = get_users( array (
				'meta_key'     => 'billing_mobile_phone',
				'meta_value'   => $_POST["umob"],
				'compare' => '='
			));
			$ans="";
			if ($users){
				$user = $users[0];
				if($this->validateOtp($_POST["mobOTP"],'MOBILE_LOGIN_OTP')) {
					wp_set_current_user($user->ID, $user->user_login);
					wp_set_auth_cookie($user->ID);
					unset($_SESSION['MOBILE_LOGIN_OTP']);
					$ans = $this->woplcommon->okRet('Login successful',wc_get_page_permalink( 'myaccount' ));
				} else {
					$ans = $this->woplcommon->errRet('Invalid OTP');
				}		
			} else {
				$ans = $this->woplcommon->errRet('No such Mobile.');
			}
			//$this->woplcommon->logIt(array("wp_ajax_nopriv_mob_login",$_POST,$user));
			die($ans);
		});

		add_action('wp_ajax_nopriv_mail_login', function () {
			$user = get_user_by('email', $_POST["uemail"]);
			$ans="";
			if ($user){
				if($this->validateOtp($_POST["mailOTP"],'EMAIL_LOGIN_OTP')) {
					unset($_SESSION['EMAIL_LOGIN_OTP']);
					wp_set_current_user($user->ID, $user->user_login);
					wp_set_auth_cookie($user->ID);
					$ans = $this->woplcommon->okRet('Login successful',wc_get_page_permalink( 'myaccount' ));
				} else {
					$ans = $this->woplcommon->errRet('Invalid OTP');
				}		
			} else {
				$ans = $this->woplcommon->errRet('No such email.');
			}
			//$this->woplcommon->logIt(array("wp_ajax_nopriv_mail_login",$_POST,$user));
			die($ans);
		});
		
		add_action('wp_ajax_nopriv_mob_reg', function () {
			$users = get_users( array (
				'meta_key'     => 'billing_mobile_phone',
				'meta_value'   => $_POST["umob_reg"],
				'compare' => '='
			));
			if ($users){
				$ans = $this->woplcommon->errRet('Mobile already registered.');
			} else {
				if($this->validateOtp($_POST["mobOTP_reg"],'MOBILE_REGISTER_OTP')) {
					unset($_SESSION['MOBILE_REGISTER_OTP']);
					$username = $_POST["umob_reg"];
					$password = $_POST["umob_reg"].$_POST["mobOTP_reg"];
					$user_id = wp_create_user($username, $password);
					$user = get_user_by('id', $user_id);
					wp_update_user(array ('ID' => $user->ID, 'first_name'=>$username, 'last_name'=>$username, 'display_name' => $username));    
					update_user_meta($user->ID,'billing_mobile_phone',$username);
					$user->remove_role('subscriber');
					$user->add_role('customer');
					wp_set_current_user($user->ID, $username);
					wp_set_auth_cookie($user->ID);
					$ans = $this->woplcommon->okRet('Login successful',wc_get_page_permalink('myaccount')."/edit-account");
				} else {
					$ans = $this->woplcommon->errRet('Invalid OTP');
				}		
			}
			die($ans);
		});
	}
	
	private function displayAndSaveMobile() {
		add_action( 'woocommerce_edit_account_form', function () {
			$user = wp_get_current_user();
			?>
			 <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="billing_mobile_phone"><?php _e( 'Mobile phone', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--phone input-text" 
				name="billing_mobile_phone" id="billing_mobile_phone" value="<?php echo esc_attr( $user->billing_mobile_phone ); ?>" />
			</p>
			<?php
		});
		add_action( 'woocommerce_save_account_details_errors',function ( $args ){
			if ( isset($_POST['billing_mobile_phone']) && empty($_POST['billing_mobile_phone']) )
				$args->add( 'error', __( 'Please fill Mobile No', 'woocommerce' ),'');
		});
		add_action( 'woocommerce_save_account_details', function ( $user_id ) {
			if( isset($_POST['billing_mobile_phone']) && ! empty($_POST['billing_mobile_phone']) )
				update_user_meta( $user_id, 'billing_mobile_phone', sanitize_text_field($_POST['billing_mobile_phone']) );
		});
	}
	
	private function addOTPAjaxHooks() {
		add_action('wp_ajax_nopriv_otp_request', function () {
			$act2 = $_POST["action2"];	
			$ans = $this->woplcommon->okRet("Init");
			switch($act2) {
				case "mob_login":
					$users = get_users( array (
						'meta_key'     => 'billing_mobile_phone',
						'meta_value'   => $_POST["umob"],
						'compare' => '='
					));
					if ($users){
						$user = $users[0];
						$OTP = $this->genOtp($user,'MOBILE_LOGIN_OTP');
						$ans = $this->woplcommon->okRet("OTP ($OTP) sent to :".$user->billing_mobile_phone);
					} else $ans = $this->woplcommon->errRet('No such Mobile.');
					break;
				
				case "mail_login":
					$user = get_user_by('email', $_POST["uemail"]);
					$ans="";
					if ($user){
						$OTP = $this->genOtp($user,'EMAIL_LOGIN_OTP');
						wp_mail($user->user_email,"OTP","OTP for login is : $OTP");
						$ans = $this->woplcommon->okRet("OTP ($OTP) sent to :".$user->user_email);
					} else $ans = $this->woplcommon->errRet('No such email.');
					break;
			
				case "mob_reg":
					$users = get_users( array (
						'meta_key'     => 'billing_mobile_phone',
						'meta_value'   => $_POST["umob_reg"],
						'compare' => '='
					));
					//$this->woplcommon->logIt(array($_POST,$users));
					if ($users){
						$ans = $this->woplcommon->errRet('Mobile already registered.');
					} else {
						$OTP = $this->genOtp($user,'MOBILE_REGISTER_OTP');
						$ans = $this->woplcommon->okRet("OTP ($OTP) sent to :".$_POST["umob_reg"]);
					}
					break;			
			}
			die($ans);
		});
	}

	private function genOtp($user,$id) {
		//$this->woplcommon->logIt(array("genOtp",$_SESSION));
		$OTP = random_int(123456, 999999);
		$_SESSION[$id] = json_encode(array("otp"=>$OTP,"expire"=>time()+( 60 * 5)));
		return $OTP;
	}
	private function validateOtp($otp,$id) {
		//$this->woplcommon->logIt(array("validateOtp",$_SESSION));
		if(isset($_SESSION[$id])) {
			$oldOTP = json_decode($_SESSION[$id]);
			return ($oldOTP->otp == $otp and $oldOTP->expire >= time());
		}
		return false;
	}

}