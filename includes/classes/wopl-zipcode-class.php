<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

class Woplzipcodeclass {
    private $woplcommon;

    public function __construct() {
        global $woplcommon;
        $this->woplcommon = $woplcommon;    
        $this->addZipcodeAjaxHooks();        
    }
	
	private function addZipcodeAjaxHooks() {
		add_action('wp_ajax_wopl_getzipcode', function () {
			/*$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
				zipcode varchar(20) PRIMARY KEY,
				state_nm varchar(100),
				dist_nm varchar(100),
				city_nm varchar(100),
				cod_abl varchar(20),
				delivery_abl varchar(20),
				return_abl varchar(20)
			) COLLATE {$wpdb_collate}";
			<th>Zipcode</th>
			<th>State</th>
			<th>District</th>
			<th>City</th>
			<th>COD</th>
			<th>Delivery</th>
			<th>Return</th>			
			*/
			
			//$this->woplcommon->logIt(array("111",$_REQUEST));
			$start = $_REQUEST["start"];
			$length = $_REQUEST["length"];
			$order_by = join(", ",array_map(function($vl) {
				return ($vl["column"]+1)." ".$vl["dir"];
			},$_REQUEST["order"]));
			$search = $_REQUEST["search"]["value"];
			
			global $wpdb;
			$table_name = $wpdb->prefix . 'wopl_zipcode_settings';
			
			$sql = "SELECT count(*) FROM $table_name";
			$tot = $wpdb->get_var($sql);

			$sql = "SELECT count(*) FROM $table_name 
				WHERE concat('~',zipcode,'~',state_nm,'~',dist_nm,'~',city_nm,'~') LIKE '%$search%'
				";
			$fltr = $wpdb->get_var($sql);
			
			$sql = "SELECT * FROM $table_name 
				WHERE concat('~',zipcode,'~',state_nm,'~',dist_nm,'~',city_nm,'~') LIKE '%$search%'
				ORDER BY $order_by LIMIT $start, $length";
			//$this->woplcommon->logIt(array("222",$sql));
			$results = $wpdb->get_results($sql, ARRAY_N);
			die(json_encode(array(
				"draw"=> $_REQUEST['draw'], 
				"recordsTotal"=> $tot, 
				"recordsFiltered"=> $fltr, 
				"data" => $results
			)));
		});
		
		
		add_action('wp_ajax_wopl_savezipcode', function () {
			/*$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
				zipcode varchar(20) PRIMARY KEY,
				state_nm varchar(100),
				dist_nm varchar(100),
				city_nm varchar(100),
				cod_abl varchar(20),
				delivery_abl varchar(20),
				return_abl varchar(20)
			) COLLATE {$wpdb_collate}";*/
			
			global $wpdb;
			$table_name = $wpdb->prefix . 'wopl_zipcode_settings';
			$ans = $wpdb->update( $table_name, 
				array(
					'cod_abl' => $_REQUEST["COD"],
					'delivery_abl' => $_REQUEST["Delivery"],
					'return_abl' => $_REQUEST["Return"]
				),
				array('zipcode' => $_REQUEST["zipcode"])
			);
			die($ans === false ? 
				$this->woplcommon->errRet("Error occured") : 
				$this->woplcommon->okRet("Saved Successfully"));			
		});
	}
	
	

}