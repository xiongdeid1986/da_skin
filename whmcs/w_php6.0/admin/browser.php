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
define('ADMINAREA', true);
require('../init.php');
$aInt          = new WHMCS_Admin('Browser');
$aInt->title   = $aInt->lang('utilities', 'browser');
$aInt->sidebar = 'browser';
$aInt->icon    = 'browser';
$jQueryCode    = '';
if($action == 'delete') {
	check_token("WHMCS.admin.default");
	delete_query('tblbrowserlinks', array(
		'id' => $id
	));
	redir();
}
if($action == 'add') {
	check_token("WHMCS.admin.default");
	$siteurl = WHMCS_Input_Sanitize::decode($whmcs->get_req_var('siteurl'));
	$siteurl = WHMCS_Filter_Input::url($siteurl);
	if(!$siteurl) {
		redir("invalidurl=1");
	}
	insert_query('tblbrowserlinks', array(
		'name' => $sitename,
		'url' => $siteurl
	));
	redir();
}
$url    = "http://www.whmcs.com/";
$link   = $whmcs->get_req_var('link');
$result = select_query('tblbrowserlinks', '', '', 'name', 'ASC');
while($data = mysql_fetch_assoc($result)) {
	$browserlinks[] = $data;
	if($data['id'] == $link) {
		$url = $data['url'];
	}
}
$aInt->assign('browserlinks', WHMCS_Input_Sanitize::makesafeforoutput($browserlinks));
$content = '';
if($whmcs->get_req_var('invalidurl')) {
	$jQueryCode .= "
    \$( \".menu a\" ).click(function() {
        \$( \".errorbox\" ).fadeOut( \"slow\" )
    });";
	infoBox($aInt->lang('browser', 'invalidURL'), $aInt->lang('browser', 'invalidURLExplanation'), 'error');
	$content .= $infobox;
}
$content .= "<iframe width=\"100%\" height=\"580\" src=\"http://nullrefer.com/?" . $url . "\" name=\"brwsrwnd\" style=\"min-width:1000px;\"></iframe>";
$aInt->deleteJSConfirm('doDelete', 'browser', 'deleteq', "?action=delete&id=");
$aInt->content    = $content;
$aInt->jquerycode = $jQueryCode;
$aInt->display();