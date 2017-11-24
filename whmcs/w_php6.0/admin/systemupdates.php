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
$licensing->forceRemoteCheck();
$aInt          = new WHMCS_Admin('Configure General Settings');
$aInt->title   = $aInt->lang('system', 'checkforupdates');
$aInt->sidebar = 'help';
$aInt->icon    = 'support';
ob_start();
infobox('Update Check', 'This page has been disabled. There is no reason to check for updates using this version.');
echo $infobox;
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();
exit();
ob_start();
$latestLicenceCheckFailed = true;
try {
	$latestVersion = $licensing->getLatestVersion();
	if(!$latestVersion instanceof WHMCS_Version_AbstractVersion) {
		throw new WHMCS_Exception_Information("Failed to get any version info from license");
	}
	$latestLicenceCheckFailed = false;
}
catch(WHMCS_Exception $e) {
	infoBox($aInt->lang('system', 'updatecheck'), $aInt->lang('system', 'connectfailed'), 'error');
	echo $infobox;
}
if(!$latestLicenceCheckFailed) {
	if($licensing->isUpdateAvailable()) {
		infoBox($aInt->lang('system', 'updatecheck'), $aInt->lang('system', 'upgrade') . " <a href=\"http://nullrefer.com/?https://www.whmcs.com/members/clientarea.php\" target=\"_blank\">" . $aInt->lang('system', 'clickhere') . "</a>");
	} else {
		infoBox($aInt->lang('system', 'updatecheck'), $aInt->lang('system', 'runninglatestversion') . " (" . $whmcs->getVersion()->getCasual() . ")", 'success');
	}
	echo "<div class=\"versionnoticecont\">" . $infobox . "</div>";
	echo "
<br />

<style>
.versioncont {
    margin:0 auto;
    padding:0 0 25px 0;
    width:600px;
}
.versionyour {
    float:left;
    margin:0;
    padding:10px 20px;
    width:260px;
    background-color:#535353;
    border-bottom:1px solid #fff;
    color: #fff;
    font-size:20px;
    text-align:right;
    -moz-border-radius: 10px 0 0 0;
    -webkit-border-radius: 10px 0 0 0;
    -o-border-radius: 10px 0 0 0;
    border-radius: 10px 0 0 0;
}
.versionyournum {
    float:left;
    margin:0;
    padding:5px 20px;
    width:260px;
    background-color:#666;
    color: #fff;
    font-family:Arial;
    font-size:70px;
    text-align:right;
    -moz-border-radius: 0 0 0 10px;
    -webkit-border-radius: 0 0 0 10px;
    -o-border-radius: 0 0 0 10px;
    border-radius: 0 0 0 10px;
}
.versionlatest {
    float:left;
    margin:0;
    padding:10px 20px;
    width:260px;
    background-color:#035485;
    border-bottom:1px solid #fff;
    color: #fff;
    font-size:20px;
    text-align:left;
    -moz-border-radius: 0 10px 0 0;
    -webkit-border-radius: 0 10px 0 0;
    -o-border-radius: 0 10px 0 0;
    border-radius: 0 10px 0 0;
}
.versionlatestnum {
    float:left;
    margin:0;
    padding:5px 20px;
    width:260px;
    background-color:#0467A2;
    color: #fff;
    font-family:Arial;
    font-size:70px;
    text-align:left;
    -moz-border-radius: 0 0 10px 0;
    -webkit-border-radius: 0 0 10px 0;
    -o-border-radius: 0 0 10px 0;
    border-radius: 0 0 10px 0;
}
.versionnoticecont {
    width:700px;
    margin:30px auto 10px;
}
.newspost {
    margin:10px auto;
    padding:6px 15px;
    width:80%;
    background-color:#f8f8f8;
    border:1px solid #ccc;
    -moz-border-radius: 10px;
    -webkit-border-radius: 10px;
    -o-border-radius: 10px;
    border-radius: 10px;
}
</style>

<div class=\"versioncont\">
<div class=\"versionyour\">";
	echo $aInt->lang('system', 'yourversion');
	echo "</div>
<div class=\"versionlatest\">";
	echo $aInt->lang('system', 'latestversion');
	echo "</div>
    ";
	$yourNumberParts = explode(" ", $whmcs->getVersion()->getCasual(), 2);
	if(empty($yourNumberParts[1])) {
		$yourNumberParts[1] = "General Release";
	}
	$yourNumberParts[1] = sprintf("<br /><span style=\"font-size:20px;\">%s</span>", $yourNumberParts[1]);
	$yourNumberParts[2] = sprintf("<br /><span style=\"font-size:9px;\">(%s)</span>", $whmcs->getVersion()->getCanonical());
	$yourNumber         = implode('', $yourNumberParts);
	$latestNumberParts  = explode(" ", $licensing->getLatestVersion()->getCasual(), 2);
	if(empty($latestNumberParts[1])) {
		$latestNumberParts[1] = "General Release";
	}
	$latestNumberParts[1] = sprintf("<br /><span style=\"font-size:20px;\">%s</span>", $latestNumberParts[1]);
	$latestNumberParts[2] = sprintf("<br /><span style=\"font-size:9px;\">(%s)</span>", $licensing->getLatestVersion()->getCanonical());
	$latestNumber         = implode('', $latestNumberParts);
	echo "<div class=\"versionyournum\">";
	echo $yourNumber;
	echo "</div>
<div class=\"versionlatestnum\">";
	echo $latestNumber;
	echo "</div>

<div style=\"clear:both;\"></div>
</div>

";
}
if(function_exists('json_decode')) {
	$feed  = curlCall("http://www.whmcs.com/feeds/news.php", '');
	$feed  = json_decode($feed, 1);
	$count = 0;
	foreach($feed as $news) {
		echo "<div class=\"newspost\"><h2>" . ($news['link'] ? "<a href=\"" . $news['link'] . "\" target=\"_blank\">" : '') . $news['headline'] . ($news['link'] ? "</a>" : '') . "</h2>
    <p>" . $news['text'] . "</p>
    <p style=\"font-size:10px;\">" . date("l, F jS, Y", strtotime($news['date'])) . "</p>
    </div>
    ";
		$count++;
	}
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();