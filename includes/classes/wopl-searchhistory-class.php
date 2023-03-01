<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

class Woplsearchhistoryclass {
    private $session;
    private $woplcommon;
    private $woplmyaccount;

    public function __construct() {
        global $session, $woplcommon, $woplmyaccount;
		$this->session = $session;
        $this->woplcommon = $woplcommon;
        $this->woplmyaccount = $woplmyaccount;

        $menuItems = array(
            "nSlug" => "search-history",
            "nTitle" => "Search History",
            "nContent" => [$this, 'displayContent'],
            "nRank" => 4,
        );

        $this->woplmyaccount->addMyaccountMenu($menuItems);

        add_action('template_redirect', function(){
            if( !is_search() ) return;
            
            $keyword = sanitize_text_field( @$_GET['s'] );
            if( !$keyword ) return;

            //$posttype = sanitize_text_field( @$_GET['post_type'] );

            $user = wp_get_current_user();
            $search_history = $user->search_history;

            if( !is_array( $search_history ) ){
                $search_history = [];
            }

            $row_key = str_replace( '-', '_', sanitize_title_with_dashes( $keyword ) );

            if( @$search_history[$row_key] ){
                $search_history[$row_key]['searched_at'] = date_i18n('Y-m-d H:i:s');
            }

            $new_arr = [];
            $new_arr[$row_key]['keyword'] = $keyword;
            $new_arr[$row_key]['searched_at'] = date_i18n('Y-m-d H:i:s');

            $search_history = array_merge( $new_arr, $search_history );

            update_user_meta($user->ID, 'search_history', $search_history);
        });
    }

    //Display Content
    public function displayContent(){
        $user = wp_get_current_user();
        $search_history = $user->search_history;
        if( !is_array( $search_history ) ){
            $search_history = [];
        }
   
        $body = array(
            "heading" => "Search History",
            "pagedata" => $search_history
        );

        echo $this->woplcommon->loadView("searchhistory", $body);
    }
}
?>