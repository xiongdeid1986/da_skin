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
require("../init.php");
$aInt = new WHMCS_Admin("View Activity Log");
$aInt->inClientsProfile = true;
$aInt->valUserID($userid);
$aInt->assertClientBoundary($userid);
ob_start();
echo "\n<form method=\"post\" action=\"clientslog.php?userid=";
echo $userid;
echo "\">\n<div style=\"width:100%;margin:0 auto;background-color:#f4f4f4;border:1px solid #ccc;text-align:center;-moz-border-radius: 5px;-webkit-border-radius: 5px;-o-border-radius: 5px;border-radius: 5px;\"><table cellpadding=\"6\" cellspacing=\"0\" align=\"center\"><tr><td><b>";
echo $aInt->lang('global', 'searchfilter');
echo "</b></td><td>";
echo $aInt->lang('fields', 'date');
echo ": <input type=\"text\" name=\"date\" value=\"";
echo $whmcs->get_req_var('date');
echo "\" class=\"datepick\"></td><td>";
echo $aInt->lang('fields', 'description');
echo ": <input type=\"text\" name=\"description\" value=\"";
echo $whmcs->get_req_var('description');
echo "\" size=\"30\"></td><td>";
echo $aInt->lang('fields', 'username');
echo ": <select name=\"username\"><option value=\"\">Any</option>";
$result = select_query('tblactivitylog', "DISTINCT user", '', 'user', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $user = $data['user'];
    echo "<option";
    if( $user == $whmcs->get_req_var('username') )
    {
        echo " selected";
    }
    echo ">" . $user . "</option>";
}
echo "</select></td><td>";
echo $aInt->lang('fields', 'ipaddress');
echo ": <input type=\"text\" name=\"ipaddress\" value=\"";
echo $whmcs->get_req_var('ipaddress');
echo "\" size=\"20\"></td><td><input type=\"submit\" value=\"";
echo $aInt->lang('system', 'filterlog');
echo "\" /></td></tr></table></div>\n</form>\n\n<br />\n\n";
$aInt->sortableTableInit('date');
$log = new WHMCS_Log_Activity();
$log->setCriteria(array( 'userid' => $userid, 'date' => $whmcs->get_req_var('date'), 'username' => $whmcs->get_req_var('username'), 'description' => $whmcs->get_req_var('description'), 'ipaddress' => $whmcs->get_req_var('ipaddress') ));
$numrows = $log->getTotalCount();
$tabledata = array(  );
$logs = $log->getLogEntries($whmcs->get_req_var('page'));
foreach( $logs as $entry )
{
    $tabledata[] = array( $entry['date'], "<div align=\"left\">" . $entry['description'] . "</div>", $entry['username'], $entry['ipaddress'] );
}
echo $aInt->sortableTable(array( $aInt->lang('fields', 'date'), $aInt->lang('fields', 'description'), $aInt->lang('fields', 'username'), $aInt->lang('fields', 'ipaddress') ), $tabledata);
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->display();