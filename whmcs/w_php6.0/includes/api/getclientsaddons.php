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
global $currency;
$currency = getCurrency();
$where = array(  );
if( $serviceid )
{
    if( is_numeric($serviceid) )
    {
        $where[] = "hostingid=" . (int) $serviceid;
    }
    else
    {
        $serviceids = explode(',', $serviceid);
        $serviceids = db_build_in_array(db_escape_numarray($serviceids));
        if( $serviceids )
        {
            $where[] = "hostingid IN (" . $serviceids . ")";
        }
    }
}
if( $clientid )
{
    $result = select_query('tblhosting', '', array( 'userid' => $clientid ));
    $hostingids = array(  );
    while( $data = mysql_fetch_array($result) )
    {
        $hostingids[] = (int) $data['id'];
    }
    $where[] = "hostingid IN (" . db_build_in_array($hostingids) . ")";
}
if( $addonid )
{
    $where[] = "addonid=" . (int) $addonid;
}
$result = select_query('tblhostingaddons', '', implode(" AND ", $where));
$apiresults = array( 'result' => 'success', 'serviceid' => $serviceid, 'clientid' => $clientid, 'totalresults' => mysql_num_rows($result) );
while( $data = mysql_fetch_array($result) )
{
    $aid = $data['id'];
    $addonarray = array( 'id' => $data['id'], 'userid' => get_query_val('tblhosting', 'userid', array( 'id' => $data['hostingid'] )), 'orderid' => $data['orderid'], 'serviceid' => $data['hostingid'], 'addonid' => $data['addonid'], 'name' => $data['name'], 'setupfee' => $data['setupfee'], 'recurring' => $data['recurring'], 'billingcycle' => $data['billingcycle'], 'tax' => $data['tax'], 'status' => $data['status'], 'regdate' => $data['regdate'], 'nextduedate' => $data['nextduedate'], 'nextinvoicedate' => $data['nextinvoicedate'], 'paymentmethod' => $data['paymentmethod'], 'notes' => $data['notes'] );
    $apiresults['addons']['addon'][] = $addonarray;
}
$responsetype = 'xml';