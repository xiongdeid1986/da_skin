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
    $where[] = "tblinvoices.userid='" . (int) $userid . "'";
}
if( $status )
{
    if( $status == 'Overdue' )
    {
        $where[] = "tblinvoices.status='Unpaid' AND tblinvoices.duedate<'" . date('Ymd') . "'";
    }
    else
    {
        $where[] = "tblinvoices.status='" . db_escape_string($status) . "'";
    }
}
$where = implode(" AND ", $where);
$result = select_query('tblinvoices', "COUNT(*)", $where);
$data = mysql_fetch_array($result);
$totalresults = $data[0];
$result = select_query('tblinvoices', "tblinvoices.id,tblinvoices.userid,tblclients.firstname,tblclients.lastname,tblclients.companyname,tblinvoices.*", $where, "tblinvoices`.`duedate", 'DESC', $limitstart . ',' . $limitnum, "tblclients ON tblclients.id=tblinvoices.userid");
$apiresults = array( 'result' => 'success', 'totalresults' => $totalresults, 'startnumber' => $limitstart, 'numreturned' => mysql_num_rows($result), 'invoices' => array(  ) );
while( $data = mysql_fetch_assoc($result) )
{
    $currency = getCurrency($data['userid']);
    $data['currencycode'] = $currency['code'];
    $data['currencyprefix'] = $currency['prefix'];
    $data['currencysuffix'] = $currency['suffix'];
    $apiresults['invoices']['invoice'][] = $data;
}
$responsetype = 'xml';