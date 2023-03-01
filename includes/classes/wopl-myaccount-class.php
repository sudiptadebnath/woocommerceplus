<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

class Woplmyaccountclass {
    private $session;
    private $woplcommon;

    public function __construct() {
        global $session, $woplcommon;
		$this->session = $session;
        $this->woplcommon = $woplcommon;        
    }

    //Create Custom My Account Page and Menu
    public function addMyaccountMenu($menuItems = array()){
        $nSlug = $menuItems["nSlug"];
        $nTitle = $menuItems["nTitle"];
        $nContent = $menuItems["nContent"];
        $nRank = $menuItems["nRank"];
        $this->createMyaccountMenu($nSlug, $nTitle, $nContent, $nRank);
    }

    private function createMyaccountMenu($nSlug, $nTitle, $nContent, $nRank){
        //URL Re-write
        add_action( 'init', function( $items ) use( $nSlug, $nTitle ){
            add_rewrite_endpoint( $nSlug, EP_ROOT | EP_PAGES );
        });

        //Get Query Var
        add_filter( 'woocommerce_get_query_vars', function( $items ) use( $nSlug, $nTitle ){
            $items[$nSlug] = $nSlug;
            return $items;            
        });

        //Set Title
        add_filter( 'woocommerce_endpoint_'.$nSlug.'_title', function($title, $endpoint) use( $nTitle ){
            $title = __( $nTitle, "woocommerce" );
            return $title;
        }, 10, 2 );

        //Add New My Account Page and Re-order Left Column Menu Items
        add_filter( 'woocommerce_account_menu_items', function( $items ) use( $nSlug, $nTitle, $nRank ){
            $newItems = array();
            $i = 0;

            foreach( $items as $key => $val ){
                if( $nRank != $i ){
                    $newItems[$key] = $val;
                }else{
                    $newItems[$nSlug] = $nTitle;
                    $newItems[$key] = $val;
                }
                $i++;
            }

            return $newItems;
        });        

        //Custom Menu Page Content
        add_action( 'woocommerce_account_'.$nSlug.'_endpoint', function() use( $nContent ){
            if( is_callable( $nContent ) ){
                $nContent();
            }
            elseif( is_array( $nContent ) && $nContent ){
                if( method_exists(@$nContent[0], @$nContent[1]) ){
                    $nContent[0]->{$nContent[1]}();
                }
            }
            elseif( is_string( $nContent ) ){
                echo $nContent;
            }
        });
    }
}