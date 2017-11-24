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
require("../../../init.php");
require("vpsnet.php");
if( !$_SESSION['uid'] )
{
    exit( "Access Denied" );
}
$result = select_query('tblhosting', "count(*)", array( 'id' => $serviceid, 'userid' => $_SESSION['uid'] ));
$data = mysql_fetch_array($result);
if( !$data[0] )
{
    exit( "Access Denied" );
}
$creds = vpsnet_GetCredentials();
$api = VPSNET::getinstance($creds['username'], $creds['accesshash']);
$result = select_query('mod_vpsnet', '', array( 'relid' => $serviceid ));
while( $data = mysql_fetch_array($result) )
{
    ${$data['setting']} = $data['value'];
}
if( !in_array($period, array( 'hourly', 'daily', 'weekly', 'monthly' )) )
{
    $period = 'hourly';
}
$postfields = new VirtualMachine();
$postfields->id = $netid;
try
{
    if( $graph == 'cpu' )
    {
        $result = $postfields->showCPUGraph($period);
    }
    else
    {
        $result = $postfields->showNetworkGraph($period);
    }
    $output = $result['response_body'];
    echo $output;
}
catch( Exception $e )
{
    return "Caught exception: " . $e->getMessage();
}