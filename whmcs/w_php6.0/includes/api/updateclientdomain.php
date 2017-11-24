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
if( !function_exists('getTLDPriceList') )
{
    require(ROOTDIR . "/includes/domainfunctions.php");
}
if( !function_exists('recalcPromoAmount') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
if( $domainid )
{
    $where = array( 'id' => $domainid );
}
else
{
    $where = array( 'domain' => $domain );
}
$result = select_query('tbldomains', 'id', $where);
$data = mysql_fetch_array($result);
$domainid = $data['id'];
if( !$domainid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Domain ID Not Found" );
}
else
{
    $whmcs = WHMCS_Application::getinstance();
    $dnsmanagement = $whmcs->get_req_var('dnsmanagement');
    $emailforwarding = $whmcs->get_req_var('emailforwarding');
    $idprotection = $whmcs->get_req_var('idprotection');
    $donotrenew = $whmcs->get_req_var('donotrenew');
    if( $type )
    {
        $updateqry['type'] = $type;
    }
    if( $regdate )
    {
        $updateqry['registrationdate'] = $regdate;
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
        $updateqry['recurringamount'] = $recurringamount;
    }
    if( $registrar )
    {
        $updateqry['registrar'] = $registrar;
    }
    if( $regperiod )
    {
        $updateqry['registrationperiod'] = $regperiod;
    }
    if( $expirydate )
    {
        $updateqry['expirydate'] = $expirydate;
    }
    if( $nextduedate )
    {
        $updateqry['nextduedate'] = $nextduedate;
        $updateqry['nextinvoicedate'] = $nextduedate;
    }
    if( $paymentmethod )
    {
        $updateqry['paymentmethod'] = $paymentmethod;
    }
    if( $subscriptionid )
    {
        $updateqry['subscriptionid'] = $subscriptionid;
    }
    if( $status )
    {
        $updateqry['status'] = $status;
    }
    if( $notes )
    {
        $updateqry['additionalnotes'] = $notes;
    }
    if( isset($_REQUEST['dnsmanagement']) )
    {
        $dnsmanagement = empty($dnsmanagement) ? '' : 'on';
        $updateqry['dnsmanagement'] = $dnsmanagement;
    }
    if( isset($_REQUEST['emailforwarding']) )
    {
        $emailforwarding = empty($emailforwarding) ? '' : 'on';
        $updateqry['emailforwarding'] = $emailforwarding;
    }
    if( isset($_REQUEST['idprotection']) )
    {
        $idprotection = empty($idprotection) ? '' : 'on';
        $updateqry['idprotection'] = $idprotection;
    }
    if( isset($_REQUEST['donotrenew']) )
    {
        $donotrenew = empty($donotrenew) ? '' : 'on';
        $updateqry['donotrenew'] = $donotrenew;
    }
    if( $promoid )
    {
        $updateqry['promoid'] = $promoid;
    }
    update_query('tbldomains', $updateqry, array( 'id' => $domainid ));
    if( $autorecalc )
    {
        $updateqry = '';
        if( $domainid )
        {
            $where = array( 'id' => $domainid );
        }
        else
        {
            $where = array( 'domain' => $domain );
        }
        $result = select_query('tbldomains', 'id,userid,domain,registrationperiod,dnsmanagement,emailforwarding,idprotection', $where);
        $data = mysql_fetch_assoc($result);
        $domainid = $data['id'];
        $domain = $data['domain'];
        $userid = $data['userid'];
        $regperiod = $data['registrationperiod'];
        $dnsmanagement = $data['dnsmanagement'];
        $emailforwarding = $data['emailforwarding'];
        $idprotection = $data['idprotection'];
        $domainparts = explode(".", $domain, 2);
        if( !isset($currency) )
        {
            if( !function_exists('getCurrency') )
            {
                require(ROOTDIR . "/includes/functions.php");
            }
            $currency = getCurrency($userid);
        }
        $temppricelist = getTLDPriceList("." . $domainparts[1], '', true, $userid);
        $recurringamount = $temppricelist[$regperiod]['renew'];
        $result = select_query('tblpricing', 'msetupfee,qsetupfee,ssetupfee', array( 'type' => 'domainaddons', 'currency' => $currency['id'], 'relid' => 0 ));
        $data = mysql_fetch_array($result);
        $domaindnsmanagementprice = $data['msetupfee'] * $regperiod;
        $domainemailforwardingprice = $data['qsetupfee'] * $regperiod;
        $domainidprotectionprice = $data['ssetupfee'] * $regperiod;
        if( $dnsmanagement )
        {
            $recurringamount += $domaindnsmanagementprice;
        }
        if( $emailforwarding )
        {
            $recurringamount += $domainemailforwardingprice;
        }
        if( $idprotection )
        {
            $recurringamount += $domainidprotectionprice;
        }
        if( $promoid )
        {
            $recurringamount -= recalcPromoAmount("D." . $domainparts[1], $userid, $domainid, $regperiod . 'Years', $recurringamount, $promoid);
        }
        $updateqry['recurringamount'] = $recurringamount;
        update_query('tbldomains', $updateqry, array( 'id' => $domainid ));
    }
    $apiresults = array( 'result' => 'success', 'domainid' => $domainid );
    if( $updatens )
    {
        if( !function_exists('RegSaveNameservers') )
        {
            require(ROOTDIR . "/includes/registrarfunctions.php");
        }
        if( $domainid )
        {
            $where = array( 'id' => $domainid );
        }
        else
        {
            $where = array( 'domain' => $domain );
        }
        $result = select_query('tbldomains', 'id,domain,registrar,registrationperiod', $where);
        if( !($ns1 && $ns2) )
        {
            $apiresults = array( 'result' => 'error', 'message' => "ns1 and ns2 required" );
            return false;
        }
        $domain = $data['domain'];
        $registrar = $data['registrar'];
        $regperiod = $data['registrationperiod'];
        $domainparts = explode(".", $domain, 2);
        $params = array(  );
        $params['domainid'] = $domainid;
        $params['sld'] = $domainparts[0];
        $params['tld'] = $domainparts[1];
        $params['regperiod'] = $regperiod;
        $params['registrar'] = $registrar;
        $params['ns1'] = $ns1;
        $params['ns2'] = $ns2;
        $params['ns3'] = $ns3;
        $params['ns4'] = $ns4;
        $params['ns5'] = $ns5;
        $values = RegSaveNameservers($params);
        if( $values['error'] )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Registrar Error Message", 'error' => $values['error'] );
            return false;
        }
    }
}