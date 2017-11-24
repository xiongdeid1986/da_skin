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
if( !$limitstart )
{
    $limitstart = 0;
}
if( !$limitnum )
{
    $limitnum = 25;
}
$where = array(  );
if( $userid )
{
    $where['clientid'] = (int) $userid;
}
if( $visitors )
{
    $where['visitors'] = (int) $visitors;
}
if( $paytype )
{
    $where['paytype'] = array( 'sqltype' => 'LIKE', 'value' => $paytype );
}
if( $payamount )
{
    $where['payamount'] = array( 'sqltype' => 'LIKE', 'value' => $payamount );
}
if( $onetime )
{
    $where['onetime'] = (int) $onetime;
}
if( $balance )
{
    $where['balance'] = array( 'sqltype' => 'LIKE', 'value' => $balance );
}
if( $withdrawn )
{
    $where['withdrawn'] = array( 'sqltype' => 'LIKE', 'value' => $withdrawn );
}
if( $userid )
{
    $result_user = select_query('tblaffiliates', 'clientid', array( 'clientid' => $userid ));
    $data_user = mysql_fetch_array($result_user);
    $userid = $data_user['clientid'];
    if( !$userid )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Client ID not found" );
        return NULL;
    }
}
$result = select_query('tblaffiliates', "COUNT(*)", $where);
$data = mysql_fetch_array($result);
$totalresults = $data[0];
$result2 = select_query('tblaffiliates', '', $where, 'id', 'ASC', (int) $limitstart . ',' . (int) $limitnum);
$apiresults = array( 'result' => 'success', 'totalresults' => $totalresults, 'startnumber' => $limitstart, 'numreturned' => mysql_num_rows($result2), 'affiliates' => array(  ) );
while( $data3 = mysql_fetch_assoc($result2) )
{
    $apiresults['affiliates']['affiliate'][] = $data3;
}
$responsetype = 'xml';