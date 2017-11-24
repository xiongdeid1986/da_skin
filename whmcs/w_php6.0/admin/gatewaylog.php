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
$aInt = new WHMCS_Admin("View Gateway Log");
$aInt->title = $aInt->lang('gatewaytranslog', 'gatewaytranslogtitle');
$aInt->sidebar = 'billing';
$aInt->icon = 'logs';
ob_start();
echo $aInt->Tabs(array( $aInt->lang('global', 'searchfilter') ), true);
if( !count($_REQUEST) )
{
    $startdate = fromMySQLDate(date('Y-m-d', mktime(0, 0, 0, date('m') - 3, date('d'), date('Y'))));
    $enddate = getTodaysDate();
}
echo "\n<div id=\"tab0box\" class=\"tabbox\">\n  <div id=\"tab_content\">\n\n<form method=\"post\" action=\"gatewaylog.php\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"15%\" class=\"fieldlabel\">";
echo $aInt->lang('fields', 'daterange');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"startdate\" value=\"";
echo $startdate;
echo "\" class=\"datepick\" /> &nbsp; ";
echo $aInt->lang('global', 'to');
echo " <input type=\"text\" name=\"enddate\" value=\"";
echo $enddate;
echo "\" class=\"datepick\" /></td><td width=\"15%\" class=\"fieldlabel\">";
echo $aInt->lang('gatewaytranslog', 'gateway');
echo "</td><td class=\"fieldarea\"><select name=\"filtergateway\"><option value=\"\">";
echo $aInt->lang('global', 'any');
echo "</option>";
$query = "SELECT DISTINCT gateway FROM tblgatewaylog ORDER BY gateway ASC";
$result = full_query($query);
while( $data = mysql_fetch_array($result) )
{
    $gateway = $data['gateway'];
    echo "<option";
    if( $gateway == $filtergateway )
    {
        echo " selected";
    }
    echo ">" . $gateway . "</option>";
}
echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('gatewaytranslog', 'debugdata');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"filterdebugdata\" size=\"40\" value=\"";
echo $filterdebugdata;
echo "\"></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'result');
echo "</td><td class=\"fieldarea\"><select name=\"filterresult\"><option value=\"\">";
echo $aInt->lang('global', 'any');
echo "</option>";
$query = "SELECT DISTINCT result FROM tblgatewaylog ORDER BY result ASC";
$result = full_query($query);
while( $data = mysql_fetch_array($result) )
{
    $resultval = $data['result'];
    echo "<option";
    if( $resultval == $filterresult )
    {
        echo " selected";
    }
    echo ">" . $resultval . "</option>";
}
echo "</select></td></tr>\n</table>\n\n<img src=\"images/spacer.gif\" height=\"10\" width=\"1\"><br>\n<div align=\"center\"><input type=\"submit\" value=\"";
echo $aInt->lang('gatewaytranslog', 'filter');
echo "\" class=\"button\"></div>\n\n</form>\n\n  </div>\n</div>\n\n<br />\n\n";
$aInt->sortableTableInit('id', 'DESC');
$where = array(  );
if( $filterdebugdata )
{
    $where[] = "data LIKE '%" . db_escape_string(WHMCS_Input_Sanitize::decode($filterdebugdata)) . "%'";
}
if( $startdate )
{
    $where[] = "date>='" . toMySQLDate($startdate) . " 00:00:00'";
}
if( $enddate )
{
    $where[] = "date<='" . toMySQLDate($enddate) . " 23:59:59'";
}
if( $filtergateway )
{
    $where[] = "gateway='" . db_escape_string($filtergateway) . "'";
}
if( $filterresult )
{
    $where[] = "result='" . db_escape_string($filterresult) . "'";
}
$result = select_query('tblgatewaylog', "COUNT(*)", implode(" AND ", $where), 'id', 'DESC');
$data = mysql_fetch_array($result);
$numrows = $data[0];
$result = select_query('tblgatewaylog', '', implode(" AND ", $where), 'id', 'DESC', $page * $limit . ',' . $limit);
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $date = $data['date'];
    $gateway = WHMCS_Input_Sanitize::makesafeforoutput($data['gateway']);
    $data2 = WHMCS_Input_Sanitize::makesafeforoutput($data['data']);
    $res = WHMCS_Input_Sanitize::makesafeforoutput($data['result']);
    $date = fromMySQLDate($date, 'time');
    $tabledata[] = array( $date, $gateway, "<textarea rows=\"6\" cols=\"80\">" . $data2 . "</textarea>", "<strong>" . $res . "</strong>" );
}
echo $aInt->sortableTable(array( $aInt->lang('fields', 'date'), $aInt->lang('gatewaytranslog', 'gateway'), $aInt->lang('gatewaytranslog', 'debugdata'), $aInt->lang('fields', 'result') ), $tabledata);
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->display();