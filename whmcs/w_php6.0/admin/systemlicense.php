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
$aInt          = new WHMCS_Admin('Main Homepage');
$aInt->title   = $aInt->lang('license', 'title');
$aInt->sidebar = 'help';
$aInt->icon    = 'support';
ob_start();
$licensing = WHMCS_License::getinstance();
if($licensing->isClientLimitsEnabled()) {
	$warningMsg = '';
	if($licensing->isNearClientLimit()) {
		$clientLimit = $licensing->getClientLimit();
		if($licensing->getNumberOfActiveClients() < $clientLimit) {
			$warningMsg = "You are nearing your license's client allotment.";
		} else {
			if($clientLimit == $licensing->getNumberOfActiveClients()) {
				$warningMsg = "You are at your license's client allotment.";
			} else {
				$warningMsg = "You have exceeded your license's client allotment.";
			}
		}
		$warningMsg = "<div style=\"background-color:#FFBFBF;padding:10px;margin:20px;text-align:center;color:#7F0000;font-size:16px;\">WARNING: " . htmlentities($warningMsg) . "</div>";
	}
	echo "<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\"><tr><td>";
	echo $warningMsg;
	echo "<table width=\"50%\" bgcolor=\"#cccccc\" cellspacing=\"1\" cellpadding=\"10\" align=\"center\">" . "<tr bgcolor=\"#fff\"><td align=\"right\" width=\"50%\">Current Client Limit</td><td align=\"center\">" . $licensing->getTextClientLimit($aInt) . "</td></tr>" . "<tr bgcolor=\"#fff\"><td align=\"right\" width=\"50%\">Number of Active Clients</td><td align=\"center\">" . $licensing->getTextNumberOfActiveClients() . "</td></tr>" . "</table>";
	echo "<p>Licenses for WHMCS operate on a tiered structure based upon the number of Active clients within the installation.  <br />An active client is classed as one with any active or suspended products or services (including addons and domains).</p><p>If you have any questions, please <a href=\"http://nullrefer.com/?https://www.whmcs.com/support/\" target=\"_blank\">contact our support team</a>.</p>";
	$rawLicenseData = array(
		'action' => "upgrade request",
		'licensekey' => $whmcs->get_license_key(),
		'activeclients' => $licensing->getNumberOfActiveClients()
	);
	$licenseData    = $licensing->encryptMemberData($rawLicenseData);
	echo "<p align=\"center\"><a href=\"http://nullrefer.com/?https://www.whmcs.com/members/managelicense.php?license_data=" . $licenseData . "\" class=\"btn\">Upgrade/Downgrade License</a></p><br />";
	echo "</td></tr></table><div style='margin-bottom: 20px; margin-top: 20px'>&nbsp;</div>";
}
echo "
<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">
<tr><td width=\"20%\" class=\"fieldlabel\">";
echo $aInt->lang('license', 'regto');
echo "</td><td class=\"fieldarea\">";
echo $licensing->getKeyData('registeredname');
echo "</td></tr>
<tr><td class=\"fieldlabel\">";
echo $aInt->lang('license', 'key');
echo "</td><td class=\"fieldarea\">";
echo $whmcs->get_license_key();
echo "</td></tr>
<tr><td class=\"fieldlabel\">";
echo $aInt->lang('license', 'type');
echo "</td><td class=\"fieldarea\">";
echo $licensing->getKeyData('productname');
if($licensing->isClientLimitsEnabled()) {
	echo " (" . $licensing->getTextClientLimit($aInt) . " Client Limit)";
}
echo "</td></tr>
<tr><td class=\"fieldlabel\">";
echo $aInt->lang('license', 'validdomain');
echo "</td><td class=\"fieldarea\">";
echo (count($licensing->getKeyData('validdomains')) ? implode('<br />', $licensing->getKeyData('validdomains')) : 'None');
echo "</td></tr>
<tr><td class=\"fieldlabel\">";
echo $aInt->lang('license', 'validip');
echo "</td><td class=\"fieldarea\">";
echo (count($licensing->getKeyData('validips')) ? implode('<br />', $licensing->getKeyData('validips')) : 'None');
echo "</td></tr>
<tr><td class=\"fieldlabel\">";
echo $aInt->lang('license', 'validdir');
echo "</td><td class=\"fieldarea\">";
echo (count($licensing->getKeyData('validdirs')) ? implode('<br />', $licensing->getKeyData('validdirs')) : 'None');
echo "</td></tr>
<tr><td class=\"fieldlabel\">";
echo $aInt->lang('license', 'brandingremoval');
echo "</td><td class=\"fieldarea\">";
echo $licensing->getBrandingRemoval() ? 'Yes' : 'No';
echo "</td></tr>
<tr><td class=\"fieldlabel\">";
echo $aInt->lang('license', 'addons');
echo "</td><td class=\"fieldarea\">";
echo count($licensing->getActiveAddons()) ? implode("<br />", $licensing->getActiveAddons()) : 'None';
echo "</td></tr>
<tr><td class=\"fieldlabel\">";
echo $aInt->lang('license', 'created');
echo "</td><td class=\"fieldarea\">";
echo date("l, jS F Y", strtotime($licensing->getKeyData('regdate')));
echo "</td></tr>
<tr><td class=\"fieldlabel\">";
echo $aInt->lang('license', 'expires');
echo "</td><td class=\"fieldarea\">";
echo $licensing->getExpiryDate(true);
echo "</td></tr>
</table>

<p>";
/*
echo $aInt->lang('license', 'reissue1');
echo " <a href=\"http://nullrefer.com/?http://docs.whmcs.com/Licensing\">docs.whmcs.com/Licensing</a> ";
echo $aInt->lang('license', 'reissue2');
echo "</p>

";
*/
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();