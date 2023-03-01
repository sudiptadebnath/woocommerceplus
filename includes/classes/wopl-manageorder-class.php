<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

class Woplmanageorderclass {
    private $woplcommon;

    public function __construct() {
        global $woplcommon;
        $this->woplcommon = $woplcommon;
        $this->displayCancelAndReorder();
        $this->createAdminSettings();
    }
	
	private function displayCancelAndReorder() {
		add_filter( 'woocommerce_my_account_my_orders_actions', function($actions, $order) {
			$odtl = $this->getOrderDetail($order->get_id());
			
			$nonRefndItm = array_filter($odtl,function($itm) {
			  return !$itm['returnable'];
			});
			$CANCEL_PERIOD = (int)getAltisSettings("CANCEL_PERIOD","100");
			$ordCrDt = new DateTime($order->get_date_created());
			$today = new DateTime();
			$abs_diff = $today->diff($ordCrDt)->format("%a");
			if($abs_diff > $CANCEL_PERIOD) unset($actions["cancel"]);
			if($odtl and $nonRefndItm) unset($actions["cancel"]);
			
			$outStockItm = array_filter($odtl,function($itm) {
			  return $itm['stock'] <= $itm['qty'];
			});
			if($odtl and !$outStockItm) {
			  $actions[] = array(
				"url" => "#",
				"name" => __( 'ReOrder', 'woocommerce' ),
				"action" => " onclick='reorder(\"".$order->get_id()."\")'"
			  );
			}
			return $actions;
		}, 10, 2);

		add_action( 'wp_ajax_nopriv_reorder', function() {
			$oDtl = $this->getOrderDetail($_REQUEST["orderId"]);
			$outStockItm = array_filter($oDtl,function($itm) {
			  return $itm['stock'] <= $itm['qty'];
			});
			$ans="";
			if($oDtl and !$outStockItm) {
				foreach($oDtl as $pid => $prod) {
					if($prod['stock'] > $prod['qty']) {
						//$woplcommon->logIt(array("altis_ajax_reorder",$pid,$prod));
						WC()->cart->add_to_cart( $pid, $prod['qty']);
					}
				}
				$ans = $woplcommon->okRet("Successfully Added to Cart.",wc_get_checkout_url());
			} else {
				if($outStockItm) $ans = $woplcommon->errRet("Blank Some Item's stock is insufficient");
				if($oDtl == []) $ans = $woplcommon->errRet("Can't fetch order detail",array($_REQUEST["orderId"],$oDtl));
			}
			die($ans);			
		} ); 

	}

	private function getOrderDetail($oid) {
		$order = wc_get_order($oid);
		$detl = array();
		foreach($order->get_items() as $item_id => $item ) {
			$product = $item->get_product();
			if($product) {
				$refunded_qty = $order->get_qty_refunded_for_item( $item_id );
				$refund = $refunded_qty ? $refunded_qty : 0;
				logIt(array($product->get_name(),$product->get_meta('_altis_returnable')));
				$detl[$item->get_product_id()] = array(
					"product" => $product->get_name(), 
					"returnable" => ($product->get_meta('_altis_returnable') !== "no"), 
					"qty" => $item->get_quantity()-$refund,
					"stock" => $product->get_stock_quantity()
				);
			}
		}
		//$woplcommon->logIt(array("getOrderDetail",$oid,$detl));
		return $detl;
	}
	
	private function createAdminSettings() {
	
		add_action('woocommerce_product_options_general_product_data', function () {
			global $woocommerce, $post, $product_object;
		
		$vl = $product_object->get_meta('_altis_returnable');

		echo '<div class="altis_returnable">';
		woocommerce_wp_checkbox( array(
			'id'        => '_altis_returnable',
			'desc'      => __('Returnable', 'woocommerce'),
			'label'     => __('Returnable ?', 'woocommerce'),
			'value'     => empty($vl) ? 'yes' : $vl,
			'desc_tip'  => 'true'
		));
		echo '</div>';
		});

		// Save quantity setting fields values
		add_action('woocommerce_admin_process_product_object', function ($product) {
			$product->update_meta_data( '_altis_returnable', isset($_POST['_altis_returnable']) ? 'yes' : 'no' );
		});
	}


}
?>