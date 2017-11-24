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
require('../includes/customfieldfunctions.php');
$aInt        = new WHMCS_Admin('List Support Tickets');
$aInt->title = $aInt->lang('support', 'printticketversion');
$aInt->requiredFiles(array(
	'ticketfunctions'
));
$result     = select_query('tbltickets', '', array(
	'id' => $id
));
$data       = mysql_fetch_array($result);
$id         = $data['id'];
$tid        = $data['tid'];
$deptid     = $data['did'];
$pauserid   = $data['userid'];
$name       = $data['name'];
$email      = $data['email'];
$date       = $data['date'];
$title      = $data['title'];
$message    = $data['message'];
$tstatus    = $data['status'];
$attachment = $data['attachment'];
$urgency    = $data['urgency'];
$lastreply  = $data['lastreply'];
$flag       = $data['flag'];
$access     = validateAdminTicketAccess($id);
if($access == 'invalidid') {
	$aInt->gracefulExit($aInt->lang('support', 'ticketnotfound'));
}
if($access == 'deptblocked') {
	$aInt->gracefulExit($aInt->lang('support', 'deptnoaccess'));
}
if($access == 'flagged') {
	$aInt->gracefulExit($aInt->lang('support', 'flagnoaccess') . ": " . getAdminName($flag));
}
if($access) {
	$aInt->gracefulExit("Access Denied");
}
$message = strip_tags($message);
$message = nl2br($message);
$message = ticketAutoHyperlinks($message);
if($pauserid != '0000000000') {
	$result     = select_query('tblclients', '', array(
		'id' => $pauserid
	));
	$data       = mysql_fetch_array($result);
	$firstname  = $data['firstname'];
	$lastname   = $data['lastname'];
	$clientinfo = "<a href=\"clientsprofile.php?userid=" . $puserid . "\">" . $firstname . " " . $lastname . "</a>";
} else {
	$clientinfo = $aInt->lang('support', 'notregclient');
}
$department = getDepartmentName($deptid);
if($lastreply == '') {
	$lastreply = $date;
}
$date      = fromMySQLDate($date, 'time');
$lastreply = fromMySQLDate($lastreply, 'time');
$outstatus = getStatusColour($tstatus);
ob_start();
echo "
<p><b>";
echo $title;
echo "</b></p>

<p><b><i>";
echo $aInt->lang('support', 'ticketid');
echo ":</i></b> ";
echo $tid;
echo "<br>
<b><i>";
echo $aInt->lang('support', 'department');
echo ":</i></b> ";
echo $department;
echo "<br>
<b><i>";
echo $aInt->lang('support', 'createdate');
echo ":</i></b> ";
echo $date;
echo "<br>
<b><i>";
echo $aInt->lang('support', 'lastreply');
echo ":</i></b> ";
echo $lastreply;
echo "<br>
<b><i>";
echo $aInt->lang('fields', 'status');
echo ":</i></b> ";
echo $outstatus;
echo "<br>
<b><i>";
echo $aInt->lang('support', 'priority');
echo ":</i></b> ";
echo $urgency;
echo "</p>
<hr size=1><p>
";
$customfields = getCustomFields('support', $deptid, $id, true);
foreach($customfields as $customfield) {
	echo "<b><i>" . $customfield['name'] . ":</i></b> " . nl2br($customfield['value']) . "<br>";
}
echo "</p><hr size=1>

";
if($pauserid != '0000000000') {
	$result2    = select_query('tblclients', '', array(
		'id' => $pauserid
	));
	$data2      = mysql_fetch_array($result2);
	$firstname  = $data2['firstname'];
	$lastname   = $data2['lastname'];
	$clientinfo = "<b>" . $firstname . " " . $lastname . "</b>";
} else {
	$clientinfo = "<b>" . $name . "</b> (" . $email . ")";
}
echo $clientinfo . " @ " . $date . "<br><hr size=1><br>" . stripslashes($message) . "<hr size=1>";
$result = select_query('tblticketreplies', '', array(
	'tid' => $id
), 'date', 'ASC');
while($data = mysql_fetch_array($result)) {
	$ids        = $data['id'];
	$puserid    = $data['userid'];
	$name       = $data['name'];
	$email      = $data['email'];
	$date       = $data['date'];
	$date       = fromMySQLDate($date, 'time');
	$message    = $data['message'];
	$attachment = $data['attachment'];
	$admin      = $data['admin'];
	$message    = strip_tags($message);
	$message    = nl2br($message);
	$message    = ticketAutoHyperlinks($message);
	if($admin) {
		$clientinfo = "<b>" . $admin . "</b>";
	} else {
		if($puserid != '0000000000') {
			$result2    = select_query('tblclients', '', array(
				'id' => $pauserid
			));
			$data2      = mysql_fetch_array($result2);
			$firstname  = $data2['firstname'];
			$lastname   = $data2['lastname'];
			$clientinfo = "<B>" . $firstname . " " . $lastname . "</B>";
		} else {
			$clientinfo = "<B>" . $name . "</B><br><a href=\"mailto:" . $email . "\">" . $email . "</a>";
		}
	}
	echo $clientinfo . " @ " . $date . "<br><hr size=1><br>" . $message . "<br><br><hr size=1>";
}
echo "<p align=center style=\"font-size:10px;\">" . $aInt->lang('support', 'outputgenby') . " WHMCompleteSolution</p>";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->displayPopUp();