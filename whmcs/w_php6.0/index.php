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
define('CLIENTAREA', true);
require("init.php");
$pagetitle     = $_LANG['globalsystemname'];
$breadcrumbnav = "<a href=\"index.php\">" . $_LANG['globalsystemname'] . "</a>";
$templatefile  = 'homepage';
$pageicon      = '';
initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);
if($m = $whmcs->get_req_var('m')) {
	$module     = preg_replace("/[^a-zA-Z0-9._]/", '', $m);
	$modulepath = ROOTDIR . '/modules/addons/' . $module . '/' . $module . ".php";
	if(!file_exists($modulepath)) {
		redir();
	}
	require($modulepath);
	if(!function_exists($module . '_clientarea')) {
		redir();
	}
	$configarray = call_user_func($module . '_config');
	if(!isValidforPath($module)) {
		exit("Invalid Addon Module Name");
	}
	$modulevars = array();
	$result     = select_query('tbladdonmodules', '', array(
		'module' => $module
	));
	while($data = mysql_fetch_array($result)) {
		$modulevars[$data['setting']] = $data['value'];
	}
	if(!count($modulevars)) {
		redir();
	}
	$modulevars['modulelink'] = "index.php?m=" . $module;
	$_ADDONLANG               = array();
	$calanguage               = $whmcs->get_client_language();
	if(!isValidforPath($calanguage)) {
		exit("Invalid Client Area Language Name");
	}
	$addonlangfile = ROOTDIR . '/modules/addons/' . $module . '/lang/' . $calanguage . ".php";
	if(file_exists($addonlangfile)) {
		require($addonlangfile);
	} else {
		if($configarray['language']) {
			if(!isValidforPath($configarray['language'])) {
				exit("Invalid Addon Module Default Language Name");
			}
			$addonlangfile = ROOTDIR . '/modules/addons/' . $module . '/lang/' . $configarray['language'] . ".php";
			if(file_exists($addonlangfile)) {
				require($addonlangfile);
			}
		}
	}
	if(count($_ADDONLANG)) {
		$modulevars['_lang'] = $_ADDONLANG;
	}
	$results = call_user_func($module . '_clientarea', $modulevars);
	if(!is_array($results)) {
		redir();
	}
	if(!isValidforPath($module)) {
		exit("Invalid Addon Module Name");
	}
	if($results['forcessl'] && $whmcs->isSSLAvailable()) {
		$smartyvalues['systemurl'] = $whmcs->getSystemSSLURL();
		if(!$whmcs->in_ssl()) {
			WHMCS_Session::set('FORCESSL', true);
			$whmcs->redirectSystemSSLURL($whmcs->getCurrentFilename(false), $_REQUEST);
		}
	}
	$templatefile              = '/modules/addons/' . $module . '/' . $results['templatefile'] . ".tpl";
	$pagetitle                 = $results['pagetitle'];
	$smartyvalues['pagetitle'] = $pagetitle;
	if(is_array($results['breadcrumb'])) {
		foreach($results['breadcrumb'] as $k => $v) {
			$breadcrumbnav .= " > <a href=\"" . $k . "\">" . $v . "</a>";
		}
	} else {
		$breadcrumbnav .= $results['breadcrumb'];
	}
	$smartyvalues['breadcrumbnav'] = $breadcrumbnav;
	if(is_array($results['vars'])) {
		foreach($results['vars'] as $k => $v) {
			$smartyvalues[$k] = $v;
		}
	}
	if($results['requirelogin'] && !$_SESSION['uid']) {
		require("login.php");
	}
	outputClientArea($templatefile);
	exit();
}
if($whmcs->get_config('DefaultToClientArea')) {
	redir('', "clientarea.php");
}
$announcements = array();
$result        = select_query('tblannouncements', '', array(
	'published' => 'on'
), 'date', 'DESC', '0,3');
while($data = mysql_fetch_array($result)) {
	$id           = $data['id'];
	$date         = $data['date'];
	$title        = $data['title'];
	$announcement = $data['announcement'];
	$result2      = select_query('tblannouncements', '', array(
		'parentid' => $id,
		'language' => $_SESSION['Language']
	));
	$data         = mysql_fetch_array($result2);
	if($data['title']) {
		$title = $data['title'];
	}
	if($data['announcement']) {
		$announcement = $data['announcement'];
	}
	$date            = fromMySQLDate($date);
	$announcements[] = array(
		'id' => $id,
		'date' => $date,
		'title' => $title,
		'urlfriendlytitle' => getModRewriteFriendlyString($title),
		'text' => $announcement
	);
}
$smartyvalues['announcements']   = $announcements;
$smartyvalues['seofriendlyurls'] = $CONFIG['SEOFriendlyUrls'];
if($CONFIG['AllowRegister']) {
	$smartyvalues['registerdomainenabled'] = true;
}
if($CONFIG['AllowTransfer']) {
	$smartyvalues['transferdomainenabled'] = true;
}
if($CONFIG['AllowOwnDomain']) {
	$smartyvalues['owndomainenabled'] = true;
}
$captcha                        = clientAreaInitCaptcha();
$smartyvalues['capatacha']      = $captcha;
$smartyvalues['captcha']        = $smartyvalues['capatacha'];
$smartyvalues['recapatchahtml'] = clientAreaReCaptchaHTML();
$smartyvalues['recaptchahtml']  = $smartyvalues['recapatchahtml'];
outputClientArea($templatefile);