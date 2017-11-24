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
$pagetitle = $_LANG['serverstatustitle'];
$breadcrumbnav = "<a href=\"index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"serverstatus.php\">" . $_LANG['serverstatustitle'] . "</a>";
$templatefile = 'serverstatus';
$pageicon = "images/status_big.gif";
initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);
if( $CONFIG['NetworkIssuesRequireLogin'] && !isset($_SESSION['uid']) )
{
    $goto = 'serverstatus';
    require("login.php");
}
WHMCS_Session::release();
$servers = array(  );
$result = select_query('tblservers', '', "disabled=0 AND statusaddress!=''", 'name', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $name = $data['name'];
    $ipaddress = $data['ipaddress'];
    $statusaddress = $data['statusaddress'];
    if( substr($statusaddress, 0 - 1, 1) != '/' )
    {
        $statusaddress .= '/';
    }
    if( substr($statusaddress, 0 - 9, 9) != "index.php" )
    {
        $statusaddress .= "index.php";
    }
    $servers[] = array( 'name' => $name, 'ipaddress' => $ipaddress, 'statusaddr' => $statusaddress, 'phpinfourl' => $statusaddress . "?action=phpinfo", 'serverload' => $serverload, 'uptime' => $uptime, 'phpver' => $phpver, 'mysqlver' => $mysqlver, 'zendver' => $zendver );
}
$smarty->assign('servers', $servers);
$smarty->register_function('get_port_status', 'getPortStatus');
if( $whmcs->get_req_var('getstats') )
{
    $num = $whmcs->get_req_var('num');
    $statusaddress = $servers[$num]['statusaddr'];
    $filecontents = curlCall($statusaddress, '');
    preg_match("/\\<load\\>(.*?)\\<\\/load\\>/", $filecontents, $serverload);
    preg_match("/\\<uptime\\>(.*?)\\<\\/uptime\\>/", $filecontents, $uptime);
    preg_match("/\\<phpver\\>(.*?)\\<\\/phpver\\>/", $filecontents, $phpver);
    preg_match("/\\<mysqlver\\>(.*?)\\<\\/mysqlver\\>/", $filecontents, $mysqlver);
    preg_match("/\\<zendver\\>(.*?)\\<\\/zendver\\>/", $filecontents, $zendver);
    $serverload = $serverload[1];
    $uptime = $uptime[1];
    $phpver = $phpver[1];
    $mysqlver = $mysqlver[1];
    $zendver = $zendver[1];
    if( !$serverload )
    {
        $serverload = $_LANG['serverstatusnotavailable'];
    }
    if( !$uptime )
    {
        $uptime = $_LANG['serverstatusnotavailable'];
    }
    echo json_encode(array( 'load' => $serverload, 'uptime' => $uptime, 'phpver' => $phpver, 'mysqlver' => $mysqlver, 'zendver' => $zendver ));
    exit();
}
if( $whmcs->get_req_var('ping') )
{
    $num = (int) $whmcs->get_req_var('num');
    $port = (int) $whmcs->get_req_var('port');
    if( is_array($servers[$num]) )
    {
        $res = @fsockopen($servers[$num]['ipaddress'], $port, $errno, $errstr, 5);
        echo "<img src=\"images/status" . ($res ? 'ok' : 'failed') . ".gif\" alt=\"" . $_LANG['serverstatus' . ($res ? 'on' : 'off') . 'line'] . "\" width=\"16\" height=\"16\" />";
        if( $res )
        {
            fclose($res);
        }
    }
    exit();
}
include("networkissues.php");
outputClientArea($templatefile);
function getPortStatus($params, &$smarty)
{
    global $servers;
    $num = $params['num'];
    $res = @fsockopen($servers[$num]['ipaddress'], $params['port'], $errno, $errstr, 5);
    $status = "<img src=\"images/status" . ($res ? 'ok' : 'failed') . ".gif\" alt=\"" . $_LANG['serverstatus' . ($res ? 'on' : 'off') . 'line'] . "\" width=\"16\" height=\"16\" />";
    return $status;
}