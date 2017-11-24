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
$query = " FROM tblorders o\n    LEFT JOIN tblclients c ON o.userid=c.id\n    LEFT JOIN tblpaymentgateways p ON o.paymentmethod=p.gateway AND p.setting='name'\n    LEFT JOIN tblinvoices i ON o.invoiceid=i.id";
$where = array(  );
if( $id )
{
    $where[] = "o.id=" . mysql_real_escape_string($id);
}
if( $userid )
{
    $where[] = "o.userid=" . mysql_real_escape_string($userid);
}
if( $status )
{
    $where[] = "o.status='" . mysql_real_escape_string($status) . "'";
}
if( count($where) )
{
    $query .= " WHERE " . implode(" AND ", $where);
}
$result_count = full_query("SELECT COUNT(o.id)" . $query);
$data = mysql_fetch_array($result_count);
$totalresults = $data[0];
$result = full_query("SELECT o.*, p.value AS paymentmethodname, i.status AS paymentstatus, CONCAT(c.firstname,' ',c.lastname) AS name" . $query . " ORDER BY o.id DESC LIMIT " . (int) $limitstart . ',' . (int) $limitnum);
$apiresults = array( 'result' => 'success', 'totalresults' => $totalresults, 'startnumber' => $limitstart, 'numreturned' => mysql_num_rows($result) );
while( $orderdata = mysql_fetch_assoc($result) )
{
    $orderid = $orderdata['id'];
    $userid = $orderdata['userid'];
    $fraudmodule = $orderdata['fraudmodule'];
    $fraudoutput = $orderdata['fraudoutput'];
    $currency = getCurrency($userid);
    $orderdata['currencyprefix'] = $currency['prefix'];
    $orderdata['currencysuffix'] = $currency['suffix'];
    $frauddata = '';
    if( $fraudmodule )
    {
        $fraud = new WHMCS_Module_Fraud();
        if( $fraud->load($fraudmodule) )
        {
            $fraudresults = $fraud->processResultsForDisplay($orderid, $fraudoutput);
            if( is_array($fraudresults) )
            {
                foreach( $fraudresults as $key => $value )
                {
                    $frauddata .= $key . " => " . $value . "\n";
                }
            }
        }
    }
    $orderdata['fraudoutput'] = $fraudoutput;
    $orderdata['frauddata'] = $frauddata;
    $lineitems = array(  );
    $result2 = select_query('tblhosting', '', array( 'orderid' => $orderid ));
    while( $data = mysql_fetch_array($result2) )
    {
        $serviceid = $data['id'];
        $domain = $data['domain'];
        $billingcycle = $data['billingcycle'];
        $hostingstatus = $data['domainstatus'];
        $firstpaymentamount = formatCurrency($data['firstpaymentamount']);
        $packageid = $data['packageid'];
        $result3 = select_query('tblproducts', "tblproducts.name,tblproducts.type,tblproducts.welcomeemail,tblproducts.autosetup,tblproducts.servertype,tblproductgroups.name AS groupname", array( "tblproducts.id" => $packageid ), '', '', '', "tblproductgroups ON tblproducts.gid=tblproductgroups.id");
        $data = mysql_fetch_array($result3);
        $groupname = $data['groupname'];
        $productname = $data['name'];
        $producttype = $data['type'];
        if( $producttype == 'hostingaccount' )
        {
            $producttype = "Hosting Account";
        }
        else
        {
            if( $producttype == 'reselleraccount' )
            {
                $producttype = "Reseller Account";
            }
            else
            {
                if( $producttype == 'server' )
                {
                    $producttype = "Dedicated/VPS Server";
                }
                else
                {
                    if( $producttype == 'other' )
                    {
                        $producttype = "Other Product/Service";
                    }
                }
            }
        }
        $lineitems['lineitem'][] = array( 'type' => 'product', 'relid' => $serviceid, 'producttype' => $producttype, 'product' => $groupname . " - " . $productname, 'domain' => $domain, 'billingcycle' => $billingcycle, 'amount' => $firstpaymentamount, 'status' => $hostingstatus );
    }
    $predefinedaddons = array(  );
    $result2 = select_query('tbladdons', '', '');
    while( $data = mysql_fetch_array($result2) )
    {
        $addon_id = $data['id'];
        $addon_name = $data['name'];
        $addon_welcomeemail = $data['welcomeemail'];
        $predefinedaddons[$addon_id] = array( 'name' => $addon_name, 'welcomeemail' => $addon_welcomeemail );
    }
    $result2 = select_query('tblhostingaddons', '', array( 'orderid' => $orderid ));
    while( $data = mysql_fetch_array($result2) )
    {
        $aid = $data['id'];
        $hostingid = $data['hostingid'];
        $addonid = $data['addonid'];
        $name = $data['name'];
        $billingcycle = $data['billingcycle'];
        $addonamount = $data['recurring'] + $data['setupfee'];
        $addonstatus = $data['status'];
        $regdate = $data['regdate'];
        $nextduedate = $data['nextduedate'];
        $addonamount = formatCurrency($addonamount);
        if( !$name )
        {
            $name = $predefinedaddons[$addonid]['name'];
        }
        $lineitems['lineitem'][] = array( 'type' => 'addon', 'relid' => $aid, 'producttype' => 'Addon', 'product' => $name, 'domain' => '', 'billingcycle' => $billingcycle, 'amount' => $addonamount, 'status' => $addonstatus );
    }
    $result2 = select_query('tbldomains', '', array( 'orderid' => $orderid ));
    while( $data = mysql_fetch_array($result2) )
    {
        $domainid = $data['id'];
        $type = $data['type'];
        $domain = $data['domain'];
        $registrationperiod = $data['registrationperiod'];
        $status = $data['status'];
        $regdate = $data['registrationdate'];
        $nextduedate = $data['nextduedate'];
        $domainamount = formatCurrency($data['firstpaymentamount']);
        $domainregistrar = $data['registrar'];
        $dnsmanagement = $data['dnsmanagement'];
        $emailforwarding = $data['emailforwarding'];
        $idprotection = $data['idprotection'];
        $lineitems['lineitem'][] = array( 'type' => 'domain', 'relid' => $domainid, 'producttype' => 'Domain', 'product' => $type, 'domain' => $domain, 'billingcycle' => $registrationperiod, 'amount' => $domainamount, 'status' => $status, 'dnsmanagement' => $dnsmanagement, 'emailforwarding' => $emailforwarding, 'idprotection' => $idprotection );
    }
    $renewals = $orderdata['renewals'];
    if( $renewals )
    {
        $renewals = explode(',', $renewals);
        foreach( $renewals as $renewal )
        {
            $renewal = explode("=", $renewal);
            $domainid = $renewal[0];
            $registrationperiod = $renewal[1];
            $renewalResult = select_query('tbldomains', '', array( 'id' => $domainid ));
            $data = mysql_fetch_array($renewalResult);
            $domainid = $data['id'];
            $type = $data['type'];
            $domain = $data['domain'];
            $registrar = $data['registrar'];
            $status = $data['status'];
            $regdate = $data['registrationdate'];
            $nextduedate = $data['nextduedate'];
            $domainamount = formatCurrency($data['recurringamount']);
            $domainregistrar = $data['registrar'];
            $dnsmanagement = $data['dnsmanagement'];
            $emailforwarding = $data['emailforwarding'];
            $idprotection = $data['idprotection'];
            $lineitems['lineitem'][] = array( 'type' => 'renewal', 'relid' => $domainid, 'producttype' => 'Domain', 'product' => 'Renewal', 'domain' => $domain, 'billingcycle' => $registrationperiod, 'amount' => $domainamount, 'status' => $status, 'dnsmanagement' => $dnsmanagement, 'emailforwarding' => $emailforwarding, 'idprotection' => $idprotection );
        }
    }
    $result2 = select_query('tblupgrades', '', array( 'orderid' => $orderid ));
    while( $data = mysql_fetch_array($result2) )
    {
        $upgradeid = $data['id'];
        $type = $data['type'];
        $relid = $data['relid'];
        $originalvalue = $data['originalvalue'];
        $newvalue = $data['newvalue'];
        $upgradeamount = formatCurrency($data['amount']);
        $newrecurringamount = $data['newrecurringamount'];
        $status = $data['status'];
        $paid = $data['paid'];
        if( $type == 'package' )
        {
            $result2 = select_query('tblproducts', 'name', array( 'id' => $originalvalue ));
            $data = mysql_fetch_array($result2);
            $oldpackagename = $data['name'];
            $newvalue = explode(',', $newvalue);
            $newpackageid = $newvalue[0];
            $result2 = select_query('tblproducts', 'name', array( 'id' => $newpackageid ));
            $data = mysql_fetch_array($result2);
            $newpackagename = $data['name'];
            $details = "Package Upgrade: " . $oldpackagename . " => " . $newpackagename . "<br>";
        }
        else
        {
            if( $type == 'configoptions' )
            {
                $tempvalue = explode("=>", $originalvalue);
                $configid = $tempvalue[0];
                $oldoptionid = $tempvalue[1];
                $result2 = select_query('tblproductconfigoptions', '', array( 'id' => $configid ));
                $data = mysql_fetch_array($result2);
                $configname = $data['optionname'];
                $optiontype = $data['optiontype'];
                if( $optiontype == 1 || $optiontype == 2 )
                {
                    $result2 = select_query('tblproductconfigoptionssub', '', array( 'id' => $oldoptionid ));
                    $data = mysql_fetch_array($result2);
                    $oldoptionname = $data['optionname'];
                    $result2 = select_query('tblproductconfigoptionssub', '', array( 'id' => $newvalue ));
                    $data = mysql_fetch_array($result2);
                    $newoptionname = $data['optionname'];
                }
                else
                {
                    if( $optiontype == 3 )
                    {
                        if( $oldoptionid )
                        {
                            $oldoptionname = 'Yes';
                            $newoptionname = 'No';
                        }
                        else
                        {
                            $oldoptionname = 'No';
                            $newoptionname = 'Yes';
                        }
                    }
                    else
                    {
                        if( $optiontype == 4 )
                        {
                            $result2 = select_query('tblproductconfigoptionssub', '', array( 'configid' => $configid ));
                            $data = mysql_fetch_array($result2);
                            $optionname = $data['optionname'];
                            $oldoptionname = $oldoptionid;
                            $newoptionname = $newvalue . " x " . $optionname;
                        }
                    }
                }
                $details = $configname . ": " . $oldoptionname . " => " . $newoptionname . "<br>";
            }
        }
        $lineitems['lineitem'][] = array( 'type' => 'upgrade', 'relid' => $relid, 'producttype' => 'Upgrade', 'product' => $details, 'domain' => '', 'billingcycle' => '', 'amount' => $upgradeamount, 'status' => $status );
    }
    $apiresults['orders']['order'][] = array_merge($orderdata, array( 'lineitems' => $lineitems ));
}
$responsetype = 'xml';