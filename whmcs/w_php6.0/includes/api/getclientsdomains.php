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
    $where["tbldomains.userid"] = $clientid;
}
if( $domainid )
{
    $where["tbldomains.id"] = $domainid;
}
if( $domain )
{
    $where["tbldomains.domain"] = $domain;
}
$result = select_query('tbldomains', "COUNT(*)", $where);
$data = mysql_fetch_array($result);
$totalresults = $data[0];
$limitstart = (int) $limitstart;
$limitnum = (int) $limitnum;
if( !$limitnum )
{
    $limitnum = 25;
}
$result = select_query('tbldomains', "tbldomains.*,(SELECT tblpaymentgateways.value FROM tblpaymentgateways WHERE tblpaymentgateways.gateway=tbldomains.paymentmethod AND tblpaymentgateways.setting='name' LIMIT 1) AS paymentmethodname", $where, "tbldomains`.`id", 'ASC', $limitstart . ',' . $limitnum);
$apiresults = array( 'result' => 'success', 'clientid' => $clientid, 'domainid' => $domainid, 'totalresults' => $totalresults, 'startnumber' => $limitstart, 'numreturned' => mysql_num_rows($result) );
if( !$totalresults )
{
    $apiresults['domains'] = '';
}
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $userid = $data['userid'];
    $orderid = $data['orderid'];
    $type = $data['type'];
    $registrationdate = $data['registrationdate'];
    $domain = $data['domain'];
    $firstpaymentamount = $data['firstpaymentamount'];
    $recurringamount = $data['recurringamount'];
    $registrar = $data['registrar'];
    $registrationperiod = $data['registrationperiod'];
    $expirydate = $data['expirydate'];
    $nextduedate = $data['nextduedate'];
    $status = $data['status'];
    $subscriptionid = $data['subscriptionid'];
    $additionalnotes = $data['additionalnotes'];
    $paymentmethod = $data['paymentmethod'];
    $paymentmethodname = $data['paymentmethodname'];
    $dnsmanagement = $data['dnsmanagement'];
    $emailforwarding = $data['emailforwarding'];
    $idprotection = $data['idprotection'];
    $donotrenew = $data['donotrenew'];
    $nameservers = array(  );
    if( $getnameservers )
    {
        if( !function_exists('RegGetNameservers') )
        {
            require(ROOTDIR . "/includes/registrarfunctions.php");
        }
        $domainparts = explode(".", $domain, 2);
        $params = array(  );
        $params['domainid'] = $id;
        $params['sld'] = $domainparts[0];
        $params['tld'] = $domainparts[1];
        $params['regperiod'] = $registrationperiod;
        $params['registrar'] = $registrar;
        $nameservers = RegGetNameservers($params);
        $nameservers['nameservers'] = true;
    }
    $apiresults['domains']['domain'][] = array_merge(array( 'id' => $id, 'userid' => $userid, 'orderid' => $orderid, 'regtype' => $type, 'domainname' => $domain, 'registrar' => $registrar, 'regperiod' => $registrationperiod, 'firstpaymentamount' => $firstpaymentamount, 'recurringamount' => $recurringamount, 'paymentmethod' => $paymentmethod, 'paymentmethodname' => $paymentmethodname, 'regdate' => $registrationdate, 'expirydate' => $expirydate, 'nextduedate' => $nextduedate, 'status' => $status, 'subscriptionid' => $subscriptionid, 'dnsmanagement' => $dnsmanagement, 'emailforwarding' => $emailforwarding, 'idprotection' => $idprotection, 'donotrenew' => $donotrenew, 'notes' => $additionalnotes ), $nameservers);
}
$responsetype = 'xml';