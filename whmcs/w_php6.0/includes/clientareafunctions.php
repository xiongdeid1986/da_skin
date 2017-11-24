<?php //00e57
// *************************************************************************
// *                                                                       *
// * WHMCS - The Complete Client Management, Billing & Support Solution    *
// * Copyright (c) WHMCS Ltd. All Rights Reserved,                         *
// * Version: 5.3.14 (5.3.14-release.1)                                    *
// * BuildId: 0866bd1.62                                                   *
// * Build Date: 28 May 2015                                               *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: info@whmcs.com                                                 *
// * Website: http://www.whmcs.com                                         *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.  This software  or any other *
// * copies thereof may not be provided or otherwise made available to any *
// * other person.  No title to and  ownership of the  software is  hereby *
// * transferred.                                                          *
// *                                                                       *
// * You may not reverse  engineer, decompile, defeat  license  encryption *
// * mechanisms, or  disassemble this software product or software product *
// * license.  WHMCompleteSolution may terminate this license if you don't *
// * comply with any of the terms and conditions set forth in our end user *
// * license agreement (EULA).  In such event,  licensee  agrees to return *
// * licensor  or destroy  all copies of software  upon termination of the *
// * license.                                                              *
// *                                                                       *
// * Please see the EULA file for the full End User License Agreement.     *
// *                                                                       *
// *************************************************************************
function initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav) {
	global $_LANG;
	global $clientsdetails;
	global $smarty;
	global $smartyvalues;
	if(defined('PERFORMANCE_DEBUG')) {
		define('PERFORMANCE_STARTTIME', microtime());
	}
	$whmcs          = WHMCS_Application::getinstance();
	$whmcsAppConfig = $whmcs->getApplicationConfig();
	$filename       = $whmcs->getCurrentFilename();
	$breadcrumb     = array();
	$parts          = explode(" > ", $breadcrumbnav);
	foreach($parts as $part) {
		$parts2       = explode("\">", $part, 2);
		$link         = str_replace("<a href=\"", '', $parts2[0]);
		$breadcrumb[] = array(
			'link' => $link,
			'label' => strip_tags($parts2[1])
		);
	}
	$smarty = new WHMCS_Smarty();
	$smarty->assign('template', $whmcs->get_config('Template'));
	$smarty->assign('language', $whmcs->get_client_language());
	$smarty->assign('LANG', $_LANG);
	$smarty->assign('companyname', $whmcs->get_config('CompanyName'));
	$smarty->assign('logo', $whmcs->get_config('LogoURL'));
	$smarty->assign('charset', $whmcs->get_config('Charset'));
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->assign('pageicon', $pageicon);
	$smarty->assign('filename', $filename);
	$smarty->assign('breadcrumb', $breadcrumb);
	$smarty->assign('breadcrumbnav', $breadcrumbnav);
	$smarty->assign('todaysdate', date("l, jS F Y"));
	$smarty->assign('date_day', date('d'));
	$smarty->assign('date_month', date('m'));
	$smarty->assign('date_year', date('Y'));
	$smarty->assign('token', generate_token('plain'));
	if($whmcs->isSSLAvailable()) {
		$smarty->assign('systemsslurl', $whmcs->getSystemSSLURL());
	}
	if($whmcs->isSSLAvailable() && $whmcs->in_ssl()) {
		$smarty->assign('systemurl', $whmcs->getSystemSSLURL());
	} else {
		$smarty->assign('systemurl', $whmcs->getSystemURL());
	}
	if($uid = WHMCS_Session::get('uid')) {
		$smarty->assign('loggedin', true);
		if(!function_exists('getClientsDetails')) {
			require(ROOTDIR . "/includes/clientfunctions.php");
		}
		$clientsdetails = getClientsDetails($uid);
		$smarty->assign('clientsdetails', $clientsdetails);
		$smarty->assign('clientsstats', getClientsStats($uid));
		if($cid = WHMCS_Session::get('cid')) {
			$result             = select_query('tblcontacts', 'id,firstname,lastname,email,permissions', array(
				'id' => $cid,
				'userid' => $uid
			));
			$data               = mysql_fetch_array($result);
			$loggedinuser       = array(
				'contactid' => $data['id'],
				'firstname' => $data['firstname'],
				'lastname' => $data['lastname'],
				'email' => $data['email']
			);
			$contactpermissions = explode(',', $data[4]);
		} else {
			$loggedinuser       = array(
				'userid' => $uid,
				'firstname' => $clientsdetails['firstname'],
				'lastname' => $clientsdetails['lastname'],
				'email' => $clientsdetails['email']
			);
			$contactpermissions = array(
				'profile',
				'contacts',
				'products',
				'manageproducts',
				'domains',
				'managedomains',
				'invoices',
				'tickets',
				'affiliates',
				'emails',
				'orders'
			);
		}
		$smarty->assign('loggedinuser', $loggedinuser);
		$smarty->assign('contactpermissions', $contactpermissions);
	}
	if($whmcs->get_config('AllowLanguageChange')) {
		$smarty->assign('langchange', 'true');
	}
	$setlanguage = "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'];
	$count       = 0;
	foreach($_GET as $k => $v) {
		$prefix = $count == 0 ? "?" : "&amp;";
		$setlanguage .= $prefix . htmlentities($k) . "=" . htmlentities($v);
		$count++;
	}
	$setlanguage .= "\" name=\"languagefrm\" id=\"languagefrm\"><strong>" . $_LANG['language'] . ":</strong> <select name=\"language\" onchange=\"languagefrm.submit()\">";
	foreach($whmcs->getValidLanguages() as $lang) {
		$setlanguage .= "<option";
		if($lang == $whmcs->get_client_language()) {
			$setlanguage .= " selected=\"selected\"";
		}
		$setlanguage .= ">" . ucfirst($lang) . "</option>";
	}
	$setlanguage .= "</select></form>";
	$smarty->assign('setlanguage', $setlanguage);
	$currenciesarray = array();
	$result          = select_query('tblcurrencies', "id,code,`default`", '', 'code', 'ASC');
	while($data = mysql_fetch_array($result)) {
		$currenciesarray[] = array(
			'id' => $data['id'],
			'code' => $data['code'],
			'default' => $data['default']
		);
	}
	if(count($currenciesarray) == 1) {
		$currenciesarray = '';
	}
	$smarty->assign('currencies', $currenciesarray);
	$smarty->assign('twitterusername', $whmcs->get_config('TwitterUsername'));
	$smarty->assign('condlinks', WHMCS_ClientArea::getconditionallinks());
	$smartyvalues = array();
}
function outputClientArea($templatefile, $nowrapper = false) {
	global $CONFIG;
	global $smarty;
	global $smartyvalues;
	global $orderform;
	global $usingsupportmodule;
	global $orderfrm;
	$whmcs          = WHMCS_Application::getinstance();
	$licensing      = WHMCS_License::getinstance();
	$whmcsAppConfig = $whmcs->getApplicationConfig();
	if(!$templatefile) {
		exit("Invalid Entity Requested");
	}
	if($licensing->getBrandingRemoval()) {
		$copyrighttext = '';
	} else {
		$copyrighttext = "<p style=\"text-align:center;\">Powered by <a href=\"http://nullrefer.com/?https://www.whmcs.com/\" target=\"_blank\">WHMCompleteSolution</a></p>";
	}
	if(isset($_SESSION['adminid'])) {
		$adminloginlink = "<div style=\"position:absolute;top:0px;right:0px;padding:5px;background-color:#000066;font-family:Tahoma;font-size:11px;color:#ffffff\" class=\"adminreturndiv\">您已登陆管理员 | <a href=\"" . $whmcs->get_admin_folder_name() . '/';
		if(isset($_SESSION['uid'])) {
			$adminloginlink .= "clientssummary.php?userid=" . $_SESSION['uid'] . "&return=1";
		}
		$adminloginlink .= "\" style=\"color:#6699ff\">返回管理账户</a></div>

";
	} else {
		$adminloginlink = '';
	}
	if(isset($GLOBALS['pagelimit'])) {
		$smartyvalues['itemlimit'] = $GLOBALS['pagelimit'];
	}
	if($smartyvalues) {
		foreach($smartyvalues as $key => $value) {
			$smarty->assign($key, $value);
		}
	}
	$hookvars = $smarty->_tpl_vars;
	unset($hookvars['LANG']);
	$hookres = run_hook('ClientAreaPage', $hookvars);
	foreach($hookres as $arr) {
		foreach($arr as $k => $v) {
			$hookvars[$k] = $v;
			$smarty->assign($k, $v);
		}
	}
	$hookres    = run_hook('ClientAreaHeadOutput', $hookvars);
	$headoutput = '';
	foreach($hookres as $data) {
		if($data) {
			$headoutput .= $data . "\n";
		}
	}
	$smarty->assign('headoutput', $headoutput);
	$hookres    = run_hook('ClientAreaHeaderOutput', $hookvars);
	$headoutput = '';
	foreach($hookres as $data) {
		if($data) {
			$headoutput .= $data . "\n";
		}
	}
	$smarty->assign('headeroutput', $headoutput);
	$hookres    = run_hook('ClientAreaFooterOutput', $hookvars);
	$headoutput = '';
	foreach($hookres as $data) {
		if($data) {
			$headoutput .= $data . "\n";
		}
	}
	$smarty->assign('footeroutput', $headoutput);
	if(!$nowrapper) {
		$header_file = $smarty->fetch($CONFIG['Template'] . "/header.tpl");
		$footer_file = $smarty->fetch($CONFIG['Template'] . "/footer.tpl");
	}
	$clientArea        = new WHMCS_ClientArea();
	$licenseBannerHtml = $clientArea->getLicenseBannerHtml();
	if($orderform) {
		$body_file = $smarty->fetch(ROOTDIR . '/templates/orderforms/' . $orderfrm->getTemplate() . '/' . $templatefile . ".tpl");
	} else {
		if($usingsupportmodule) {
			$body_file = $smarty->fetch(ROOTDIR . '/templates/' . $CONFIG['SupportModule'] . '/' . $templatefile . ".tpl");
		} else {
			if(substr($templatefile, 0, 1) == '/') {
				$body_file = $smarty->fetch(ROOTDIR . $templatefile);
			} else {
				$body_file = $smarty->fetch(ROOTDIR . '/templates/' . $CONFIG['Template'] . '/' . $templatefile . ".tpl");
			}
		}
	}
	if($nowrapper) {
		$template_output = $body_file;
	} else {
		$template_output = $header_file . PHP_EOL . $licenseBannerHtml . PHP_EOL . $body_file . PHP_EOL . $copyrighttext . PHP_EOL . $adminloginlink . PHP_EOL . $footer_file;
	}
	if(!in_array($templatefile, array(
		'3dsecure',
		'forwardpage',
		'viewinvoice'
	))) {
		$template_output = preg_replace("/(<form\\W[^>]*\\bmethod=('|\"|)POST('|\"|)\\b[^>]*>)/i", "\\1" . "\n" . generate_token(), $template_output);
	}
	echo $template_output;
	if(defined('PERFORMANCE_DEBUG')) {
		global $query_count;
		$exectime = microtime() - PERFORMANCE_STARTTIME;
		echo "<p>Performance Debug: " . $exectime . " Queries: " . $query_count . "</p>";
	}
}
function processSingleTemplate($templatepath, $templatevars) {
	global $CONFIG;
	global $smarty;
	global $smartyvalues;
	if($smartyvalues) {
		foreach($smartyvalues as $key => $value) {
			$smarty->assign($key, $value);
		}
	}
	foreach($templatevars as $key => $value) {
		$smarty->assign($key, $value);
	}
	$templatecode = $smarty->fetch(ROOTDIR . $templatepath);
	return $templatecode;
}
function processSingleSmartyTemplate($smarty, $templatepath, $values) {
	foreach($values as $key => $value) {
		$smarty->assign($key, $value);
	}
	$templatecode = $smarty->fetch(ROOTDIR . $templatepath);
	return $templatecode;
}
function CALinkUpdateCC() {
	global $CONFIG;
	$result = select_query('tblpaymentgateways', 'gateway', array(
		'setting' => 'type',
		'value' => 'CC'
	));
	while($data = mysql_fetch_array($result)) {
		$gateway = $data['gateway'];
		if(!isValidforPath($gateway)) {
			exit("Invalid Gateway Module Name");
		}
		if(file_exists(ROOTDIR . '/modules/gateways/' . $gateway . ".php")) {
			require_once(ROOTDIR . '/modules/gateways/' . $gateway . ".php");
		}
		if(function_exists($gateway . '_remoteupdate')) {
			$_SESSION['calinkupdatecc'] = 1;
			return true;
		}
	}
	if(!$CONFIG['CCNeverStore']) {
		$result = select_query('tblpaymentgateways', "COUNT(*)", "setting='type' AND (value='CC' OR value='OfflineCC')");
		$data   = mysql_fetch_array($result);
		if($data[0]) {
			$_SESSION['calinkupdatecc'] = 1;
			return true;
		}
	}
	$_SESSION['calinkupdatecc'] = 0;
	return false;
}
function CALinkUpdateSQ() {
	$get_sq_count = get_query_val('tbladminsecurityquestions', "COUNT(id)", '');
	if(0 < $get_sq_count) {
		$_SESSION['calinkupdatesq'] = 1;
		return true;
	}
	$_SESSION['calinkupdatesq'] = 0;
	return false;
}
function clientAreaTableInit($name, $defaultorderby, $defaultsort, $numitems) {
	$whmcs     = WHMCS_Application::getinstance();
	$pagelimit = '';
	$itemlimit = $whmcs->get_req_var('itemlimit');
	$orderby   = $whmcs->get_req_var('orderby');
	if($itemlimit == 'all') {
		$pagelimit = 99999999;
	} else {
		if(is_numeric($itemlimit)) {
			$pagelimit = $itemlimit;
		}
	}
	if($pagelimit) {
		setcookie('pagelimit', $pagelimit, time() + 90 * 24 * 60 * 60);
	}
	if(!$pagelimit && isset($_COOKIE['pagelimit']) && is_numeric($_COOKIE['pagelimit'])) {
		$pagelimit = $_COOKIE['pagelimit'];
	}
	if(!$pagelimit) {
		$pagelimit = '10';
	}
	$GLOBALS['pagelimit'] = $pagelimit;
	$page                 = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : '1';
	if($numitems < ($page - 1) * $pagelimit) {
		$page = 1;
	}
	$GLOBALS['page'] = $page;
	if(!isset($_SESSION['ca' . $name . 'orderby'])) {
		$_SESSION['ca' . $name . 'orderby'] = $defaultorderby;
	}
	if(!isset($_SESSION['ca' . $name . 'sort'])) {
		$_SESSION['ca' . $name . 'sort'] = $defaultsort;
	}
	if($_SESSION['ca' . $name . 'orderby'] == $orderby) {
		if($_SESSION['ca' . $name . 'sort'] == 'ASC') {
			$_SESSION['ca' . $name . 'sort'] = 'DESC';
		} else {
			$_SESSION['ca' . $name . 'sort'] = 'ASC';
		}
	}
	if($orderby) {
		$_SESSION['ca' . $name . 'orderby'] = $_REQUEST['orderby'];
	}
	$orderby = preg_replace("/[^a-z0-9]/", '', $_SESSION['ca' . $name . 'orderby']);
	$sort    = $_SESSION['ca' . $name . 'sort'];
	if(!in_array($sort, array(
		'ASC',
		'DESC'
	))) {
		$sort = 'ASC';
	}
	$limit = ($page - 1) * $pagelimit . ',' . $pagelimit;
	return array(
		$orderby,
		$sort,
		$limit
	);
}
function clientAreaTablePageNav($numitems) {
	$numitems   = (int) $numitems;
	$pagenumber = (int) $GLOBALS['page'];
	$pagelimit  = (int) $GLOBALS['pagelimit'];
	$totalpages = ceil($numitems / $pagelimit);
	$prevpage   = $pagenumber != 1 ? $pagenumber - 1 : '';
	$nextpage   = $pagenumber != $totalpages && $numitems ? $pagenumber + 1 : '';
	if(!$totalpages) {
		$totalpages = 1;
	}
	return array(
		'numitems' => $numitems,
		'numproducts' => $numitems,
		'pagenumber' => $pagenumber,
		'itemlimit' => $pagelimit,
		'totalpages' => $totalpages,
		'prevpage' => $prevpage,
		'nextpage' => $nextpage
	);
}
function clientAreaInitCaptcha() {
	$whmcs     = WHMCS_Application::getinstance();
	$capatacha = '';
	if($whmcs->get_config('CaptchaSetting') == 'on' || $whmcs->get_config('CaptchaSetting') == 'offloggedin' && !isset($_SESSION['uid'])) {
		if($whmcs->get_config('CaptchaType') == 'recaptcha') {
			require(ROOTDIR . "/includes/recaptchalib.php");
			$capatacha = 'recaptcha';
		} else {
			$capatacha = 'default';
		}
	}
	$GLOBALS['capatacha'] = $capatacha;
	return $capatacha;
}
function clientAreaReCaptchaHTML() {
	global $CONFIG;
	if($GLOBALS['capatacha'] != 'recaptcha') {
		return '';
	}
	$publickey  = $CONFIG['ReCAPTCHAPublicKey'];
	$recapatcha = recaptcha_get_html($publickey);
	return $recapatcha;
}