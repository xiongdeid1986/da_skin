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
if( !function_exists('getCustomFields') )
{
    require(ROOTDIR . "/includes/customfieldfunctions.php");
}
if( !function_exists('getCartConfigOptions') )
{
    require(ROOTDIR . "/includes/configoptionsfunctions.php");
}
$where = array(  );
if( $clientid )
{
    $where["tblhosting.userid"] = $clientid;
}
if( $serviceid )
{
    $where["tblhosting.id"] = $serviceid;
}
if( $pid )
{
    $where["tblhosting.packageid"] = $pid;
}
if( $domain )
{
    $where["tblhosting.domain"] = $domain;
}
if( $username2 )
{
    $where["tblhosting.username"] = $username2;
}
$result = select_query('tblhosting', "COUNT(*)", $where, '', '', '', "tblproducts ON tblproducts.id=tblhosting.packageid INNER JOIN tblproductgroups ON tblproductgroups.id=tblproducts.gid");
$data = mysql_fetch_array($result);
$totalresults = $data[0];
$limitstart = (int) $limitstart;
$limitnum = (int) $limitnum;
if( !$limitnum )
{
    $limitnum = 999999;
}
$result = select_query('tblhosting', "tblhosting.*,tblproducts.name AS productname,tblproductgroups.name AS groupname,(SELECT CONCAT(name,'|',ipaddress,'|',hostname) FROM tblservers WHERE tblservers.id=tblhosting.server) AS serverdetails,(SELECT tblpaymentgateways.value FROM tblpaymentgateways WHERE tblpaymentgateways.gateway=tblhosting.paymentmethod AND tblpaymentgateways.setting='name' LIMIT 1) AS paymentmethodname", $where, "tblhosting`.`id", 'ASC', $limitstart . ',' . $limitnum, "tblproducts ON tblproducts.id=tblhosting.packageid INNER JOIN tblproductgroups ON tblproductgroups.id=tblproducts.gid");
$apiresults = array( 'result' => 'success', 'clientid' => $clientid, 'serviceid' => $serviceid, 'pid' => $pid, 'domain' => $domain, 'totalresults' => $totalresults, 'startnumber' => $limitstart, 'numreturned' => mysql_num_rows($result) );
if( !$totalresults )
{
    $apiresults['products'] = '';
}
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $userid = $data['userid'];
    $orderid = $data['orderid'];
    $pid = $data['packageid'];
    $name = $data['productname'];
    $groupname = $data['groupname'];
    $server = $data['server'];
    $regdate = $data['regdate'];
    $domain = $data['domain'];
    $paymentmethod = $data['paymentmethod'];
    $paymentmethodname = $data['paymentmethodname'];
    $firstpaymentamount = $data['firstpaymentamount'];
    $recurringamount = $data['amount'];
    $billingcycle = $data['billingcycle'];
    $nextduedate = $data['nextduedate'];
    $domainstatus = $data['domainstatus'];
    $username = $data['username'];
    $password = decrypt($data['password']);
    $notes = $data['notes'];
    $subscriptionid = $data['subscriptionid'];
    $promoid = $data['promoid'];
    $ipaddress = $data['ipaddress'];
    $overideautosuspend = $data['overideautosuspend'];
    $overidesuspenduntil = $data['overidesuspenduntil'];
    $ns1 = $data['ns1'];
    $ns2 = $data['ns2'];
    $dedicatedip = $data['dedicatedip'];
    $assignedips = $data['assignedips'];
    $diskusage = $data['diskusage'];
    $disklimit = $data['disklimit'];
    $bwusage = $data['bwusage'];
    $bwlimit = $data['bwlimit'];
    $lastupdate = $data['lastupdate'];
    $serverdetails = $data['serverdetails'];
    $serverdetails = explode("|", $serverdetails);
    $customfieldsdata = array(  );
    $customfields = getCustomFields('product', $pid, $id, 'on', '');
    foreach( $customfields as $customfield )
    {
        $customfieldsdata[] = array( 'id' => $customfield['id'], 'name' => $customfield['name'], 'value' => $customfield['value'] );
    }
    $configoptionsdata = array(  );
    $currency = getCurrency($userid);
    $configoptions = getCartConfigOptions($pid, '', $billingcycle, $id);
    foreach( $configoptions as $configoption )
    {
        switch( $configoption['optiontype'] )
        {
            case 1:
                $type = 'dropdown';
                break;
            case 2:
                $type = 'radio';
                break;
            case 3:
                $type = 'yesno';
                break;
            case 4:
                $type = 'quantity';
        }
        if( $configoption['optiontype'] == '3' || $configoption['optiontype'] == '4' )
        {
            $configoptionsdata[] = array( 'id' => $configoption['id'], 'option' => $configoption['optionname'], 'type' => $type, 'value' => $configoption['selectedqty'] );
        }
        else
        {
            $configoptionsdata[] = array( 'id' => $configoption['id'], 'option' => $configoption['optionname'], 'type' => $type, 'value' => $configoption['selectedoption'] );
        }
        break;
    }
    $apiresults['products']['product'][] = array( 'id' => $id, 'clientid' => $userid, 'orderid' => $orderid, 'pid' => $pid, 'regdate' => $regdate, 'name' => $name, 'groupname' => $groupname, 'domain' => $domain, 'dedicatedip' => $dedicatedip, 'serverid' => $server, 'servername' => $serverdetails[0], 'serverip' => $serverdetails[1], 'serverhostname' => $serverdetails[2], 'firstpaymentamount' => $firstpaymentamount, 'recurringamount' => $recurringamount, 'paymentmethod' => $paymentmethod, 'paymentmethodname' => $paymentmethodname, 'billingcycle' => $billingcycle, 'nextduedate' => $nextduedate, 'status' => $domainstatus, 'username' => $username, 'password' => $password, 'subscriptionid' => $subscriptionid, 'promoid' => $promoid, 'overideautosuspend' => $overideautosuspend, 'overidesuspenduntil' => $overidesuspenduntil, 'ns1' => $ns1, 'ns2' => $ns2, 'dedicatedip' => $dedicatedip, 'assignedips' => $assignedips, 'notes' => $notes, 'diskusage' => $diskusage, 'disklimit' => $disklimit, 'bwusage' => $bwusage, 'bwlimit' => $bwlimit, 'lastupdate' => $lastupdate, 'customfields' => array( 'customfield' => $customfieldsdata ), 'configoptions' => array( 'configoption' => $configoptionsdata ) );
}
$responsetype = 'xml';