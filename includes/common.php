<?php
if( !defined("ABSPATH") ) die('Direct access not allowed!');

require_once("classes/session-class.php");
require_once("classes/wopl-common-class.php");
require_once("classes/wopl-install-class.php");
require_once("classes/wopl-myaccount-class.php");
require_once("classes/wopl-review-class.php");
require_once("classes/wopl-searchhistory-class.php");
require_once("classes/wopl-wallet-class.php");
require_once("classes/wopl-recentitems-class.php");
require_once("classes/wopl-manageorder-class.php");
require_once("classes/wopl-login-class.php");
require_once("classes/wopl-zipcode-class.php");


global $session, $woplcommon, $woplmyaccount;

$session = new Sessionclass();
$woplcommon = new Woplcommonclass();
$woplinstall = new Woplinstallclass();
$woplzipcodeclass = new Woplzipcodeclass();
$woplmyaccount = new Woplmyaccountclass();
$woplreview = new Woplreviewclass();
$woplsearchhistory = new Woplsearchhistoryclass();
$woplwallet = new Woplwalletclass();
$woplrecentitems = new Woplrecentitemsclass();
$woplmanageorder = new Woplmanageorderclass();
$woplloginclass = new Woplloginclass();


?>