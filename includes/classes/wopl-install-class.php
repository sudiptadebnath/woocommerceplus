<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

class Woplinstallclass {
	private $woplcommon;
	public $wopl_db_version;
	public $wopl_plugin_version;
	
	public function __construct() {
		global $woplcommon;
		$this->woplcommon = $woplcommon;
		$this->wopl_plugin_version = '1.0.0';
		add_action( 'init', array( $this, 'woplCreatePostType' ) );		

		register_activation_hook( WOPL_PLUGIN_FILE,  array( $this, 'wopl_install' ) );
		register_deactivation_hook( WOPL_PLUGIN_FILE, array( $this, 'wopl_uninstall' ) );
		
		////add_action('admin_head', array( $this, 'wopl_include_admin' ));	
		add_action('admin_menu', array( $this, 'wopl_menu' ));
		////add_action( 'admin_footer', array( $this->woplcommon, 'wopl_ajax_call' ));

		//Frontend JS/css
		add_action('wp_head', array( $this, 'woplIncludeHeader' ));
		add_action('wp_footer', array( $this->woplcommon, 'woplIncludeFooter' ));

		//shortcode

		//end

		//add_filter( 'page_template', array( $this->woplcommon, 'woplPageTemplate' ) );
		//add_filter( 'single_template', array( $this->woplcommon, 'woplPageTemplate' ) );
		//add_action( 'template_redirect', array( $this->woplcommon, 'aidTemplateRedirect' ) );
	}	

	public function woplCreatePostType(){
		//Register Post
		// $post_type = "locationlist";
		// register_post_type($post_type,
		// 	array(
		// 		'labels'      => array(
		// 			'name'          => __('Location List', 'themethepeoplelab'),
		// 			'singular_name' => __('LoctionList', 'themethepeoplelab'),
		// 			'menu_name' => __('Location List', 'themethepeoplelab'),
		// 			'capability_type'    => 'post',
		// 		),
		// 		'public'      => true,
		// 		'has_archive' => true,
		// 		'show_in_menu'       => false,
		// 		'rewrite'            => array( 'slug' => $post_type ),					
		// 	)
		// );
		//end		
	}
	
	public function wopl_install(){
		global $wpdb, $user_level, $wp_rewrite, $wp_version, $wpsc_page_titles;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		add_option( "wopl_db_version", $this->wopl_db_version );	

		//======== Create Wallet Transaction Table ========
		$table_name = $wpdb->prefix . 'wallet_transaction';

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) !== $table_name ) {
			$wpdb_collate = $wpdb->collate;
			$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
				id bigint(20) unsigned NOT NULL auto_increment,
				user_id bigint(20) unsigned NULL,
				amount double,
				currency varchar( 20 ) NOT NULL,
				transaction_type varchar(200) NULL,
				payment_method varchar(50) NULL,
				transaction_id varchar(50) NULL,
				note varchar(500) Null,
				date datetime,
				PRIMARY KEY  (Id),
				KEY user_id (user_id)
				)
				COLLATE {$wpdb_collate}";
			dbDelta( $sql );
		}
	
		//============ Create GLOBAL PLUGIN SETTINGS Table ===========
		$table_name = $wpdb->prefix . 'wopl_settings';
		
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) !== $table_name ) {
			$wpdb_collate = $wpdb->collate;
			$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
				setting_key varchar(20) PRIMARY KEY,
				setting_lbl varchar(200),
				setting_value varchar(200),
				setting_typ varchar(200),
				setting_vld varchar(200)
			) COLLATE {$wpdb_collate}";
			dbDelta( $sql );
			
			//-------------- INSERT SETTINGS ------------------
			$wpdb->insert($table_name, array(
				'setting_key' => 'CANCEL_PERIOD',
				'setting_lbl' => 'Cancellation Period (Days)',
				'setting_value' => '7',
				'setting_typ' => 'TEXT',
				'setting_vld' => '>=<=,7,20,Cancellation Period may be 7 to 20 Days only.'		
			));		
			/*$wpdb->insert($table_name, array(
				'setting_key' => 'TEST_FLD1',
				'setting_lbl' => 'TEST FIELD 1',
				'setting_value' => 'bbb',
				'setting_typ' => 'COMBO||aaa,bbb,ccc',
				'setting_vld' => ''		
			));	*/
		}
		
	}
	
	public function wopl_uninstall(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'wopl_settings';
		$sql = "DROP TABLE IF EXISTS {$table_name}";
		$wpdb->query( $sql );
	}

	
	public function wopl_include_admin(){
		$returnText = '
		<link rel="stylesheet" type="text/css" href="'. $this->woplcommon->plugin_folder_path .'/admin/css/style.css" />
		<script type="text/javascript">
			var fcpkfolder = "'. $this->woplcommon->plugin_folder_path .'";
		</script>
		';
		echo $returnText;
	}

	public function wopl_menu(){  
		add_options_page('Woocommerceplus Settings', 'Woocommerceplus', 'manage_options', 
			'wopl_plugin', function () { $this->woplcommon->loadView("settings"); });	
		add_action('wp_ajax_save_settings', function () {
			global $wpdb;
			$table_name = $wpdb->prefix . 'wopl_settings';
			unset($_POST["action"]);
			$errs = array();
			foreach($_POST as $ky => $vl) {
				$ans = $wpdb->update( $table_name, 
					array('setting_value' => $vl),
					array('setting_key'=>$ky)
				);
				if($ans === false) $errs[] = $ky;
			}
			die( !$errs ? okRet("Successfully Saved") : errRet("Error Occured",$errs));
		});
		//add_menu_page('Data Feed', 'Data Feed', 'administrator', 'woplmainslug', array( $this->woplcommon, 'display_datafeed_main' ), 'dashicons-download'); 
	    //add_submenu_page('woplmainslug', 'Data Feed', 'Data Feed', 'administrator', 'woplmainslug', array( $this->woplcommon, 'display_datafeed_main' ));
    }

	public function woplIncludeHeader(){
		$returnText = '
		<link rel="stylesheet" type="text/css" href="'. $this->woplcommon->plugin_folder_path .'/assets/css/style.css" />
		<script type="text/javascript" src="'. $this->woplcommon->plugin_folder_path .'/assets/js/jquery.validate.min.js"></script>
		<script type="text/javascript" src="'. $this->woplcommon->plugin_folder_path .'/assets/js/additional-methods.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
		<script type="text/javascript" src="'. $this->woplcommon->plugin_folder_path .'/assets/js/custom.js"></script>
		<script type="text/javascript">
			var fcpkfolder = "'. $this->woplcommon->plugin_folder_path .'";
			var siteurl = "'. site_url() .'";
			var ajaxurl = "'. admin_url( "admin-ajax.php" ) .'";
		</script>
		';
		echo $returnText;
	}

	public function woplIncludeFooter(){
		$returnText = '';
		echo $returnText;
	}
	//end
		
}
?>