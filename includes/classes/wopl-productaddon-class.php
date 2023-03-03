<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

class Woplproductaddonclass {
    private $woplcommon;

    public function __construct() {
        global $woplcommon;
        $this->woplcommon = $woplcommon;    
        $this->addProductAjaxHook();        
        $this->addProductAddonAjaxHooks();        
    }
	
	private function addProductAjaxHook() {
		add_action('wp_ajax_wopl_getproducts', function () {
			
			$tot = count(wc_get_products(array(
				'status' => 'publish', 
				'limit' => -1,
				'return' => "ids"
			)));

			$search = $_REQUEST["search"]["value"];
			$fltr = count(wc_get_products(array(
				'status' => 'publish', 
				'title' => $search,
				'limit' => -1,
				'return' => "ids"
			)));

			$opts = array(
				'status' => 'publish', 
				'title' => $search,
				'offset' => $_REQUEST["start"],
				'limit' => $_REQUEST["length"]
			);
			$ordFlds = array('ID','date','title');
			foreach($_REQUEST["order"] as $ord) {
				$opts["orderby"] = $ordFlds[$ord["column"]];
				$opts["order"] = $ord["dir"];
			}

			$prods = wc_get_products($opts);
			$data = array();
			foreach ($prods as $prod) {
				$data[] = array(
					"".$prod->get_id(),
					'<img src="'.wp_get_attachment_url( $prod->get_image_id() )
						.'" class="img-fluid" style="max-height:50px;" />',
					$prod->get_title(),
					wc_get_product_category_list($prod->get_id()),
					"".$prod->get_id()
				);
			}
			die(json_encode(array(
				"draw" => $_REQUEST['draw'], 
				"recordsTotal" => $tot, 
				"recordsFiltered" => $fltr, 
				"data" => $data
			)));
		});
	}

	private function addProductAddonAjaxHooks() {
		add_action('wp_ajax_wopl_getproductaddon', function () {
			$product = wc_get_product($_REQUEST["pid"]);
			$ans = "";
			if($product) {
				$prods = array();
				foreach(explode(",",$product->get_meta('_wopl_product_addon')) as $pid) {
					$prod = wc_get_product($pid);
					if($prod) {
						$prods[] = array(
							"".$prod->get_id(),
							'<img src="'.wp_get_attachment_url( $prod->get_image_id() )
								.'" class="img-fluid" style="max-height:50px;" />',
							$prod->get_title(),
							wc_get_product_category_list($prod->get_id())
						);
					}
				}
				$ans = $this->woplcommon->okRet("Ok",$prods);
			} else {
				$ans = $this->woplcommon->errRet("No such product");
			}
			die($ans);			
		});
		add_action('wp_ajax_wopl_saveproductaddon', function () {
			$product = wc_get_product($_REQUEST["pid"]);
			$ans = "";
			if($product) {
				$product->update_meta_data('_wopl_product_addon',$_REQUEST["addons"]);
				$ans = $this->woplcommon->okRet("Saved Successfully");
			} else {
				$ans = $this->woplcommon->errRet("No such product");
			}
			die($ans);			
		});
	}
	
	
}