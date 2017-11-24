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
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}
$currusername = get_query_val('tbladmins', 'username', array( 'id' => $_SESSION['adminid'] ));
$result = full_query("SELECT DISTINCT adminusername FROM tbladminlog WHERE lastvisit>='" . date("Y-m-d H:i:s", mktime(date('H'), date('i') - 15, date('s'), date('m'), date('d'), date('Y'))) . "' AND adminusername!='" . db_escape_string($currusername) . "' AND logouttime='0000-00-00' ORDER BY lastvisit ASC");
$apiresults = array( 'result' => 'success', 'totalresults' => mysql_num_rows($result) + 1 );
$apiresults['staffonline']['staff'][] = array( 'adminusername' => $currusername, 'logintime' => date("Y-m-d H:i:s"), 'ipaddress' => $remote_ip, 'lastvisit' => date("Y-m-d H:i:s") );
while( $data = mysql_fetch_assoc($result) )
{
    $username = $data['adminusername'];
    $result2 = select_query('tbladminlog', 'adminusername,logintime,ipaddress,lastvisit', "lastvisit>='" . date("Y-m-d H:i:s", mktime(date('H'), date('i') - 15, date('s'), date('m'), date('d'), date('Y'))) . "' AND adminusername='" . db_escape_string($username) . "'", 'lastvisit', 'ASC', '0,1');
    $apiresults['staffonline']['staff'][] = mysql_fetch_assoc($result2);
}
$responsetype = 'xml';