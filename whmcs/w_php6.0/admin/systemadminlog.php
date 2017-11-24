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
$aInt = new WHMCS_Admin("View Admin Log");
$aInt->title = $aInt->lang('system', 'adminloginlog');
$aInt->sidebar = 'utilities';
$aInt->icon = 'logs';
$aInt->sortableTableInit('date');
$query = "DELETE FROM tbladminlog WHERE lastvisit='00000000000000'";
$result = full_query($query);
$date = date("Y-m-d H:i:s", mktime(date('H'), date('i') - 15, date('s'), date('m'), date('d'), date('Y')));
$query = "UPDATE tbladminlog SET logouttime=lastvisit WHERE lastvisit<'" . $date . "' and logouttime='00000000000000'";
$result = full_query($query);
$numrows = get_query_val('tbladminlog', "COUNT(*)", '');
$result = select_query('tbladminlog', '', '', 'id', 'DESC', $page * $limit . ',' . $limit);
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $logintime = $data['logintime'];
    $lastvisit = $data['lastvisit'];
    $logouttime = $data['logouttime'];
    $admin_uname = $data['adminusername'];
    $ipaddress = $data['ipaddress'];
    $logintime = fromMySQLDate($logintime, true);
    $lastvisit = fromMySQLDate($lastvisit, true);
    if( $logouttime == "0000-00-00 00:00:00" )
    {
        $logouttime = '-';
    }
    else
    {
        $logouttime = fromMySQLDate($logouttime, true);
    }
    $tabledata[] = array( $logintime, $lastvisit, $logouttime, $admin_uname, "<a href=\"http://www.geoiptool.com/en/?IP=" . $ipaddress . "\" target=\"_blank\">" . $ipaddress . "</a>" );
}
$content = $aInt->sortableTable(array( $aInt->lang('system', 'logintime'), $aInt->lang('system', 'lastaccess'), $aInt->lang('system', 'logouttime'), $aInt->lang('fields', 'username'), $aInt->lang('fields', 'ipaddress') ), $tabledata);
$aInt->content = $content;
$aInt->display();