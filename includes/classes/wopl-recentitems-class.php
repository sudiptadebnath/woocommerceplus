<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

class Woplrecentitemsclass {
    private $session;
    private $woplcommon;
    private $woplmyaccount;

    public function __construct() {
        global $session, $woplcommon, $woplmyaccount;
		$this->session = $session;
        $this->woplcommon = $woplcommon;
        $this->woplmyaccount = $woplmyaccount;

        $menuItems = array(
            "nSlug" => "recent-viewed-items",
            "nTitle" => "Recently Viewed Items",
            "nContent" => [$this, 'displayContent'],
            "nRank" => 4,
        );

        $this->woplmyaccount->addMyaccountMenu($menuItems);

        add_action('template_redirect', function(){
            if( !is_product() ) return;
            global $post;
            if( $post->post_type != 'product' ) return;
            $user = wp_get_current_user();
            $recent_items = $user->recent_viewed_items;
            if( !is_array( $recent_items ) ){
                $recent_items = [];
            }

            $row_key = 'product_'.$post->ID;

            if( @$recent_items[$row_key] ){
                $recent_items[$row_key]['viewed_at'] = date_i18n('Y-m-d H:i:s');
            }

            $new_arr = [];
            $new_arr[$row_key]['product_id'] = $post->ID;
            $new_arr[$row_key]['viewed_at'] = date_i18n('Y-m-d H:i:s');

            $recent_items = array_merge( $new_arr, $recent_items );

            update_user_meta($user->ID, 'recent_viewed_items', $recent_items);
        });
    }

    //Display Content
    public function displayContent(){
        $user = wp_get_current_user();
        $recent_items = $user->recent_viewed_items;
        if( !is_array( $recent_items ) ){
            $recent_items = [];
        }
       
        $body = array(
            "heading" => "Recently Viewed Items",
            "pagedata" => $recent_items
        );

        echo $this->woplcommon->loadView("recentvieweditems", $body);
    }
}
?>