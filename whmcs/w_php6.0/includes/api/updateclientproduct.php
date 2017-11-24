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
if( !function_exists('recalcRecurringProductPrice') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
if( !function_exists('saveCustomFields') )
{
    require(ROOTDIR . "/includes/customfieldfunctions.php");
}
if( !function_exists('getCartConfigOptions') )
{
    require(ROOTDIR . "/includes/configoptionsfunctions.php");
}
$result = select_query('tblhosting', 'id,packageid,billingcycle,promoid', array( 'id' => $serviceid ));
$data = mysql_fetch_array($result);
$serviceid = $data['id'];
if( !$serviceid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Service ID Not Found" );
}
else
{
    $updateqry = array(  );
    if( $pid )
    {
        $updateqry['packageid'] = $pid;
    }
    if( $serverid )
    {
        $updateqry['server'] = $serverid;
    }
    if( $regdate )
    {
        $updateqry['regdate'] = $regdate;
    }
    if( $nextduedate )
    {
        $updateqry['nextduedate'] = $nextduedate;
        $updateqry['nextinvoicedate'] = $nextduedate;
    }
    if( $domain )
    {
        $updateqry['domain'] = $domain;
    }
    if( $firstpaymentamount )
    {
        $updateqry['firstpaymentamount'] = $firstpaymentamount;
    }
    if( $recurringamount )
    {
        $updateqry['amount'] = $recurringamount;
    }
    if( $billingcycle )
    {
        $updateqry['billingcycle'] = $billingcycle;
    }
    if( $status )
    {
        $updateqry['domainstatus'] = $status;
    }
    if( $serviceusername )
    {
        $updateqry['username'] = $serviceusername;
    }
    if( $servicepassword )
    {
        $updateqry['password'] = encrypt($servicepassword);
    }
    if( $subscriptionid )
    {
        $updateqry['subscriptionid'] = $subscriptionid;
    }
    if( $paymentmethod )
    {
        $updateqry['paymentmethod'] = $paymentmethod;
    }
    if( $promoid )
    {
        $updateqry['promoid'] = $promoid;
    }
    if( $overideautosuspend == 'on' )
    {
        $updateqry['overideautosuspend'] = 'on';
    }
    else
    {
        if( $overideautosuspend == 'off' )
        {
            $updateqry['overideautosuspend'] = '';
        }
    }
    if( $overidesuspenduntil )
    {
        $updateqry['overidesuspenduntil'] = $overidesuspenduntil;
    }
    if( $ns1 )
    {
        $updateqry['ns1'] = $ns1;
    }
    if( $ns2 )
    {
        $updateqry['ns2'] = $ns2;
    }
    if( $dedicatedip )
    {
        $updateqry['dedicatedip'] = $dedicatedip;
    }
    if( $assignedips )
    {
        $updateqry['assignedips'] = $assignedips;
    }
    if( $notes )
    {
        $updateqry['notes'] = $notes;
    }
    if( $diskusage )
    {
        $updateqry['diskusage'] = $diskusage;
    }
    if( $disklimit )
    {
        $updateqry['disklimit'] = $disklimit;
    }
    if( $bwusage )
    {
        $updateqry['bwusage'] = $bwusage;
    }
    if( $bwlimit )
    {
        $updateqry['bwlimit'] = $bwlimit;
    }
    if( $lastupdate )
    {
        $updateqry['lastupdate'] = $lastupdate;
    }
    if( $suspendreason )
    {
        $updateqry['suspendreason'] = $suspendreason;
    }
    if( 0 < count($updateqry) )
    {
        update_query('tblhosting', $updateqry, array( 'id' => $serviceid ));
    }
    if( $customfields )
    {
        if( !is_array($customfields) )
        {
            $customfields = base64_decode($customfields);
            $customfields = safe_unserialize($customfields);
        }
        saveCustomFields($serviceid, $customfields, 'product');
    }
    if( $configoptions )
    {
        if( !is_array($configoptions) )
        {
            $configoptions = base64_decode($configoptions);
            $configoptions = safe_unserialize($configoptions);
        }
        foreach( $configoptions as $cid => $vals )
        {
            if( is_array($vals) )
            {
                $oid = $vals['optionid'];
                $qty = $vals['qty'];
            }
            else
            {
                $oid = $vals;
                $qty = 0;
            }
            if( get_query_val('tblhostingconfigoptions', "COUNT(*)", array( 'relid' => $serviceid, 'configid' => $cid )) )
            {
                update_query('tblhostingconfigoptions', array( 'optionid' => $oid, 'qty' => $qty ), array( 'relid' => $serviceid, 'configid' => $cid ));
            }
            else
            {
                insert_query('tblhostingconfigoptions', array( 'relid' => $serviceid, 'configid' => $cid, 'optionid' => $oid, 'qty' => $qty ));
            }
        }
    }
    if( $autorecalc )
    {
        if( !$pid )
        {
            $pid = $data['packageid'];
        }
        if( !$billingcycle )
        {
            $billingcycle = $data['billingcycle'];
        }
        if( !$promoid )
        {
            $promoid = $data['promoid'];
        }
        $recurringamount = recalcRecurringProductPrice($serviceid, '', $pid, $billingcycle, 'empty', $promoid);
        update_query('tblhosting', array( 'amount' => $recurringamount ), array( 'id' => $serviceid ));
    }
    $apiresults = array( 'result' => 'success', 'serviceid' => $serviceid );
}