<?php
class Woplcommonclass {
	private $session;
	public $siteURL = "";
	public $plugin_folder_name = "";
	public $plugin_folder_path = "";
	public $default_currency = "₹";
	
	public function __construct() {
		global $session;
		$this->session = $session;
		$this->siteURL = $this->get_site_url();
		$this->plugin_folder_name = $this->get_plugin_folder_name();
		$this->plugin_folder_path = $this->get_plugin_folder_path();
		$this->registerThemeFunction();
	}

	protected function registerThemeFunction(){
		//add_filter( 'themeLocationList', array( $this, 'displayLocationList' ) );
		//add_filter( 'themeLoginLogout', array( $this, 'getLoginLogoutMenu' ) );
		//add_filter( 'themeLabPop', array( $this, 'displayLabPop' ) );

		//add_action( 'themeLocationListAc', array( $this, 'displayLocationList' ) );
	}

	public function get_site_url(){
		global $wpdb;
		$optn_table = $wpdb->prefix . 'options';
		$siteq = $wpdb->get_results( $wpdb->prepare("select * from ". $optn_table ." where option_name = %s", 'siteurl') );
		foreach ($siteq as $siteq){
			$siteURL = $siteq->option_value;
		}
		return $siteURL;
	}

	public function get_plugin_folder_name(){
		$pl_foldername = plugin_basename(__FILE__);
		$pl_foldername = explode("/", $pl_foldername);
		return $pl_foldername[0];
	}
	
	public function get_plugin_folder_path(){		
		$mainurl = $this->siteURL . "/wp-content/plugins/" . $this->get_plugin_folder_name();
		return $mainurl;
	}

	public function format_price($price, $decplace = 2){
		return number_format($price, $decplace);
	}

	public function display_price($price, $decimalval = 0){
		$price_display = $this->default_currency . "&nbsp;" . $this->format_price($price, $decimalval);
		return $price_display;
	}

	public function delete_custom_post($postType){
		global $wpdb;

		$sql = "DELETE a, b, c FROM wp_posts as a
		LEFT JOIN wp_term_relationships as b
			ON (a.ID = b.object_id)
		LEFT JOIN wp_postmeta as c
			ON (a.ID = c.post_id)
		WHERE a.post_type = '". $postType ."';";

		$wpdb->query($sql);

		/*
		DELETE FROM wp_posts WHERE post_type='post_type';
		DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts);
		DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts);
		*/
	}

	//plug-in page URL
	public function getPluginPage($slug){
		return get_permalink(get_page_by_path($slug));
	}

	//Load View
	public function loadView($view, $variables = array()){
		extract($variables);
		ob_start();
		include WOPL_PLUGIN_DIR . "views/" . $view . ".php";
		$buffer = ob_get_contents();
		@ob_end_clean();
		return $buffer;
	}
	
	
	//SUDIPTA
	public function logIt($msg,$flnm="debug.txt",$prnt=True) {
		$msg = is_array($msg) ? $msg : array($msg);
		$cont = sprintf("%s",json_encode($msg));
		$flcont = sprintf("%s=>%s\n",date("m-d-Y H:i:s"),$cont);
		if($prnt) file_put_contents(dirname(__FILE__)."/".$flnm,$flcont,FILE_APPEND);
		return $cont;
	}

	public function okRet($msg,$data=NULL) {
		return json_encode(array("err"=>False, "msg"=>$msg, "data"=>$data));
	}
	public function errRet($msg,$data=NULL) {
		return json_encode(array("err"=>True, "msg"=>$msg, "data"=>$data));
	}

}
?>