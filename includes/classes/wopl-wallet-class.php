<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

class Woplwalletclass {
    private $session;
    private $woplcommon;
    private $woplmyaccount;

    public function __construct() {
        global $session, $woplcommon, $woplmyaccount;
		$this->session = $session;
        $this->woplcommon = $woplcommon;
        $this->woplmyaccount = $woplmyaccount;

        $menuItems = array(
            "nSlug" => "my-wallet",
            "nTitle" => "My Wallet",
            "nContent" => [$this, 'displayContent'],
            "nRank" => 1,
        );

        $this->woplmyaccount->addMyaccountMenu($menuItems);
    }

    //Display Content
    public function displayContent(){
        $user = wp_get_current_user();
        $walletAr = array(
            "walletBalance" => 0,
            "walletHistory" => array(),
        );

        $body = array(
            "heading" => "My Wallet",
            "pagedata" => $walletAr
        );

        echo $this->woplcommon->loadView("mywallet", $body);
    }
}
?>