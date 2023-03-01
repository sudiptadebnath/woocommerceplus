<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

class Woplreviewclass {
    private $session;
    private $woplcommon;
    private $woplmyaccount;

    public function __construct() {
        global $session, $woplcommon, $woplmyaccount;
		$this->session = $session;
        $this->woplcommon = $woplcommon;
        $this->woplmyaccount = $woplmyaccount;

        $menuItems = array(
            "nSlug" => "my-reviews",
            "nTitle" => "My Reviews",
            "nContent" => [$this, 'displayContent'],
            "nFnt" => [$this, 'reviewTitle'],
            "nRank" => 3,
        );

        $this->woplmyaccount->addMyaccountMenu($menuItems);
    }

    //Display Content
    public function displayContent(){
        $comments = get_comments( array(
        'post_type' => 'product',
        'status'    => 'approve',
        'user_id' => get_current_user_id()
        ) );

        $body = array(
            "heading" => "My Reviews",
            "pagedata" => $comments
        );

        echo $this->woplcommon->loadView("myreview", $body);
    }
}
?>