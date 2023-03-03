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
			die( !$errs ? $this->woplcommon->okRet("Successfully Saved") : $this->woplcommon->errRet("Error Occured",$errs));
		});
		////add_action( 'admin_footer', array( $this->woplcommon, 'wopl_ajax_call' ));

		//Frontend JS/css
		add_action( 'admin_enqueue_scripts', array( $this, 'wopl_admin_styles_and_script') );
		add_action('wp_head', array( $this, 'woplIncludeHeader' ));
		add_action('wp_footer', array( $this, 'woplIncludeFooter' ));

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
		
		//============ Create ZIPCODE SETTINGS Table ===========
		$table_name = $wpdb->prefix . 'wopl_zipcode_settings';
		
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) !== $table_name ) {
			$wpdb_collate = $wpdb->collate;
			$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
				zipcode varchar(20) PRIMARY KEY,
				state_nm varchar(100),
				dist_nm varchar(100),
				city_nm varchar(100),
				cod_abl varchar(20),
				delivery_abl varchar(20),
				return_abl varchar(20)
			) COLLATE {$wpdb_collate}";
			dbDelta( $sql );
			
			//-------------- INSERT ZIPCODES ------------------
			foreach(file(dirname(__FILE__)."/zipcodes.txt") as $line) {
				if(trim($line)) {
					$det = explode("\t",trim($line));
					if(count($det) >= 4) {
						$wpdb->insert($table_name, array(
							'zipcode' => $this->woplcommon->getItm($det,0),
							'state_nm' => $this->woplcommon->getItm($det,1),
							'dist_nm' => $this->woplcommon->getItm($det,2),
							'city_nm' => $this->woplcommon->getItm($det,3),
							'cod_abl' => 'no',
							'delivery_abl' => 'no',
							'return_abl' => 'no'		
						));		
					}
				}
			}
		}
		
	}
	
	public function wopl_uninstall(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'wopl_settings';
		$sql = "DROP TABLE IF EXISTS {$table_name}";
		$wpdb->query( $sql );
		$table_name = $wpdb->prefix . 'wopl_zipcode_settings';
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
		add_menu_page('Woocommerce+', 'Woocommerce+', 'manage_options', 'woplmainslug'); 
	    add_submenu_page('woplmainslug', 'Woocommerce+ General', 'General', 'manage_options', 'woplgenslug', 
			function() { echo $this->woplcommon->loadView("generalsettings"); });
	    add_submenu_page('woplmainslug', 'Woocommerce+ Zipcode', 'Zipcode', 'manage_options', 'woplzipcodeslug', 
			function() { echo $this->woplcommon->loadView("zipcodesettings"); });
	    add_submenu_page('woplmainslug', 'Woocommerce+ Product Addon', 'Product Addon', 'manage_options', 'woplprodadonslug', 
			function() { echo $this->woplcommon->loadView("productaddon"); });
		remove_submenu_page('woplmainslug', 'woplmainslug');
		//add_menu_page('Data Feed', 'Data Feed', 'administrator', 'woplmainslug', array( $this->woplcommon, 'display_datafeed_main' ), 'dashicons-download'); 
	    //add_submenu_page('woplmainslug', 'Data Feed', 'Data Feed', 'administrator', 'woplmainslug', array( $this->woplcommon, 'display_datafeed_main' ));
    }

	public function woplIncludeHeader(){
		$myPath = $this->woplcommon->plugin_folder_path;
		$returnText = '
		<link rel="stylesheet" type="text/css" href="'. $myPath .'/assets/libs/Bootstrap-4-4.6.0/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="'. $myPath .'/assets/libs/DataTables-1.13.3/css/dataTables.bootstrap4.min.css" />
		<script type="text/javascript" src="'. $myPath .'/assets/libs/jquery-3.5.1.js"></script>
		<script type="text/javascript" src="'. $myPath .'/assets/libs/DataTables-1.13.3/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="'. $myPath .'/assets/libs/DataTables-1.13.3/js/dataTables.bootstrap4.min.js"></script>

		<link rel="stylesheet" type="text/css" href="'. $myPath .'/assets/css/style.css" />
		<script type="text/javascript" src="'. $myPath .'/assets/js/jquery.validate.min.js"></script>
		<script type="text/javascript" src="'. $myPath .'/assets/js/additional-methods.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
		<script type="text/javascript" src="'. $myPath .'/assets/js/custom.js"></script>
		<script type="text/javascript">
			var fcpkfolder = "'. $myPath .'";
			var siteurl = "'. site_url() .'";
			var ajaxurl = "'. admin_url( "admin-ajax.php" ) .'";
		</script>		
		';
		echo $returnText;
	}

	public function wopl_admin_styles_and_script() {
		$myPath = $this->woplcommon->plugin_folder_path;
		
		wp_enqueue_style('bootstrap.min.css', $myPath . '/assets/libs/Bootstrap-4-4.6.0/css/bootstrap.min.css');
		wp_enqueue_style('dataTables.bootstrap4.min.css', $myPath . '/assets/libs/DataTables-1.13.3/css/dataTables.bootstrap4.min.css');
		wp_enqueue_style('font-awesome.min.css', $myPath . '/assets/libs/font-awesome/css/font-awesome.min.css');
		wp_enqueue_style('style.css', $myPath . '/assets/css/style.css');
		
		wp_enqueue_script('jquery-3.5.1.js', $myPath . '/assets/libs/jquery-3.5.1.js');
		wp_enqueue_script('bootstrap.js', $myPath . '/assets/libs/Bootstrap-4-4.6.0/js/bootstrap.js');
		wp_enqueue_script('jquery.dataTables.min.js', $myPath . '/assets/libs/DataTables-1.13.3/js/jquery.dataTables.min.js');
		wp_enqueue_script('dataTables.bootstrap4.min.js', $myPath . '/assets/libs/DataTables-1.13.3/js/dataTables.bootstrap4.min.js');
		
		wp_enqueue_script('jquery.validate.min.js', $myPath . '/assets/js/jquery.validate.min.js');
		wp_enqueue_script('additional-methods.js', $myPath . '/assets/js/additional-methods.js');
		wp_enqueue_script('sweetalert.min.js', "https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js");
		wp_enqueue_script('custom.js', $myPath . '/assets/js/custom.js');
	}


	public function woplIncludeFooter(){
		$returnText = '';
		echo $returnText;
	}
	//end
		
}
?>